// Chỉ JS cần thiết: đóng/mở menu mobile. Mọi thứ khác (FAQ, dropdown) là HTML/CSS thuần.
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

// Form lead trên trang được cache HTML: lấy token CSRF khi người dùng tương tác (không để token trong HTML cache).
document.querySelectorAll('input[data-csrf-from]').forEach((input) => {
    const form = input.form;
    const load = () => input.value
        ? Promise.resolve()
        : fetch(input.dataset.csrfFrom, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((d) => { input.value = d.token || ''; });
    form.addEventListener('focusin', () => { load().catch(() => {}); }, { once: true });
    form.addEventListener('submit', (e) => {
        if (input.value) return;
        e.preventDefault();
        load().then(() => form.submit()).catch(() => form.submit());
    });
});
