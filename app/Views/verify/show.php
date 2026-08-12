<?php
/* verify_result.php */
$title = 'Verifikasi TTD';

if (!$signature) {
    $status = 'invalid';
    $icon = 'ti-help-circle';
    $heading = 'Tidak Ditemukan';
    $desc = 'Kode yang di-scan tidak terdaftar dalam sistem. Pastikan QR Code berasal dari dokumen resmi COM SMKN 2 Pinrang.';
} elseif ($signature['status'] === 'dibatalkan') {
    $status = 'revoked';
    $icon = 'ti-ban';
    $heading = 'Dibatalkan';
    $desc = 'Dokumen ini pernah ditandatangani secara resmi, namun tanda tangan digitalnya telah dibatalkan dan tidak berlaku lagi.';
} else {
    $status = 'valid';
    $icon = 'ti-shield-check';
    $heading = 'Sah & Terverifikasi';
    $desc = 'Dokumen ini ditandatangani secara resmi oleh COM SMKN 2 Pinrang dan tercatat sah dalam sistem.';
}
?>

<div class="verify-wrap">
  <div class="verify-sheet">

    <div class="verify-watermark">
      <img src="<?= asset('images/logo-com.png') ?>" alt="">
    </div>

    <div class="verify-letterhead">
      <img class="verify-letterhead-logo" src="<?= asset('images/logo-com.png') ?>" alt="Logo Community Programmer SMKN 2 Pinrang">
      <div class="verify-letterhead-text">
        <span class="verify-letterhead-eyebrow">Community Programmer</span>
        <span class="verify-letterhead-org">SMK Negeri 2 Pinrang</span>
        <span class="verify-letterhead-unit">Sistem Tanda Tangan Digital &middot; Kab. Pinrang, Sulawesi Selatan</span>
      </div>
    </div>
    <div class="verify-rule"></div>

    <div class="verify-title"><span>Hasil Verifikasi Dokumen</span></div>

    <div class="verify-stamp-row">
      <div class="verify-stamp verify-stamp-<?= $status ?>">
        <i class="ti <?= $icon ?>"></i>
        <span><?= $heading ?></span>
      </div>
    </div>
    <p class="verify-desc"><?= $desc ?></p>

    <?php if ($signature): ?>
      <table class="verify-table">
        <tr>
          <td class="vlabel">Kode Unik</td>
          <td class="vvalue kode-mono"><?= e($signature['kode_unik']) ?></td>
        </tr>
        <tr>
          <td class="vlabel">Jenis Dokumen</td>
          <td class="vvalue"><?= $signature['jenis'] === 'surat' ? 'Surat' : 'Sertifikat' ?></td>
        </tr>
        <?php if ($signature['jenis'] === 'surat'): ?>
          <tr>
            <td class="vlabel">Nomor Surat</td>
            <td class="vvalue"><?= e($signature['nomor_surat']) ?></td>
          </tr>
          <tr>
            <td class="vlabel">Perihal</td>
            <td class="vvalue"><?= e($signature['perihal']) ?></td>
          </tr>
        <?php else: ?>
          <tr>
            <td class="vlabel">Nama Sertifikat</td>
            <td class="vvalue"><?= e($signature['nama_sertifikat']) ?></td>
          </tr>
        <?php endif; ?>
        <tr>
          <td class="vlabel">Ditandatangani Oleh</td>
          <td class="vvalue"><?= e($signature['nama_penandatangan']) ?></td>
        </tr>
        <tr>
          <td class="vlabel">Jabatan</td>
          <td class="vvalue"><?= e($signature['jabatan_penandatangan']) ?></td>
        </tr>
        <tr>
          <td class="vlabel">Tanggal TTD</td>
          <td class="vvalue"><?= format_tanggal_indo($signature['tanggal_ttd']) ?></td>
        </tr>
      </table>
    <?php else: ?>
      <table class="verify-table">
        <tr>
          <td class="vlabel">Kode yang Dicek</td>
          <td class="vvalue kode-mono"><?= e($kode) ?></td>
        </tr>
      </table>
    <?php endif; ?>

    <div class="verify-footnote">
      <p>Dokumen elektronik ini diterbitkan dan dapat diverifikasi keabsahannya melalui Sistem Tanda Tangan Digital resmi COM SMKN 2 Pinrang. Verifikasi ulang dapat dilakukan kapan saja melalui QR Code atau kode unik pada dokumen.</p>
      <div class="verify-footnote-meta">
        <span>Ref: <?= e($signature['kode_unik'] ?? ($kode ?? '-')) ?></span>
        <span>Diverifikasi: <?= date('d M Y, H:i') ?> WITA</span>
      </div>
    </div>

  </div>

  <p class="verify-outside-note">Sistem Tanda Tangan Digital &middot; COM SMKN 2 Pinrang</p>
</div>