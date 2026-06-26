// Mobile / overlay navigation toggle
(() => {
  const nav = document.querySelector('.nav');
  if (!nav) return;

  const toggle = nav.querySelector('.nav__toggle');
  const close = nav.querySelector('.nav__close');
  const menu = nav.querySelector('.nav__menu');

  const open = () => {
    menu.hidden = false;
    // next frame so the transition can run from the hidden state
    requestAnimationFrame(() => nav.classList.add('is-open'));
    toggle.setAttribute('aria-expanded', 'true');
    document.body.classList.add('nav-open');
  };

  const shut = () => {
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('nav-open');
    // wait for the transition before hiding from the a11y tree
    menu.addEventListener('transitionend', () => {
      if (!nav.classList.contains('is-open')) menu.hidden = true;
    }, { once: true });
  };

  toggle.addEventListener('click', open);
  close.addEventListener('click', shut);

  // Close when a menu link is clicked (one-pager anchors)
  menu.querySelectorAll('.nav__link').forEach((link) =>
    link.addEventListener('click', shut)
  );

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav.classList.contains('is-open')) shut();
  });
})();

// Events block: filter the list by tag
(() => {
  document.querySelectorAll('.events').forEach((section) => {
    const tabs = section.querySelectorAll('.events__tab');
    const items = section.querySelectorAll('.events__item');
    if (!tabs.length) return;

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const filter = tab.dataset.filter;
        tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
        items.forEach((item) => {
          item.hidden = filter !== 'all' && item.dataset.tag !== filter;
        });
      });
    });
  });
})();

// Hero slider: enhance the CSS scroll-snap track with dots, arrows, keyboard
// and optional autoplay. The track already swipes/snaps on its own, so this is
// purely additive — if it doesn't run, the slider still works by swiping.
(() => {
  document.querySelectorAll('.hero--slider').forEach((hero) => {
    const track = hero.querySelector('.hero__track');
    const slides = Array.from(hero.querySelectorAll('.hero__slide'));
    const dots = Array.from(hero.querySelectorAll('.hero__dot'));
    const prev = hero.querySelector('.hero__nav--prev');
    const next = hero.querySelector('.hero__nav--next');
    if (!track || slides.length < 2) return;

    hero.classList.add('is-ready');

    let current = 0;
    const goTo = (i) => {
      const idx = (i + slides.length) % slides.length;
      track.scrollTo({ left: idx * track.clientWidth, behavior: 'smooth' });
    };
    const setActive = (i) => {
      if (i === current) return;
      current = i;
      dots.forEach((d, di) => {
        d.classList.toggle('is-active', di === i);
        if (di === i) d.setAttribute('aria-current', 'true');
        else d.removeAttribute('aria-current');
      });
    };

    // Derive the active slide from the scroll position (throttled with rAF).
    let raf = null;
    track.addEventListener('scroll', () => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = null;
        setActive(Math.round(track.scrollLeft / track.clientWidth));
      });
    });

    dots.forEach((d, i) => d.addEventListener('click', () => goTo(i)));
    if (prev) prev.addEventListener('click', () => goTo(current - 1));
    if (next) next.addEventListener('click', () => goTo(current + 1));

    track.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(current - 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); goTo(current + 1); }
    });

    // Optional autoplay, paused on hover/focus and when the tab is hidden.
    const interval = parseInt(hero.dataset.autoplay || '0', 10);
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (interval > 0 && !reduce) {
      let timer = null;
      const play = () => { if (!timer) timer = setInterval(() => goTo(current + 1), interval); };
      const stop = () => { clearInterval(timer); timer = null; };
      play();
      hero.addEventListener('mouseenter', stop);
      hero.addEventListener('mouseleave', play);
      hero.addEventListener('focusin', stop);
      hero.addEventListener('focusout', play);
      document.addEventListener('visibilitychange', () => (document.hidden ? stop() : play()));
    }
  });
})();
