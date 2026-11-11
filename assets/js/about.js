/* قالب ایمن تاش — درباره ما: ویدئوی سربرگ
   فقط نسخه اندازه صفحه ساخته می‌شود (موبایل اگر نسخه جدا ندارد همان دسکتاپ).
   با کاهش حرکت، Save-Data یا اینترنت ۲G فقط فریم اول ثابت دیده می‌شود (مثل هیروی صفحه اصلی).
   دکمه توقف/پخش؛ بیرون از دید هم ویدئو موقتاً متوقف می‌شود. */
(() => {
  const media = document.querySelector('[data-em-ahero-video]');
  if (!media) return;

  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const conn = navigator.connection;
  const saveData = Boolean(conn && (conn.saveData || /(^|-)2g$/.test(conn.effectiveType || '')));
  const still = reduce || saveData;

  const d = media.dataset;
  let src = (window.matchMedia('(max-width: 767px)').matches && d.emMobile) || d.emDesktop;
  if (!src) return;

  const video = document.createElement('video');
  video.className = 'em-ahero__video';
  video.muted = true;
  video.playsInline = true;
  video.setAttribute('muted', '');
  video.setAttribute('playsinline', '');
  video.addEventListener('loadeddata', () => video.classList.add('em-ahero__video--on'), { once: true });

  if (still) {
    // فقط متادیتا و فریم اول؛ #t پرش به فریم اول را در سافاری هم اجباری می‌کند.
    video.preload = 'metadata';
    src += '#t=0.1';
  } else {
    video.loop = true;
    video.autoplay = true;
  }
  video.src = src;
  media.appendChild(video);
  if (still) return;

  const play = () => video.play().catch(() => {});
  play();

  const toggle = document.querySelector('[data-em-ahero-toggle]');
  let userPaused = false;
  const setPaused = (value) => {
    userPaused = value;
    if (value) video.pause(); else play();
    if (!toggle) return;
    toggle.toggleAttribute('data-em-paused', value);
    toggle.setAttribute('aria-label', value ? toggle.dataset.emLabelPlay : toggle.dataset.emLabelPause);
  };
  if (toggle) {
    toggle.hidden = false;
    toggle.addEventListener('click', () => setPaused(!userPaused));
  }

  if ('IntersectionObserver' in window) {
    new IntersectionObserver((entries) => {
      if (userPaused) return;
      if (entries.some((en) => en.isIntersecting)) play(); else video.pause();
    }).observe(media);
  }
})();
