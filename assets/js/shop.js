/* قالب ایمن تاش — فروشگاه و صفحه محصول: مرتب‌سازی، گالری، تب‌ها */
(() => {
  /* ---------- مرتب‌سازی: ارسال با تغییر منو؛ دکمه «اعمال» فقط بدون JS ---------- */
  document.querySelectorAll('[data-em-sort]').forEach((form) => {
    const go = form.querySelector('[data-em-sort-go]');
    if (go) go.hidden = true;
    const select = form.querySelector('select');
    if (select) select.addEventListener('change', () => form.submit());
  });

  /* ---------- گالری محصول: بندانگشتی عکس اصلی را عوض می‌کند ---------- */
  document.querySelectorAll('[data-em-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-em-main]');
    const thumbs = gallery.querySelectorAll('.em-pd__thumb');
    if (!main || !thumbs.length) return;

    thumbs.forEach((thumb) => {
      thumb.addEventListener('click', () => {
        if (thumb.getAttribute('aria-pressed') === 'true') return;
        thumbs.forEach((t) => t.setAttribute('aria-pressed', String(t === thumb)));
        main.removeAttribute('srcset');
        main.src = thumb.dataset.emSrc;
        if (thumb.dataset.emSrcset) main.srcset = thumb.dataset.emSrcset;
        main.alt = thumb.dataset.emAlt || '';
      });
    });
  });

  /* ---------- تب‌های صفحه محصول ----------
     بدون JS همه پنل‌ها زیر هم دیده می‌شوند؛ اینجا فهرست تب نمایش و پنل‌های غیرفعال پنهان می‌شوند. */
  document.querySelectorAll('[data-em-tabs]').forEach((box) => {
    const list = box.querySelector('[data-em-tablist]');
    const tabs = Array.from(box.querySelectorAll('[role="tab"]'));
    if (!list || !tabs.length) return;

    const select = (tab, focus) => {
      tabs.forEach((t) => {
        const on = t === tab;
        t.setAttribute('aria-selected', String(on));
        t.tabIndex = on ? 0 : -1;
        const panel = document.getElementById(t.getAttribute('aria-controls'));
        if (panel) panel.hidden = !on;
      });
      if (focus) tab.focus();
    };

    list.hidden = false;
    box.setAttribute('data-em-ready', '');
    select(tabs.find((t) => t.getAttribute('aria-selected') === 'true') || tabs[0], false);

    list.addEventListener('click', (e) => {
      const tab = e.target.closest('[role="tab"]');
      if (tab) select(tab, false);
    });

    // تب‌های افقی در RTL: چپ یعنی بعدی
    list.addEventListener('keydown', (e) => {
      const i = tabs.indexOf(document.activeElement);
      if (i < 0) return;
      const rtl = getComputedStyle(list).direction === 'rtl';
      const keys = rtl ? { ArrowLeft: 1, ArrowRight: -1 } : { ArrowLeft: -1, ArrowRight: 1 };
      let next = null;
      if (e.key in keys) next = tabs[(i + keys[e.key] + tabs.length) % tabs.length];
      else if (e.key === 'Home') next = tabs[0];
      else if (e.key === 'End') next = tabs[tabs.length - 1];
      if (!next) return;
      e.preventDefault();
      select(next, true);
    });
  });
})();
