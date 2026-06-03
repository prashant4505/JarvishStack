// JarvishStack — application scripts

document.addEventListener('DOMContentLoaded', () => {

  // --- Mobile nav toggle ---
  const toggle = document.getElementById('navToggle');
  const menu   = document.getElementById('navMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      const open = menu.classList.toggle('open');
      toggle.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', open);
    });
    // Close on outside click
    document.addEventListener('click', e => {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
        toggle.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // --- Active nav link ---
  const path = window.location.pathname;
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === path || (href !== '/' && path.startsWith(href))) {
      link.classList.add('active');
    }
  });

  // --- Auto-dismiss alerts after 5 s ---
  document.querySelectorAll('.alert').forEach(alert => {
    const close = alert.querySelector('.alert-close');
    const dismiss = () => {
      alert.style.transition = 'opacity .4s, transform .4s';
      alert.style.opacity    = '0';
      alert.style.transform  = 'translateY(-6px)';
      setTimeout(() => alert.remove(), 400);
    };
    if (close) close.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
  });

});
