<?php
/* verify_result.php */
$title = 'Verifikasi TTD';

if (!$signature) {
    $status = 'invalid';
    $icon = 'ti-help-circle';
    $heading = 'TTD Tidak Ditemukan';
    $desc = 'Kode yang di-scan tidak terdaftar dalam sistem. Pastikan QR Code berasal dari dokumen resmi COM SMKN 2 Pinrang.';
} elseif ($signature['status'] === 'dibatalkan') {
    $status = 'revoked';
    $icon = 'ti-ban';
    $heading = 'TTD Sudah Dibatalkan';
    $desc = 'Dokumen ini pernah ditandatangani, namun TTD-nya sudah tidak berlaku lagi.';
} else {
    $status = 'valid';
    $icon = 'ti-shield-check';
    $heading = 'TTD Sah & Terverifikasi';
    $desc = 'Dokumen ini ditandatangani secara resmi oleh COM SMKN 2 Pinrang.';
}
?>

<div class="verify-wrap">
  <div class="verify-card">
    <div class="verify-status-band <?= $status ?>">
      <i class="ti <?= $icon ?>"></i>
      <h2><?= $heading ?></h2>
      <p><?= $desc ?></p>
    </div>

    <div class="verify-body">
      <?php if ($signature): ?>
        <div class="verify-row">
          <span class="label">Kode Unik</span>
          <span class="value kode-mono"><?= e($signature['kode_unik']) ?></span>
        </div>
        <div class="verify-row">
          <span class="label">Jenis Dokumen</span>
          <span class="value"><?= $signature['jenis'] === 'surat' ? 'Surat' : 'Sertifikat' ?></span>
        </div>
        <?php if ($signature['jenis'] === 'surat'): ?>
          <div class="verify-row">
            <span class="label">Nomor Surat</span>
            <span class="value"><?= e($signature['nomor_surat']) ?></span>
          </div>
          <div class="verify-row">
            <span class="label">Perihal</span>
            <span class="value"><?= e($signature['perihal']) ?></span>
          </div>
        <?php else: ?>
          <div class="verify-row">
            <span class="label">Nama Sertifikat</span>
            <span class="value"><?= e($signature['nama_sertifikat']) ?></span>
          </div>
        <?php endif; ?>
        <div class="verify-row">
          <span class="label">Ditandatangani Oleh</span>
          <span class="value"><?= e($signature['nama_penandatangan']) ?></span>
        </div>
        <div class="verify-row">
          <span class="label">Jabatan</span>
          <span class="value"><?= e($signature['jabatan_penandatangan']) ?></span>
        </div>
        <div class="verify-row">
          <span class="label">Tanggal TTD</span>
          <span class="value"><?= format_tanggal_indo($signature['tanggal_ttd']) ?></span>
        </div>
      <?php else: ?>
        <div class="verify-row">
          <span class="label">Kode yang dicek</span>
          <span class="value kode-mono"><?= e($kode) ?></span>
        </div>
      <?php endif; ?>

      <div class="verify-footer-brand">
        <img src="<?= asset('images/logo-com.png') ?>" alt="Logo COM">
        <span>Community Programmer &middot; SMKN 2 Pinrang</span>
      </div>
    </div>
  </div>
</div>