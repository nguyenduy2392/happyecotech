// Chỉ JS cần thiết. FAQ, dropdown là HTML/CSS thuần.
import './analytics.js'; // đo lường (no-op khi chưa cấu hình GTM/GA4)

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

// ---- Menu mobile ----
const toggle = document.querySelector('[data-nav-toggle]');
const panel = document.querySelector('[data-nav-panel]');

if (toggle && panel) {
    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', String(open));
        panel.classList.toggle('is-open', open);
    };

    panel.querySelectorAll('a').forEach((a, i) => a.style.setProperty('--i', i));
    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            toggle.focus();
        }
    });
    panel.addEventListener('click', (e) => {
        if (e.target.closest('a')) setOpen(false);
    });
}

// ---- Nút lên đầu trang (ẩn ở đầu trang; position: fixed nên không gây CLS) ----
const topBtn = document.querySelector('[data-back-to-top]');
if (topBtn) {
    let ticking = false;
    const update = () => {
        ticking = false;
        const show = window.scrollY > 600;
        topBtn.classList.toggle('is-visible', show);
        topBtn.tabIndex = show ? 0 : -1;
        topBtn.setAttribute('aria-hidden', String(!show));
    };
    window.addEventListener('scroll', () => {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(update);
        }
    }, { passive: true });
    update();
    topBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
        document.getElementById('main')?.focus({ preventScroll: true });
    });
}

// ---- Form gửi tại chỗ (lead-form + trang liên hệ) ----
// Không bao giờ submit() thường sau khi đã gửi fetch (tránh lead trùng): lỗi mạng/5xx -> giữ dữ liệu + nút "Thử lại" dùng CÙNG idempotency_key.
// Chỉ để trình duyệt submit thường khi fetch/FormData không khả dụng.
const uuid = () => (crypto.randomUUID ? crypto.randomUUID()
    : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        return (c === 'x' ? r : (r & 0x3) | 0x8).toString(16);
    }));

const clearErrors = (form) => {
    form.querySelectorAll('[data-js-error]').forEach((el) => el.remove());
    form.querySelectorAll('[data-js-invalid]').forEach((el) => {
        el.removeAttribute('aria-invalid');
        el.removeAttribute('data-js-invalid');
        const prev = el.dataset.prevDescribedby;
        if (prev) el.setAttribute('aria-describedby', prev); else el.removeAttribute('aria-describedby');
        delete el.dataset.prevDescribedby;
    });
};

const showStatus = (form, kind, text, { focus = false, retry = null } = {}) => {
    const box = form.querySelector('[data-form-status]');
    if (!box) return;
    box.textContent = text;
    box.className = kind === 'ok'
        ? 'rounded-lg border border-brand-200 bg-brand-50 p-4 font-semibold text-brand-900'
        : 'rounded-lg border border-danger-600 bg-danger-50 p-4 font-semibold text-danger-600';
    box.setAttribute('role', kind === 'ok' ? 'status' : 'alert');
    let btn = null;
    if (retry) {
        btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline mt-3 block';
        btn.textContent = 'Thử lại';
        btn.addEventListener('click', retry);
        box.appendChild(btn);
    }
    if (btn) btn.focus(); else if (focus) box.focus();
};

const showFieldErrors = (form, errors) => {
    Object.entries(errors).forEach(([name, messages]) => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field) return;
        const id = `${form.dataset.formId}-${name}-js-err`;
        const p = document.createElement('p');
        p.id = id;
        p.className = 'field-error';
        p.dataset.jsError = '';
        p.textContent = [].concat(messages)[0];
        (field.closest('.field') || field.parentElement).appendChild(p);
        const prev = field.getAttribute('aria-describedby');
        if (prev) field.dataset.prevDescribedby = prev;
        field.setAttribute('aria-invalid', 'true');
        field.dataset.jsInvalid = '';
        field.setAttribute('aria-describedby', prev ? `${prev} ${id}` : id);
    });
};

// Token CSRF: 1 promise dùng chung cho focusin + submit; force=true để làm mới (khi 419 / session đổi).
const tokenFor = (form, force = false) => {
    const input = form.querySelector('input[name="_token"]');
    const endpoint = form.dataset.csrfEndpoint || input?.dataset.csrfFrom;
    if (!input || !endpoint) return Promise.resolve();
    if (!force && input.value) return Promise.resolve();
    if (!force && form._tokenPromise) return form._tokenPromise;
    form._tokenPromise = fetch(endpoint, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } })
        .then((r) => { if (!r.ok) throw new Error('token'); return r.json(); })
        .then((d) => { input.value = d.token || ''; })
        .finally(() => { form._tokenPromise = null; });
    return form._tokenPromise;
};

