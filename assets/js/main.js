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

/* ---------- ظاهر شدن هنگام اسکرول ----------
   هر نوع المان یک ورود ثابت دارد (CSS هر نوع در main.css، [data-em-reveal="..."]):
   words: تیتر سکشن‌ها کلمه‌به‌کلمه (نه حرف‌به‌حرف: حروف فارسی به هم چسبیده‌اند و جدا کردنشان اتصال را می‌شکند)
   read: متن کوتاه معرفی؛ کلمه‌ها با اسکرول از کم‌رنگ به پررنگ (همان .em-mw درباره ما، view() timeline)
   curtain: عکس‌های بزرگ، پرده از پایین · side: کارت خدمات از کنار · zoom: صنایع و گالری
   pop: نوار دعوت فوتر · up (پیش‌فرض): کارت‌های محصول و مقاله، آمار (با شمارش اعداد) و بقیه فهرست‌ها
   - فقط چیزهایی که هنگام بارگذاری پایین‌تر از صفحه‌اند؛ بالای صفحه بدون انیمیشن (بدون چشمک و بدون تأخیر نمایش).
   - بدون JS یا با «کاهش حرکت» هیچ‌چیز پنهان نمی‌شود. متن بلند (مقاله، توضیح محصول، جدول) انیمیشن ندارد.
   - فقط کلاس‌های em- قالب؛ ویجت‌های المنتور دست نمی‌خورند. */
(() => {
  if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const STAGGER = 120;
  const MAX_STEPS = 6;
  // انتخابگر ← نوع ورود. single: خود المان؛ list: فرزندان یکی‌یکی.
  const single = {
    '.em-h2': 'words',
    '.em-head__text, .em-acards__text, .em-textile__text': 'read',
    '.em-frame__img, .em-story__img, .em-rd__img, .em-textile__media': 'curtain',
    '.em-quote, .em-rd__text, .em-hstrip': 'up',
    '.em-fcta': 'pop',
  };
  const lists = {
    '.em-svcs': 'side',
    '.em-inds, .em-gal': 'zoom',
    '.em-posts, .em-pgrid, .em-acards__list, .em-certs__list, .em-tl, .em-rd__list, .em-vals,.em-offices__list, .em-stats': 'up',
  };

  // همه اندازه‌گیری‌ها اول، بعد نوشتن (بدون رفت‌وبرگشت layout).
  const fold = window.innerHeight;
  const below = (el) => el.getBoundingClientRect().top > fold;
  const scrolls = (el) => /(auto|scroll)/.test(getComputedStyle(el).overflowX);
  const targets = [];
  Object.entries(single).forEach(([sel, type]) => {
    document.querySelectorAll(sel).forEach((el) => {
      if (!el.closest('.em-hero') && below(el)) targets.push([el, type, 0]);
    });
  });
  Object.entries(lists).forEach(([sel, type]) => {
    document.querySelectorAll(sel).forEach((list) => {
      if (!below(list)) return;
      // فهرست افقی (اسلایدر موبایل) یکجا ظاهر می‌شود؛ آیتم‌های بیرون از دید هرگز وارد صفحه نمی‌شوند.
      if (scrolls(list)) {
        targets.push([list, 'up', 0]);
        return;
      }
      [...list.children].forEach((item, i) => targets.push([item, type, Math.min(i, MAX_STEPS) * STAGGER]));
    });
  });
  if (!targets.length) return;

  /**
   * کلمه‌های متن داخل span می‌روند؛ تگ‌های داخلی (bdi، a، strong) هر کدام یک واحد می‌مانند.
   * فاصله‌ها متن معمولی می‌مانند تا شکستن خط و چسبیدن حروف فارسی دست نخورد.
   */
  const splitWords = (el, cls) => {
    let i = 0;
    [...el.childNodes].forEach((node) => {
      const frag = document.createDocumentFragment();
      if (node.nodeType === Node.TEXT_NODE) {
        node.textContent.split(/(\s+)/).forEach((part) => {
          if (!part) return;
          if (/^\s+$/.test(part)) {
            frag.append(part);
            return;
          }
          const span = document.createElement('span');
          span.className = cls;
          span.style.setProperty('--i', i++);
          span.textContent = part;
          frag.append(span);
        });
      } else if (node.nodeType === Node.ELEMENT_NODE) {
        const span = document.createElement('span');
        span.className = cls;
        span.style.setProperty('--i', i++);
        node.replaceWith(span);
        span.append(node);
        return;
      } else {
        return;
      }
      node.replaceWith(frag);
    });
  };

  const fa = '۰۱۲۳۴۵۶۷۸۹';
  const countUp = (el) => {
    const text = el.textContent.trim();
    const latin = text.replace(/[۰-۹]/g, (d) => fa.indexOf(d));
    if (!/^[\d٬,]+$/.test(latin)) return; // «ISO» و متن‌های غیرعددی دست نمی‌خورند.
    const end = parseInt(latin.replace(/[٬,]/g, ''), 10);
    const sep = (latin.match(/[٬,]/) || [''])[0];
    const persian = /[۰-۹]/.test(text);
    const format = (n) => {
      let s = String(n);
      if (sep) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, sep);
      return persian ? s.replace(/\d/g, (d) => fa[d]) : s;
    };
    el.style.minWidth = el.getBoundingClientRect().width + 'px';
    const t0 = performance.now();
    const dur = 1400;
    const step = (t) => {
      const p = Math.min(1, (t - t0) / dur);
      el.textContent = format(Math.round(end * (1 - Math.pow(1 - p, 3))));
      if (p < 1) requestAnimationFrame(step);
      else el.style.minWidth = '';
    };
    el.textContent = format(0);
    requestAnimationFrame(step);
  };

  const io = new IntersectionObserver((entries) => {
    entries.forEach((en) => {
      if (!en.isIntersecting) return;
      const el = en.target;
      io.unobserve(el);
      el.classList.add('is-in');
      el.querySelectorAll('.em-stat__num').forEach(countUp);
      if (el.matches('.em-stat__num')) countUp(el);
      // بعد از ظاهر شدن، تأخیر و ویژگی پاک می‌شود تا روی hover و بقیه حرکت‌ها اثری نماند.
      const done = (ev) => {
        if (ev.target !== el) return; // transitionend فرزندها (مثل hover عکس) بالا می‌آید
        el.removeEventListener('transitionend', done);
        el.removeAttribute('data-em-reveal');
        el.style.removeProperty('--reveal-delay');
        el.classList.remove('is-in');
      };
      el.addEventListener('transitionend', done);
    });
  }, { rootMargin: '0px 0px -10% 0px' });

  const scrollTimeline = CSS.supports('animation-timeline: view()');
  targets.forEach(([el, type, delay]) => {
    // «read» خودش با اسکرول جلو می‌رود (CSS)؛ IntersectionObserver لازم ندارد. مرورگر بدون view() متن ثابت می‌بیند.
    if ('read' === type) {
      if (scrollTimeline && !el.querySelector('.em-mw, .em-mr')) splitWords(el, 'em-mr');
      return;
    }
    if ('words' === type) splitWords(el, 'em-rw');
    el.setAttribute('data-em-reveal', type);
    if (delay) el.style.setProperty('--reveal-delay', delay + 'ms');
    io.observe(el);
  });
})();
