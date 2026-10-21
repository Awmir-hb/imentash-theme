/* قالب ایمن تاش — منوی تمام‌صفحه موبایل */
(() => {
  const menu = document.getElementById('em-menu');
  if (!menu) return;

  const root = document.documentElement;
  const openers = document.querySelectorAll('[aria-controls="em-menu"]');
  const focusable = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';
  let lastFocus = null;

  const setExpanded = (value) => openers.forEach((b) => b.setAttribute('aria-expanded', String(value)));

  const onKey = (e) => {
    if (e.key === 'Escape') {
      close();
      return;
    }
    if (e.key !== 'Tab') return;
    // نگه داشتن فوکوس داخل منو
    const items = menu.querySelectorAll(focusable);
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  };

  function open() {
    lastFocus = document.activeElement;
    menu.hidden = false;
    root.classList.add('em-lock');
    setExpanded(true);
    const closeBtn = menu.querySelector('[data-em-close]');
    if (closeBtn) closeBtn.focus();
    document.addEventListener('keydown', onKey);
  }

  function close() {
    if (menu.hidden) return;
    menu.hidden = true;
    root.classList.remove('em-lock');
    setExpanded(false);
    document.removeEventListener('keydown', onKey);
    if (lastFocus && document.contains(lastFocus)) lastFocus.focus();
  }

  openers.forEach((b) => b.addEventListener('click', open));
  menu.querySelectorAll('[data-em-close]').forEach((b) => b.addEventListener('click', close));

  // لینک‌های داخل صفحه (#...) منو را می‌بندند
  menu.addEventListener('click', (e) => {
    if (e.target.closest('a[href^="#"]')) close();
  });

  // اگر عرض صفحه به دسکتاپ رسید، منو بسته شود
  const desktop = window.matchMedia('(min-width: 1101px)');
  desktop.addEventListener('change', (e) => {
    if (e.matches) close();
  });
})();
