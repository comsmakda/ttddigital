document.addEventListener('DOMContentLoaded', function () {
  /* ---------- Sidebar off-canvas (mobile) ---------- */
  var sidebar = document.getElementById('appSidebar');
  var overlay = document.querySelector('.sidebar-overlay');
  var toggles = document.querySelectorAll('[data-menu-toggle]');

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('sidebar-open');
    if (overlay) overlay.classList.remove('show');
  }

  function toggleSidebar() {
    if (!sidebar) return;
    sidebar.classList.toggle('sidebar-open');
    if (overlay) overlay.classList.toggle('show');
  }

  toggles.forEach(function (el) {
    el.addEventListener('click', toggleSidebar);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSidebar();
  });

  /* ---------- Confirm dialogs ---------- */
  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var msg = form.getAttribute('data-confirm') || 'Yakin ingin melanjutkan?';
      if (!window.confirm(msg)) {
        e.preventDefault();
      }
    });
  });

  /* Note: the Surat/Sertifikat tab toggle + live preview on the "Buat TTD"
     page is handled by its own inline script (ttd_buat.php), since it also
     drives the formal letter/certificate preview markup. It's intentionally
     not duplicated here to avoid double event bindings. */
});