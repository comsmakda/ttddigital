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

    <!-- Surat: format surat dinas resmi (kop, nomor/lampiran/perihal, salam penutup) -->
    <div class="letter-sheet" id="letterSurat">
      <div class="letter-ribbon">Pratinjau</div>

      <div class="letter-kop">
        <img class="letter-kop-logo" src="<?= asset('images/logo-com.png') ?>" alt="Logo">
        <div class="letter-kop-text">
          <strong>SMK Negeri 2 Pinrang</strong>
          <span>Jl. Pendidikan, Kab. Pinrang, Sulawesi Selatan</span>
        </div>
      </div>
      <div class="letter-kop-rule"></div>

      <div class="letter-meta">
        <table>
          <tr>
            <td class="letter-meta-label">Nomor</td>
            <td>: <span id="previewNomor">—</span></td>
          </tr>
          <tr>
            <td class="letter-meta-label">Lampiran</td>
            <td>: -</td>
          </tr>
          <tr>
            <td class="letter-meta-label">Perihal</td>
            <td>: <strong id="previewPerihal">—</strong></td>
          </tr>
        </table>
        <span class="letter-date" id="previewDate">Pinrang, —</span>
      </div>

      <div class="letter-body-skeleton">
        <span class="ln w-40"></span>
        <span class="ln"></span>
        <span class="ln"></span>
        <span class="ln w-85"></span>
        <span class="ln w-70"></span>
      </div>

      <div class="letter-sign">
        <span>Hormat kami,</span>
        <div class="letter-sign-qr"><i class="ti ti-qrcode"></i></div>
        <strong id="previewSignerNameSurat">Nama Penandatangan</strong>
        <span id="previewSignerRoleSurat">Jabatan Penandatangan</span>
      </div>
    </div>

    <!-- Sertifikat: format sertifikat formal, tetap satu aksen -->
    <div class="letter-sheet cert-sheet" id="letterSertifikat" style="display:none;">
      <div class="letter-ribbon">Pratinjau</div>

      <div class="letter-kop">
        <img class="letter-kop-logo" src="<?= asset('images/logo-com.png') ?>" alt="Logo">
        <div class="letter-kop-text">
          <strong>SMK Negeri 2 Pinrang</strong>
          <span>Community Programmer</span>
        </div>
      </div>
      <div class="letter-kop-rule"></div>

      <div class="cert-eyebrow">Sertifikat</div>
      <h3 class="cert-title" id="previewNamaSertifikat">Nama sertifikat akan tampil di sini</h3>

      <div class="letter-sign">
        <span>Diberikan oleh,</span>
        <div class="letter-sign-qr"><i class="ti ti-qrcode"></i></div>
        <strong id="previewSignerNameCert">Nama Penandatangan</strong>
        <span id="previewSignerRoleCert">Jabatan Penandatangan</span>
        <span id="previewDateCert" style="margin-top:4px;">—</span>
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
  const letterSurat = document.getElementById('letterSurat');
  const letterSertifikat = document.getElementById('letterSertifikat');

  const bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

  function setJenis(jenis) {
    jenisInput.value = jenis;
    tabs.forEach(t => t.classList.toggle('active', t.dataset.jenisTab === jenis));
    fieldsSurat.style.display = jenis === 'surat' ? 'block' : 'none';
    fieldsSertifikat.style.display = jenis === 'sertifikat' ? 'block' : 'none';
    letterSurat.style.display = jenis === 'surat' ? 'block' : 'none';
    letterSertifikat.style.display = jenis === 'sertifikat' ? 'block' : 'none';
    updatePreview();
  }

  function formatTanggal(tgl) {
    if (!tgl) return null;
    const d = new Date(tgl + 'T00:00:00');
    return d.getDate() + ' ' + bulanIndo[d.getMonth()] + ' ' + d.getFullYear();
  }

  function updatePreview() {
    const jenis = jenisInput.value;
    const tgl = document.getElementById('in-tanggal_ttd').value;
    const tglFormatted = formatTanggal(tgl);
    const signerName = document.getElementById('in-nama_penandatangan').value.trim() || 'Nama Penandatangan';
    const signerRole = document.getElementById('in-jabatan_penandatangan').value.trim() || 'Jabatan Penandatangan';

    if (jenis === 'surat') {
      const perihal = document.getElementById('in-perihal').value.trim();
      const nomor = document.getElementById('in-nomor_surat').value.trim();
      document.getElementById('previewPerihal').textContent = perihal || '—';
      document.getElementById('previewNomor').textContent = nomor || '—';
      document.getElementById('previewDate').textContent = 'Pinrang, ' + (tglFormatted || '—');
      document.getElementById('previewSignerNameSurat').textContent = signerName;
      document.getElementById('previewSignerRoleSurat').textContent = signerRole;
    } else {
      const nama = document.getElementById('in-nama_sertifikat').value.trim();
      document.getElementById('previewNamaSertifikat').textContent = nama || 'Nama sertifikat akan tampil di sini';
      document.getElementById('previewSignerNameCert').textContent = signerName;
      document.getElementById('previewSignerRoleCert').textContent = signerRole;
      document.getElementById('previewDateCert').textContent = 'Pinrang, ' + (tglFormatted || '—');
    }
  }

  tabs.forEach(t => t.addEventListener('click', () => setJenis(t.dataset.jenisTab)));
  document.getElementById('ttdForm').addEventListener('input', updatePreview);
  updatePreview();
})();
</script>