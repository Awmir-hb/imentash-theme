/* قالب ایمن تاش — نوار افتخارات: فلش‌ها، کیبورد، حرکت خودکار آهسته */
(() => {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const behavior = reduce ? 'auto' : 'smooth';
  const DELAY = 4000;

  document.querySelectorAll('[data-em-honors]').forEach((box) => {
    const view = box.querySelector('[data-em-honors-view]');
    const nav = box.querySelector('[data-em-honors-nav]');
    const prev = box.querySelector('[data-em-honors-prev]');
    const next = box.querySelector('[data-em-honors-next]');
    const pause = box.querySelector('[data-em-honors-pause]');
    if (!view || !nav || !prev || !next) return;

    // در RTL موقعیت اسکرول منفی است و «بعدی» یعنی حرکت به چپ
    const sign = getComputedStyle(view).direction === 'rtl' ? -1 : 1;
    const pos = () => Math.abs(view.scrollLeft);
    const max = () => view.scrollWidth - view.clientWidth;
    const atEnd = () => pos() >= max() - 2;

    // یک کارت به‌علاوه فاصله بین کارت‌ها
    const step = () => {
      const item = view.querySelector('li');
      if (!item) return view.clientWidth;
      const gap = parseFloat(getComputedStyle(item.parentElement).columnGap) || 0;
      return item.getBoundingClientRect().width + gap;
    };
    const go = (dir) => view.scrollBy({ left: sign * dir * step(), behavior });
    const toStart = () => view.scrollTo({ left: 0, behavior });
    const toEnd = () => view.scrollTo({ left: sign * max(), behavior });

    const update = () => {
      nav.hidden = max() <= 2;
      prev.setAttribute('aria-disabled', String(pos() <= 2));
      next.setAttribute('aria-disabled', String(atEnd()));
    };

    /* ---------- حرکت خودکار ----------
       فقط وقتی دیده می‌شود؛ با هاور، فوکوس، تب پنهان یا لمس متوقف؛ با کاهش حرکت خاموش. */
    let userPaused = reduce;
    let hovering = false;
    let focused = false;
    let visible = false;

    const tick = () => {
      if (userPaused || hovering || focused || !visible || document.hidden || max() <= 2) return;
      if (atEnd()) toStart();
      else go(1);
    };

    const setPaused = (value) => {
      userPaused = value;
      if (!pause) return;
      pause.toggleAttribute('data-em-paused', value);
      pause.setAttribute('aria-label', value ? pause.dataset.emLabelPlay : pause.dataset.emLabelPause);
    };

    if (reduce) {
      if (pause) pause.hidden = true;
    } else {
      setInterval(tick, DELAY);
      if ('IntersectionObserver' in window) {
        new IntersectionObserver((entries) => {
          visible = entries.some((en) => en.isIntersecting);
        }, { threshold: 0.3 }).observe(view);
      } else {
        visible = true;
      }
    }

    prev.addEventListener('click', () => {
      if (prev.getAttribute('aria-disabled') !== 'true') go(-1);
    });
    next.addEventListener('click', () => {
      if (next.getAttribute('aria-disabled') !== 'true') go(1);
    });
    if (pause) pause.addEventListener('click', () => setPaused(!userPaused));

    // کیبورد روی ناحیه اسکرول: جهت‌ها به اندازه یک کارت، Home و End تا دو سر
    view.addEventListener('keydown', (e) => {
      const keys = sign < 0 ? { ArrowLeft: 1, ArrowRight: -1 } : { ArrowLeft: -1, ArrowRight: 1 };
      if (e.key in keys) go(keys[e.key]);
      else if (e.key === 'Home') toStart();
      else if (e.key === 'End') toEnd();
      else return;
      e.preventDefault();
    });

    box.addEventListener('mouseenter', () => { hovering = true; });
    box.addEventListener('mouseleave', () => { hovering = false; });
    box.addEventListener('focusin', () => { focused = true; });
    box.addEventListener('focusout', (e) => { focused = box.contains(e.relatedTarget); });
    // کسی که با لمس کشیده، خودش کنترل را گرفته است
    view.addEventListener('touchstart', () => setPaused(true), { passive: true });

    let raf = 0;
    view.addEventListener('scroll', () => {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(update);
    }, { passive: true });
    window.addEventListener('resize', update);
    update();
  });
})();
