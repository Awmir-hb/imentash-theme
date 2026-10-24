/* قالب ایمن تاش — فرم تماس: ارسال با AJAX؛ بدون JS همان فرم معمولی کار می‌کند */
(() => {
  document.querySelectorAll('[data-em-inquiry]').forEach((form) => {
    const status = form.querySelector('[data-em-status]');
    const submit = form.querySelector('[data-em-submit]');
    const label = submit ? submit.textContent : '';
    if (!status || !submit || !window.fetch || !window.FormData) return;

    const showStatus = (type, text) => {
      status.className = 'em-form__status' + (type ? ' em-form__status--' + type : '');
      status.textContent = text;
      status.hidden = !text;
    };

    const clearErrors = () => {
      form.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));
      form.querySelectorAll('[data-em-error]').forEach((el) => {
        el.textContent = '';
        el.hidden = true;
      });
    };

    const showErrors = (errors) => {
      let first = null;
      Object.keys(errors).forEach((name) => {
        const field = form.elements[name];
        if (!field) return;
        field.setAttribute('aria-invalid', 'true');
        const msg = document.getElementById(field.id + '-err');
        if (msg) {
          msg.textContent = errors[name];
          msg.hidden = false;
        }
        if (!first) first = field;
      });
      return first;
    };

    const busy = (on) => {
      submit.disabled = on;
      form.setAttribute('aria-busy', String(on));
      submit.textContent = on ? form.dataset.emSending : label;
    };

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors();
      busy(true);

      let data = null;
      try {
        const res = await fetch(form.dataset.emAjax, { method: 'POST', body: new FormData(form), credentials: 'same-origin' });
        data = await res.json();
      } catch (err) {
        data = null;
      }
      busy(false);

      if (!data) {
        showStatus('error', form.dataset.emFailed);
        status.focus();
        return;
      }

      if (data.status === 'ok') {
        // نوع درخواست و محصول انتخاب‌شده برای ارسال بعدی می‌ماند.
        const type = form.querySelector('input[name="em_type"]:checked');
        const typeValue = type ? type.value : '';
        form.reset();
        ['em_name', 'em_company', 'em_phone', 'em_email', 'em_message'].forEach((n) => {
          if (form.elements[n]) form.elements[n].value = '';
        });
        if (typeValue && form.querySelector('input[name="em_type"][value="' + typeValue + '"]')) {
          form.querySelector('input[name="em_type"][value="' + typeValue + '"]').checked = true;
        }
        showStatus('ok', data.message);
        status.focus();
        return;
      }

      showStatus('error', data.message || form.dataset.emFailed);
      const first = data.errors ? showErrors(data.errors) : null;
      if (first) first.focus();
      else status.focus();
    });
  });
})();
