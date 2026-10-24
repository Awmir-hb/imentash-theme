/* قالب ایمن تاش — صفحه اصلی */
(() => {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- رسانه هیرو ----------
     ویدئو یا گیف فقط برای اندازه صفحه مناسب ساخته می‌شود تا هر دو نسخه دانلود نشوند.
     موبایل: ویدئوی عمودی؛ اگر نبود و پوستر داریم همان پوستر، وگرنه رسانه دسکتاپ. */
  const media = document.querySelector('[data-em-hero-media]');
  if (media && !reduce) {
    const d = media.dataset;
    let src = d.emDesktop;
    let type = d.emDesktopType;
    if (window.matchMedia('(max-width: 767px)').matches) {
      if (d.emMobile) {
        src = d.emMobile;
        type = d.emMobileType;
      } else if (d.emPoster) {
        src = '';
      }
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
        el.addEventListener('loadeddata', show, { once: true });
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
})();
