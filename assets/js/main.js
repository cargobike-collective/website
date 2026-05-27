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
