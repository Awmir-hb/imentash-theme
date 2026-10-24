/* قالب ایمن تاش — متاباکس‌ها: ردیف‌های تکرارشونده و انتخاب فایل */
(() => {
  let uid = Date.now();

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-em-rep-add], [data-em-rep-remove], [data-em-rep-up], [data-em-rep-down], [data-em-media-pick], [data-em-media-clear]');
    if (!btn) return;
    e.preventDefault();

    // ردیف‌های تکرارشونده
    const rep = btn.closest('[data-em-rep]');
    if (rep && btn.hasAttribute('data-em-rep-add')) {
      const tpl = rep.querySelector('template[data-em-rep-tpl]');
      const list = rep.querySelector('[data-em-rep-list]');
      list.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__i__/g, String(uid++)));
      const input = list.lastElementChild.querySelector('input, textarea, select');
      if (input) input.focus();
      return;
    }

    const row = btn.closest('.em-rep__row');
    if (row) {
      if (btn.hasAttribute('data-em-rep-remove')) {
        row.remove();
      } else if (btn.hasAttribute('data-em-rep-up') && row.previousElementSibling) {
        row.parentNode.insertBefore(row, row.previousElementSibling);
        btn.focus();
      } else if (btn.hasAttribute('data-em-rep-down') && row.nextElementSibling) {
        row.parentNode.insertBefore(row.nextElementSibling, row);
        btn.focus();
      }
      return;
    }

    // انتخاب فایل از کتابخانه رسانه
    const box = btn.closest('[data-em-media]');
    if (!box) return;
    const input = box.querySelector('[data-em-media-input]');
    const name = box.querySelector('[data-em-media-name]');
    const clear = box.querySelector('[data-em-media-clear]');

    if (btn.hasAttribute('data-em-media-clear')) {
      input.value = '';
      name.textContent = clear.dataset.empty || '';
      clear.hidden = true;
      return;
    }

    if (!window.wp || !wp.media) return;
    if (!box.emFrame) {
      box.emFrame = wp.media({
        title: box.dataset.title,
        button: { text: box.dataset.button },
        library: box.dataset.type ? { type: box.dataset.type } : {},
        multiple: false
      });
      box.emFrame.on('select', () => {
        const file = box.emFrame.state().get('selection').first().toJSON();
        input.value = String(file.id);
        name.textContent = file.filename || file.title || '';
        clear.hidden = false;
      });
    }
    box.emFrame.open();
  });
})();
