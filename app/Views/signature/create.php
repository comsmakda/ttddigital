<?php
/* ttd_buat.php */
$title = 'Buat TTD Baru';
$subtitle = 'Isi data dokumen, sistem akan membuat kode unik & QR Code otomatis.';
$selectedJenis = $jenis === 'sertifikat' ? 'sertifikat' : 'surat';
?>

<div class="create-grid">
  <div class="card">
    <div class="tab-bar">
      <button type="button" class="tab-btn <?= $selectedJenis === 'surat' ? 'active' : '' ?>" data-jenis-tab="surat">
        <i class="ti ti-mail"></i> Surat
      </button>
      <button type="button" class="tab-btn <?= $selectedJenis === 'sertifikat' ? 'active' : '' ?>" data-jenis-tab="sertifikat">
        <i class="ti ti-certificate"></i> Sertifikat
      </button>
    </div>

    <form method="POST" action="<?= url('/ttd/buat') ?>" id="ttdForm">
      <?= csrf_field() ?>
      <input type="hidden" name="jenis" id="jenis-input" value="<?= e($selectedJenis) ?>">

      <div id="fields-surat" style="display: <?= $selectedJenis === 'surat' ? 'block' : 'none' ?>;">
        <div class="field-group">
          <label class="field-label">Nomor Surat</label>
          <input type="text" name="nomor_surat" id="in-nomor_surat" class="field-input" placeholder="mis. 421/123/SMKN2-PRG/2026" value="<?= old('nomor_surat') ?>">
        </div>
        <div class="field-group">
          <label class="field-label">Perihal</label>
          <input type="text" name="perihal" id="in-perihal" class="field-input" placeholder="mis. Surat Keterangan Aktif Siswa" value="<?= old('perihal') ?>">
        </div>
      </div>

      <div id="fields-sertifikat" style="display: <?= $selectedJenis === 'sertifikat' ? 'block' : 'none' ?>;">
        <div class="field-group">
          <label class="field-label">Nama Sertifikat</label>
          <input type="text" name="nama_sertifikat" id="in-nama_sertifikat" class="field-input" placeholder="mis. Sertifikat Juara 1 Lomba Web Design" value="<?= old('nama_sertifikat') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Nama Penandatangan</label>
          <input type="text" name="nama_penandatangan" id="in-nama_penandatangan" class="field-input" placeholder="mis. Drs. Ahmad, M.Pd." value="<?= old('nama_penandatangan') ?>" required>
        </div>
        <div class="field-group">
          <label class="field-label">Jabatan Penandatangan</label>
          <input type="text" name="jabatan_penandatangan" id="in-jabatan_penandatangan" class="field-input" placeholder="mis. Kepala Sekolah" value="<?= old('jabatan_penandatangan') ?>" required>
        </div>
      </div>

      <div class="field-group">
        <label class="field-label">Tanggal TTD</label>
        <input type="date" name="tanggal_ttd" id="in-tanggal_ttd" class="field-input" value="<?= old('tanggal_ttd', date('Y-m-d')) ?>" required>
      </div>

      <div class="field-hint" style="margin-bottom:1.2rem;">
        Setelah disimpan, sistem otomatis membuat kode unik & QR Code (dengan logo COM di tengah) yang bisa didownload dan ditempel ke dokumen.
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="ti ti-qrcode"></i> Simpan &amp; Generate QR
      </button>
      <a href="<?= url('/dashboard') ?>" class="btn btn-outline">Batal</a>
    </form>
  </div>

  <aside class="preview-card">
    <span class="preview-eyebrow"><i class="ti ti-eye"></i> Pratinjau Dokumen</span>

    <div class="preview-doc">
      <div class="preview-doc-head">
        <div class="preview-doc-seal"><i class="ti ti-shield-check"></i></div>
        <div class="preview-doc-head-text">
          <span class="preview-doc-org">SMKN 2 Pinrang</span>
          <span class="preview-doc-kind" id="previewKind">Surat</span>
        </div>
      </div>

      <h3 class="preview-doc-title" id="previewTitle">Perihal surat akan tampil di sini</h3>
      <p class="preview-doc-number" id="previewNumber">Nomor surat</p>

      <div class="preview-doc-divider"></div>

      <div class="preview-sign">
        <div class="preview-sign-seal"><i class="ti ti-qrcode"></i></div>
        <div class="preview-sign-text">
          <strong id="previewSignerName">Nama Penandatangan</strong>
          <span id="previewSignerRole">Jabatan Penandatangan</span>
          <span class="preview-sign-date" id="previewDate">—</span>
        </div>
      </div>
    </div>

    <p class="preview-hint"><i class="ti ti-info-circle"></i> QR Code &amp; kode unik dibuat otomatis setelah disimpan.</p>
  </aside>
</div>

<script>
(function () {
  const jenisInput = document.getElementById('jenis-input');
  const tabs = document.querySelectorAll('[data-jenis-tab]');
  const fieldsSurat = document.getElementById('fields-surat');
  const fieldsSertifikat = document.getElementById('fields-sertifikat');
  const previewKind = document.getElementById('previewKind');
  const previewTitle = document.getElementById('previewTitle');
  const previewNumber = document.getElementById('previewNumber');

  const bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

  function setJenis(jenis) {
    jenisInput.value = jenis;
    tabs.forEach(t => t.classList.toggle('active', t.dataset.jenisTab === jenis));
    fieldsSurat.style.display = jenis === 'surat' ? 'block' : 'none';
    fieldsSertifikat.style.display = jenis === 'sertifikat' ? 'block' : 'none';
    previewKind.textContent = jenis === 'surat' ? 'Surat' : 'Sertifikat';
    updatePreview();
  }

  function updatePreview() {
    const jenis = jenisInput.value;
    if (jenis === 'surat') {
      const perihal = document.getElementById('in-perihal').value.trim();
      const nomor = document.getElementById('in-nomor_surat').value.trim();
      previewTitle.textContent = perihal || 'Perihal surat akan tampil di sini';
      previewNumber.textContent = nomor || 'Nomor surat';
      previewNumber.style.display = 'block';
    } else {
      const nama = document.getElementById('in-nama_sertifikat').value.trim();
      previewTitle.textContent = nama || 'Nama sertifikat akan tampil di sini';
      previewNumber.textContent = '';
      previewNumber.style.display = 'none';
    }
    document.getElementById('previewSignerName').textContent =
      document.getElementById('in-nama_penandatangan').value.trim() || 'Nama Penandatangan';
    document.getElementById('previewSignerRole').textContent =
      document.getElementById('in-jabatan_penandatangan').value.trim() || 'Jabatan Penandatangan';

    const tgl = document.getElementById('in-tanggal_ttd').value;
    const dateEl = document.getElementById('previewDate');
    if (tgl) {
      const d = new Date(tgl + 'T00:00:00');
      dateEl.textContent = d.getDate() + ' ' + bulanIndo[d.getMonth()] + ' ' + d.getFullYear();
    } else {
      dateEl.textContent = '—';
    }
  }

  tabs.forEach(t => t.addEventListener('click', () => setJenis(t.dataset.jenisTab)));
  document.getElementById('ttdForm').addEventListener('input', updatePreview);
  updatePreview();
})();
</script>