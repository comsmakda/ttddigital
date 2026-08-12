document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('[data-menu-toggle]');
  var sidebar = document.querySelector('.sidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
      if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var msg = form.getAttribute('data-confirm') || 'Yakin ingin melanjutkan?';
      if (!window.confirm(msg)) {
        e.preventDefault();
      }
    });
  });

  // Toggle field jenis dokumen (surat / sertifikat) di form buat TTD
  var tabButtons = document.querySelectorAll('[data-jenis-tab]');
  var jenisInput = document.getElementById('jenis-input');
  var suratFields = document.getElementById('fields-surat');
  var sertifikatFields = document.getElementById('fields-sertifikat');

  if (tabButtons.length && jenisInput) {
    tabButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var jenis = btn.getAttribute('data-jenis-tab');
        jenisInput.value = jenis;

        tabButtons.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');

        if (jenis === 'surat') {
          suratFields.style.display = '';
          sertifikatFields.style.display = 'none';
        } else {
          suratFields.style.display = 'none';
          sertifikatFields.style.display = '';
        }
      });
    });
  }
});
