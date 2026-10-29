/* قالب ایمن تاش — صفحه اصلی */
(() => {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- رسانه هیرو ----------
     ویدئو یا گیف فقط برای اندازه صفحه مناسب ساخته می‌شود تا هر دو نسخه دانلود نشوند.
     موبایل: فقط ویدئوی عمودی (اگر در پنل انتخاب شده)، وگرنه همان پوستر؛ رسانه سنگین دسکتاپ هیچ‌وقت در موبایل نمی‌آید.
     با Save-Data (حالت کم‌مصرف مرورگر) هیچ ویدئویی بارگذاری نمی‌شود. */
  const media = document.querySelector('[data-em-hero-media]');
  const saveData = Boolean(navigator.connection && navigator.connection.saveData);

  /* لودر با آماده شدن ویدئو، خطا یا بعد از مهلت محو و سپس حذف می‌شود تا انیمیشن بی‌نهایتش نماند */
  const loader = document.querySelector('[data-em-hero-loader]');
  let loaderTimer = 0;
  const hideLoader = () => {
    if (!loader || !loader.isConnected) return;
    clearTimeout(loaderTimer);
    const remove = () => loader.remove();
    if (getComputedStyle(loader).display === 'none') return remove();
    loader.addEventListener('transitionend', remove, { once: true });
    setTimeout(remove, 1000);
    loader.classList.add('em-hero__loader--done');
  };
  if (loader) loaderTimer = setTimeout(hideLoader, 8000);

  if (media && !reduce && !saveData) {
    const d = media.dataset;
    let src = d.emDesktop;
    let type = d.emDesktopType;
    if (window.matchMedia('(max-width: 767px)').matches) {
      src = d.emMobile || '';
      type = d.emMobileType;
    }

    if (src) {
      const el = document.createElement(type === 'video' ? 'video' : 'img');
      el.className = 'em-hero__live';
      const show = () => el.classList.add('em-hero__live--on');
      if (type === 'video') {
        el.muted = true;
        el.loop = true;
        el.autoplay = true;
        el.playsInline = true;
        el.setAttribute('muted', '');
        el.setAttribute('playsinline', '');
        const ready = () => {
          show();
          hideLoader();
        };
        el.addEventListener('loadeddata', ready, { once: true });
        el.addEventListener('canplay', ready, { once: true });
        // خطا: ویدئو پنهان می‌ماند و پوستر زیر لودر دیده می‌شود
        el.addEventListener('error', hideLoader, { once: true });
      } else {
        el.alt = '';
        el.decoding = 'async';
        el.addEventListener('load', show, { once: true });
      }
      el.src = src;
      media.appendChild(el);
      if (type === 'video') el.play().catch(() => {});
    }
  }
  // ویدئویی در کار نیست (کاهش حرکت، Save-Data، گیف یا فقط پوستر برای این اندازه)
  if (!media || !media.querySelector('video')) hideLoader();

  /* ---------- کلمات چرخان ----------
     کلمه اول در انتها تکرار می‌شود تا برگشت به ابتدا دیده نشود. */
  const track = document.querySelector('[data-em-rotate]');
  if (track && !reduce && track.children.length > 1) {
    const count = track.children.length;
    track.appendChild(track.children[0].cloneNode(true));
    let index = 0;

    const move = (i, animate) => {
      track.style.transition = animate ? '' : 'none';
      track.style.transform = `translateY(${(-i * 100) / (count + 1)}%)`;
      if (!animate) void track.offsetHeight; // اعمال فوری بدون انیمیشن
      track.style.transition = '';
    };

    track.addEventListener('transitionend', () => {
      if (index >= count) {
        index = 0;
        move(0, false);
      }
    });

    setTimeout(() => {
      setInterval(() => {
        // اگر تب پنهان بوده و transitionend نیامده، از ابتدا شروع شود
        if (index >= count) {
          index = 0;
          move(0, false);
        }
        index += 1;
        move(index, true);
      }, 2750);
    }, 2000);
  }

  /* ---------- تب دسته‌های محصولات ----------
     الگوی تب WAI-ARIA: کلیک یا کلیدهای جهت‌دار، Home و End. */
  document.querySelectorAll('[data-em-tabs]').forEach((box) => {
    const tabs = Array.from(box.querySelectorAll('[role="tab"]'));

    const select = (tab, focus) => {
      tabs.forEach((t) => {
        const on = t === tab;
        t.setAttribute('aria-selected', String(on));
        t.tabIndex = on ? 0 : -1;
        const panel = document.getElementById(t.getAttribute('aria-controls'));
        if (panel) panel.hidden = !on;
      });
      if (focus) tab.focus();
      // فقط در اسلایدر افقی موبایل: تب انتخاب‌شده دیده شود
      const list = tab.parentElement;
      if (list.scrollWidth > list.clientWidth) {
        tab.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: reduce ? 'auto' : 'smooth' });
      }
    };

    box.addEventListener('click', (e) => {
      const tab = e.target.closest('[role="tab"]');
      if (tab && tab.getAttribute('aria-selected') !== 'true') select(tab, false);
    });

    box.addEventListener('keydown', (e) => {
      const i = tabs.indexOf(document.activeElement);
      if (i < 0) return;
      // در RTL جهت چپ یعنی تب بعدی
      const keys = { ArrowDown: 1, ArrowLeft: 1, ArrowUp: -1, ArrowRight: -1 };
      let next = null;
      if (e.key in keys) next = tabs[(i + keys[e.key] + tabs.length) % tabs.length];
      else if (e.key === 'Home') next = tabs[0];
      else if (e.key === 'End') next = tabs[tabs.length - 1];
      if (!next) return;
      e.preventDefault();
      select(next, true);
    });
  });

  /* ---------- خط تولید ----------
     انیمیشن ترسیم تا رسیدن سکشن به صفحه متوقف است. بدون JS از ابتدا اجرا می‌شود. */
  const line = document.querySelector('[data-em-pl]');
  if (line) {
    const map = line.querySelector('[data-em-viewbox-mobile]');
    if (map) {
      const mq = window.matchMedia('(max-width: 767px)');
      const desktopBox = map.getAttribute('viewBox');
      const fit = () => map.setAttribute('viewBox', mq.matches ? map.dataset.emViewboxMobile : desktopBox);
      fit();
      mq.addEventListener('change', fit);
    }

    if (!reduce && 'IntersectionObserver' in window) {
      line.dataset.emPl = 'wait';
      const io = new IntersectionObserver((entries) => {
        if (entries.some((en) => en.isIntersecting)) {
          line.dataset.emPl = 'on';
          io.disconnect();
        }
      }, { threshold: 0.25 });
      io.observe(line);
    }
  }
})();
