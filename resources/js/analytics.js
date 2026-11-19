// Đo lường chuyển đổi. Không làm gì nếu không có #he-analytics (chưa cấu hình GTM/GA4).
// Không gửi PII: sự kiện chỉ mang nhãn vị trí/nguồn/giải pháp, không bao giờ đọc giá trị ô nhập.
const cfgEl = document.getElementById('he-analytics');
let cfg = null;
try { cfg = cfgEl ? JSON.parse(cfgEl.textContent) : null; } catch (e) { cfg = null; }

if (cfg && (cfg.gtm || cfg.ga4)) {
    const KEY = 'he_consent';
    const read = () => { try { return localStorage.getItem(KEY); } catch (e) { return null; } };
    const write = (v) => { try { localStorage.setItem(KEY, v); } catch (e) { /* bỏ qua */ } };
    const update = (granted) => {
        const s = granted ? 'granted' : 'denied';
        window.gtag?.('consent', 'update', { ad_storage: s, analytics_storage: s, ad_user_data: s, ad_personalization: s });
    };

    // Sự kiện: GTM đọc dataLayer; GA4 thuần (không GTM) nhận qua gtag('event').
    const emit = (event, params = {}) => {
        if (cfg.gtm) window.dataLayer.push({ event, ...params });
        else window.gtag('event', event, params);
    };

    // GA4 thuần: cấu hình trước để sự kiện xếp hàng đúng thứ tự (script nạp sau).
    if (!cfg.gtm) { window.gtag('js', new Date()); window.gtag('config', cfg.ga4); }

    // `solution` luôn là slug ổn định: giá trị lạ -> 'other', trống -> 'none'. product_tier theo danh sách chủ lực.
    const solutionOf = (v) => (!v || v === 'none' ? 'none' : (cfg.solutions || []).includes(v) ? v : 'other');
    const tierOf = (s) => ((cfg.flagship || []).includes(s) ? 'flagship' : 'other');
    const product = (v) => { const solution = solutionOf(v); return { solution, product_tier: tierOf(solution) }; };

    // ---- Consent ----
    const saved = read();
    if (saved === 'granted' || saved === 'denied') {
        update(saved === 'granted');
    } else {
        const banner = document.querySelector('[data-consent-banner]');
        if (banner) {
            banner.hidden = false;
            banner.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-consent]');
                if (!btn) return;
                const granted = btn.dataset.consent === 'granted';
                write(granted ? 'granted' : 'denied');
                update(granted);
                banner.hidden = true;
            });
        }
    }

    // ---- Nạp script sau tương tác hoặc idle (không chặn render, giữ LCP) ----
    let loaded = false;
    const load = () => {
        if (loaded) return;
        loaded = true;
        const s = document.createElement('script');
        s.async = true;
        if (cfg.gtm) {
            window.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
            s.src = `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(cfg.gtm)}`;
        } else {
            s.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(cfg.ga4)}`;
        }
        document.head.appendChild(s);
    };
    const kick = () => {
        ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach((t) => window.removeEventListener(t, kick));
        load();
    };
    ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach((t) => window.addEventListener(t, kick, { once: true, passive: true }));
    const idle = window.requestIdleCallback || ((fn) => setTimeout(fn, 3500));
    window.addEventListener('load', () => idle(load, { timeout: 5000 }));

    // ---- Sự kiện chuyển đổi (data-attribute / href, ủy quyền 1 listener) ----
    const where = (el) => el.closest('[data-cta-pos]')?.dataset.ctaPos || el.dataset.ctaPos || 'page';
    document.addEventListener('click', (e) => {
        const a = e.target.closest('a');
        if (!a) return;
        const href = a.getAttribute('href') || '';
        if (href.startsWith('tel:')) emit('click_phone', { position: where(a) });
        else if (/^https?:\/\/(www\.)?zalo\.me\//.test(href)) emit('click_zalo', { position: where(a) });
        else if (a.dataset.cta) emit('cta_click', {
            position: a.dataset.cta,
            cta_text: (a.dataset.ctaLabel || '').slice(0, 60),
            ...product(a.dataset.solution || href.match(/\/dich-vu\/([a-z0-9-]+)/)?.[1] || a.closest('[data-view-service]')?.dataset.viewService),
        });
    });

    // Form gửi thành công (app.js phát he:lead sau khi server trả ok:true).
    document.addEventListener('he:lead', (e) => {
        const { source, solution } = e.detail || {};
        emit('lead_submit', { source: source === 'lead-form' ? 'lead-form' : 'contact-form', ...product(solution) });
    });

    // Xem trang giải pháp
    const svc = document.querySelector('[data-view-service]');
    if (svc) emit('view_service', product(svc.dataset.viewService));

    // Cuộn 50% / 90% (tuỳ chọn trong Cài đặt)
    if (cfg.scroll) {
        const marks = [50, 90];
        let ticking = false;
        const check = () => {
            ticking = false;
            const max = document.documentElement.scrollHeight - window.innerHeight;
            if (max <= 0) return;
            const pct = (window.scrollY / max) * 100;
            while (marks.length && pct >= marks[0]) emit('scroll_depth', { percent: marks.shift() });
            if (!marks.length) window.removeEventListener('scroll', onScroll);
        };
        const onScroll = () => { if (!ticking) { ticking = true; requestAnimationFrame(check); } };
        window.addEventListener('scroll', onScroll, { passive: true });
    }
}