document.querySelectorAll('form[data-ajax-form]').forEach((form, i) => {
    form.dataset.formId = form.dataset.formId || `ajax${i}`;
    let keyInput = form.querySelector('input[name="idempotency_key"]');
    if (!keyInput) {
        keyInput = document.createElement('input');
        keyInput.type = 'hidden';
        keyInput.name = 'idempotency_key';
        form.appendChild(keyInput);
    }
    // 1 key cho mỗi lần điền form; giữ nguyên khi thử lại, đổi mới sau khi gửi thành công.
    keyInput.value = keyInput.value || uuid();

    form.addEventListener('focusin', () => { tokenFor(form).catch(() => {}); }, { once: true });

    const post = () => fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });

    const send = async () => {
        const btn = form.querySelector('button[type="submit"]');
        const label = btn?.dataset.label || btn?.textContent;
        if (btn) { btn.dataset.label = label; btn.disabled = true; btn.textContent = 'Đang gửi...'; }
        clearErrors(form);
        const retry = () => { send(); };
        try {
            await tokenFor(form);
            let res = await post();
            if (res.status === 419) {
                // Token hết hạn/đổi session: làm mới và thử lại đúng 1 lần.
                await tokenFor(form, true);
                res = await post();
            }
            const isJson = (res.headers.get('content-type') || '').includes('json');
            const data = isJson ? await res.json().catch(() => null) : null;

            if (res.status === 422 && data?.errors) {
                showFieldErrors(form, data.errors);
                showStatus(form, 'error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
                form.querySelector('[data-js-invalid]')?.focus();
            } else if (res.status === 429) {
                showStatus(form, 'error', 'Bạn thao tác hơi nhanh. Vui lòng thử lại sau ít phút hoặc gọi hotline.');
            } else if (res.status === 419) {
                showStatus(form, 'error', 'Phiên làm việc đã hết hạn và chưa làm mới được. Dữ liệu của bạn vẫn được giữ.', { retry });
            } else if (res.ok && (data?.ok || !isJson)) {
                // Đo lường: chỉ nhãn nguồn + slug giải pháp, KHÔNG đọc tên/SĐT/email.
                const sel = form.querySelector('select[name="service_id"]');
                document.dispatchEvent(new CustomEvent('he:lead', { detail: {
                    source: form.querySelector('input[name="source"]')?.value,
                    solution: sel?.value ? (sel.selectedOptions[0]?.dataset.slug || 'other') : 'none', // slug ổn định, không dùng tên hiển thị
                } }));
                form.reset();
                keyInput.value = uuid();
                showStatus(form, 'ok', data?.message || 'Cảm ơn bạn. Chúng tôi đã nhận được thông tin và sẽ liên hệ lại sớm.', { focus: true });
            } else {
                showStatus(form, 'error', 'Chưa xác nhận được việc gửi. Dữ liệu của bạn vẫn được giữ, hãy bấm Thử lại (sẽ không tạo yêu cầu trùng).', { retry });
            }
        } catch (err) {
            showStatus(form, 'error', 'Chưa xác nhận được việc gửi (lỗi kết nối). Dữ liệu của bạn vẫn được giữ, hãy bấm Thử lại (sẽ không tạo yêu cầu trùng).', { retry });
        } finally {
            if (btn) { btn.disabled = false; btn.textContent = label; }
        }
    };

    form.addEventListener('submit', (e) => {
        if (!window.fetch || !window.FormData) return; // để trình duyệt submit thường
        e.preventDefault();
        send();
    });
});

// ---- Lightbox ảnh màn hình: <a data-lightbox> mở <dialog>; tắt JS thì link mở ảnh gốc ----
const lightbox = document.querySelector('[data-lightbox-dialog]');
if (lightbox && lightbox.showModal) {
    const big = lightbox.querySelector('img');
    document.querySelectorAll('a[data-lightbox]').forEach((a) => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            const thumb = a.querySelector('img');
            big.src = a.href;
            big.alt = thumb?.alt || '';
            lightbox.showModal();
        });
    });
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) lightbox.close(); });
    lightbox.addEventListener('close', () => { big.removeAttribute('src'); });
}

// ---- /lien-he?solution=<slug>: khi người dùng đổi lựa chọn giải pháp thì bỏ `solution` cũ để không lấn service_id ----
document.querySelectorAll('select[data-solution-select]').forEach((sel) => {
    sel.addEventListener('change', () => {
        sel.form.querySelectorAll('[data-solution], [data-solution-note]').forEach((el) => el.remove());
    });
});

// ---- Hiện dần khi cuộn + tagline từng từ: IntersectionObserver (không lắng nghe scroll), 1 lần/phần tử ----
const motionOk = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if ('IntersectionObserver' in window && motionOk) {
    const sections = [...document.querySelectorAll('main > section')].slice(1); // bỏ phần đầu (hero) để không trễ LCP
    sections.forEach((el) => el.setAttribute('data-reveal', ''));
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('is-in');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -6% 0px' });
    sections.forEach((el) => {
        const r = el.getBoundingClientRect();
        if (r.top < window.innerHeight) el.classList.add('is-in'); else io.observe(el);
    });

    const words = [...document.querySelectorAll('.tagline-text .tw')];
    if (words.length) {
        let batch = 0;
        const wio = new IntersectionObserver((entries) => {
            const hit = entries.filter((e) => e.isIntersecting).sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top || a.boundingClientRect.left - b.boundingClientRect.left);
            hit.forEach((e, i) => {
                e.target.style.transitionDelay = `${Math.min(i * 40, 400)}ms`;
                e.target.classList.add('on');
                wio.unobserve(e.target);
            });
            batch += 1;
        }, { rootMargin: '0px 0px -35% 0px', threshold: 0 });
        words.forEach((w) => {
            const r = w.getBoundingClientRect();
            if (r.top < window.innerHeight * 0.65 && r.bottom > 0) w.classList.add('on'); else wio.observe(w);
        });
    }
    document.documentElement.classList.add('reveal-ready');
}
