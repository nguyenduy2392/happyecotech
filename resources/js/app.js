// Chỉ JS cần thiết. FAQ, dropdown là HTML/CSS thuần.
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

// ---- Menu mobile ----
const toggle = document.querySelector('[data-nav-toggle]');
const panel = document.querySelector('[data-nav-panel]');

if (toggle && panel) {
    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', String(open));
        panel.classList.toggle('hidden', !open);
    };

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

// ---- Form gửi tại chỗ (lead-form + trang liên hệ). Lỗi JS/không có JSON -> submit thường. ----
const clearErrors = (form) => {
    form.querySelectorAll('[data-js-error]').forEach((el) => el.remove());
    form.querySelectorAll('[aria-invalid="true"]').forEach((el) => {
        el.removeAttribute('aria-invalid');
        const id = el.getAttribute('aria-describedby');
        if (id && id.endsWith('-js-err')) el.removeAttribute('aria-describedby');
    });
};

const showStatus = (form, kind, text) => {
    const box = form.querySelector('[data-form-status]');
    if (!box) return;
    box.textContent = text;
    box.className = kind === 'ok'
        ? 'rounded-lg border border-brand-200 bg-brand-50 p-4 font-semibold text-brand-900'
        : 'rounded-lg border border-danger-600 bg-danger-50 p-4 font-semibold text-danger-600';
    box.setAttribute('role', kind === 'ok' ? 'status' : 'alert');
    box.focus();
};

const showFieldErrors = (form, errors) => {
    Object.entries(errors).forEach(([name, messages]) => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field) return;
        const id = `${form.dataset.formId || 'f'}-${name}-js-err`;
        const p = document.createElement('p');
        p.id = id;
        p.className = 'field-error';
        p.dataset.jsError = '';
        p.textContent = [].concat(messages)[0];
        (field.closest('.field') || field.parentElement).appendChild(p);
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', id);
    });
};

const ensureToken = (form) => {
    const input = form.querySelector('input[data-csrf-from]');
    if (!input || input.value) return Promise.resolve();
    return fetch(input.dataset.csrfFrom, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
        .then((r) => r.json())
        .then((d) => { input.value = d.token || ''; });
};

document.querySelectorAll('form[data-ajax-form]').forEach((form, i) => {
    form.dataset.formId = form.dataset.formId || `ajax${i}`;
    form.addEventListener('focusin', () => { ensureToken(form).catch(() => {}); }, { once: true });

    form.addEventListener('submit', async (e) => {
        if (!window.fetch || !window.FormData) return;
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const label = btn?.textContent;
        clearErrors(form);
        if (btn) { btn.disabled = true; btn.textContent = 'Đang gửi...'; }
        let handled = false;
        try {
            await ensureToken(form);
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const isJson = (res.headers.get('content-type') || '').includes('json');
            const data = isJson ? await res.json().catch(() => null) : null;

            if (res.status === 429) {
                handled = true;
                showStatus(form, 'error', 'Bạn thao tác hơi nhanh. Vui lòng thử lại sau ít phút hoặc gọi hotline.');
            } else if (res.status === 422 && data?.errors) {
                handled = true;
                showFieldErrors(form, data.errors);
                showStatus(form, 'error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
                form.querySelector('[aria-invalid="true"]')?.focus({ preventScroll: true });
                form.querySelector('[data-form-status]')?.focus();
            } else if (res.ok && data?.ok) {
                handled = true;
                form.reset();
                showStatus(form, 'ok', data.message || 'Cảm ơn bạn. Chúng tôi đã nhận được thông tin và sẽ liên hệ lại sớm.');
            } else if (res.ok && res.redirected && !isJson) {
                // Backend chưa trả JSON: đọc kết quả từ trang HTML sau redirect (đã gửi 1 lần, không submit lại).
                handled = true;
                const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
                const invalid = [...doc.querySelectorAll('[aria-invalid="true"][aria-describedby]')];
                if (invalid.length) {
                    const errors = {};
                    invalid.forEach((el) => { errors[el.getAttribute('name')] = doc.getElementById(el.getAttribute('aria-describedby'))?.textContent || 'Chưa hợp lệ'; });
                    showFieldErrors(form, errors);
                    showStatus(form, 'error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
                    form.querySelector('[aria-invalid="true"]')?.focus({ preventScroll: true });
                    form.querySelector('[data-form-status]')?.focus();
                } else if (doc.querySelector('[role="status"]')) {
                    form.reset();
                    showStatus(form, 'ok', 'Cảm ơn bạn. Chúng tôi đã nhận được thông tin và sẽ liên hệ lại sớm.');
                } else {
                    showStatus(form, 'error', 'Chưa gửi được. Vui lòng thử lại hoặc gọi hotline.');
                }
            }
        } catch (err) {
            handled = false;
        }
        if (btn) { btn.disabled = false; btn.textContent = label; }
        // Không xử lý được (mạng/JS/không phải JSON): quay về submit thường (HTML redirect).
        if (!handled) {
            HTMLFormElement.prototype.submit.call(form);
        }
    });
});
