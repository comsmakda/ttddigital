<?php
$title = 'Buat TTD Baru';
$subtitle = 'Isi data dokumen, sistem akan membuat kode unik & QR Code otomatis.';
$selectedJenis = $jenis === 'sertifikat' ? 'sertifikat' : 'surat';
?>

<div class="card" style="max-width: 720px;">
  <div class="tab-bar">
    <button type="button" class="tab-btn <?= $selectedJenis === 'surat' ? 'active' : '' ?>" data-jenis-tab="surat">
      <i class="ti ti-mail"></i> Surat
    </button>
    <button type="button" class="tab-btn <?= $selectedJenis === 'sertifikat' ? 'active' : '' ?>" data-jenis-tab="sertifikat">
      <i class="ti ti-certificate"></i> Sertifikat
    </button>
  </div>

  <form method="POST" action="<?= url('/ttd/buat') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="jenis" id="jenis-input" value="<?= e($selectedJenis) ?>">

    <div id="fields-surat" style="display: <?= $selectedJenis === 'surat' ? 'block' : 'none' ?>;">
      <div class="field-group">
        <label class="field-label">Nomor Surat</label>
        <input type="text" name="nomor_surat" class="field-input" placeholder="mis. 421/123/SMKN2-PRG/2026" value="<?= old('nomor_surat') ?>">
      </div>
      <div class="field-group">
        <label class="field-label">Perihal</label>
        <input type="text" name="perihal" class="field-input" placeholder="mis. Surat Keterangan Aktif Siswa" value="<?= old('perihal') ?>">
      </div>
    </div>

    <div id="fields-sertifikat" style="display: <?= $selectedJenis === 'sertifikat' ? 'block' : 'none' ?>;">
      <div class="field-group">
        <label class="field-label">Nama Sertifikat</label>
        <input type="text" name="nama_sertifikat" class="field-input" placeholder="mis. Sertifikat Juara 1 Lomba Web Design" value="<?= old('nama_sertifikat') ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="field-group">
        <label class="field-label">Nama Penandatangan</label>
        <input type="text" name="nama_penandatangan" class="field-input" placeholder="mis. Drs. Ahmad, M.Pd." value="<?= old('nama_penandatangan') ?>" required>
      </div>
      <div class="field-group">
        <label class="field-label">Jabatan Penandatangan</label>
        <input type="text" name="jabatan_penandatangan" class="field-input" placeholder="mis. Kepala Sekolah" value="<?= old('jabatan_penandatangan') ?>" required>
      </div>
    </div>

    <div class="field-group">
      <label class="field-label">Tanggal TTD</label>
      <input type="date" name="tanggal_ttd" class="field-input" value="<?= old('tanggal_ttd', date('Y-m-d')) ?>" required>
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
