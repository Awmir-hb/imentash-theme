/* قالب ایمن تاش — درباره ما: ویدئوی سربرگ (این بخش) و لایت‌باکس گواهینامه‌ها (پایین فایل)
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

/* لایت‌باکس گواهینامه‌ها: کارت ویترین ← dialog بومی با همه سندهای همان کارت (از <template>).
   بدون پشتیبانی dialog، لینک کارت همان عکس بزرگ را باز می‌کند. Esc و قفل فوکوس را خود dialog مدال دارد. */
(() => {
  const dlg = document.querySelector('.em-lb');
  if (!dlg || typeof dlg.showModal !== 'function') return;

  const stage = dlg.querySelector('.em-lb__stage');
  const pos = dlg.querySelector('.em-lb__pos');
  const nav = dlg.querySelector('.em-lb__nav');
  const lang = document.documentElement.lang || 'fa';
  let figs = [];
  let idx = 0;
  let opener = null;

  const show = (i) => {
    idx = (i + figs.length) % figs.length;
    figs.forEach((f, k) => { f.hidden = k !== idx; });
    pos.textContent = figs.length > 1 ? `${(idx + 1).toLocaleString(lang)} / ${figs.length.toLocaleString(lang)}` : '';
  };

  document.addEventListener('click', (e) => {
    const card = e.target.closest('[data-em-lb]');
    if (!card || e.ctrlKey || e.metaKey || e.shiftKey) return;
    const tpl = document.getElementById(card.dataset.emLb);
    if (!tpl) return;
    e.preventDefault();
    opener = card;
    stage.replaceChildren(tpl.content.cloneNode(true));
    figs = [...stage.querySelectorAll('.em-lb__fig')];
    if (!figs.length) return;
    nav.hidden = figs.length < 2;
    show(0);
    dlg.showModal();
    dlg.querySelector('.em-lb__close').focus();
  });

  dlg.querySelector('.em-lb__close').addEventListener('click', () => dlg.close());
  dlg.querySelector('.em-lb__prev').addEventListener('click', () => show(idx - 1));
  dlg.querySelector('.em-lb__next').addEventListener('click', () => show(idx + 1));
  // کلیک روی پس‌زمینه (بیرون کادر) می‌بندد.
  dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); });
  // صفحه راست‌به‌چپ: فلش چپ = بعدی.
  dlg.addEventListener('keydown', (e) => {
    if (figs.length < 2) return;
    if (e.key === 'ArrowLeft') show(idx + 1);
    else if (e.key === 'ArrowRight') show(idx - 1);
  });
  dlg.addEventListener('close', () => {
    stage.replaceChildren();
    if (opener) opener.focus();
  });
})();
