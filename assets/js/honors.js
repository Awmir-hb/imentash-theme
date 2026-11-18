/* قالب ایمن تاش — نوار افتخارات: عکس بزرگ تندیس در پنجره، با قبلی/بعدی.
   حرکت نوار فقط با CSS است (honors.css).
   - ترتیب از فهرست اصلی (نه نسخه تکراری نوار)؛ از هر کدام باز شود همان افتخار نمایش داده می‌شود.
   - صفحه راست‌به‌چپ: فلش چپ و کشیدن انگشت به راست = بعدی.
   - عکس‌های کناری پیش‌بارگذاری می‌شوند تا ورق زدن بی‌مکث باشد. */
(() => {
  document.querySelectorAll('[data-em-honors]').forEach((box) => {
    const dlg = box.querySelector('[data-em-hzoom]');
    // بدون پشتیبانی dialog، لینک خود عکس باز می‌شود.
    if (!dlg || typeof dlg.showModal !== 'function') return;

    const img = dlg.querySelector('img');
    const cap = dlg.querySelector('[data-em-hzoom-caption]');
    const pos = dlg.querySelector('[data-em-hzoom-pos]');
    const nav = dlg.querySelector('.em-hzoom__nav');
    const links = [...box.querySelectorAll('.em-honors__list:not([data-em-copy]) [data-em-honor-zoom]')];
    const lang = document.documentElement.lang || 'fa';
    let idx = 0;
    let opener = null;

    if (nav) nav.hidden = links.length < 2;

    const preload = (i) => {
      const link = links[(i + links.length) % links.length];
      if (link) (new Image()).src = link.href;
    };

    const show = (i) => {
      if (!links.length) return;
      idx = (i + links.length) % links.length;
      const link = links[idx];
      img.src = link.href;
      img.alt = link.dataset.emCaption || '';
      cap.textContent = link.dataset.emCaption || '';
      if (pos) pos.textContent = `${(idx + 1).toLocaleString(lang)} / ${links.length.toLocaleString(lang)}`;
      preload(idx + 1);
      preload(idx - 1);
    };

    box.addEventListener('click', (e) => {
      const link = e.target.closest('[data-em-honor-zoom]');
      if (!link) return;
      e.preventDefault();
      opener = link;
      const i = links.findIndex((l) => l.href === link.href);
      show(i < 0 ? 0 : i);
      dlg.showModal();
    });

    dlg.querySelector('[data-em-hzoom-close]').addEventListener('click', () => dlg.close());
    const prev = dlg.querySelector('[data-em-hzoom-prev]');
    const next = dlg.querySelector('[data-em-hzoom-next]');
    if (prev) prev.addEventListener('click', () => show(idx - 1));
    if (next) next.addEventListener('click', () => show(idx + 1));

    dlg.addEventListener('keydown', (e) => {
      if (links.length < 2) return;
      if (e.key === 'ArrowLeft') show(idx + 1);
      else if (e.key === 'ArrowRight') show(idx - 1);
    });

    // کشیدن انگشت روی عکس (موبایل)
    let x0 = null;
    img.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive: true });
    img.addEventListener('touchend', (e) => {
      if (x0 === null || links.length < 2) return;
      const dx = e.changedTouches[0].clientX - x0;
      x0 = null;
      if (Math.abs(dx) < 40) return;
      show(dx > 0 ? idx + 1 : idx - 1);
    }, { passive: true });

    // کلیک روی پس‌زمینه (خود dialog، نه محتوایش) می‌بندد
    dlg.addEventListener('click', (e) => {
      if (e.target === dlg) dlg.close();
    });
    // فوکوس به تندیس برمی‌گردد؛ اگر از نسخه تکراری باز شده بود (بیرون از Tab)، به ناحیه کلی نمی‌رود.
    dlg.addEventListener('close', () => {
      if (opener && opener.tabIndex !== -1) opener.focus();
    });
  });
})();
