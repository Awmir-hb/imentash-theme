/* قالب ایمن تاش — نوار افتخارات: عکس بزرگ تندیس در پنجره.
   حرکت نوار فقط با CSS است (honors.css)؛ این فایل کار دیگری ندارد. */
(() => {
  document.querySelectorAll('[data-em-honors]').forEach((box) => {
    const dlg = box.querySelector('[data-em-hzoom]');
    // بدون پشتیبانی dialog، لینک خود عکس باز می‌شود.
    if (!dlg || typeof dlg.showModal !== 'function') return;

    const img = dlg.querySelector('img');
    const cap = dlg.querySelector('[data-em-hzoom-caption]');
    let opener = null;

    box.addEventListener('click', (e) => {
      const link = e.target.closest('[data-em-honor-zoom]');
      if (!link) return;
      e.preventDefault();
      opener = link;
      img.src = link.href;
      img.alt = link.dataset.emCaption || '';
      cap.textContent = link.dataset.emCaption || '';
      dlg.showModal();
    });

    dlg.querySelector('[data-em-hzoom-close]').addEventListener('click', () => dlg.close());
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
