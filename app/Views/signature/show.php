<?php
/* ttd_detail.php */
$title = 'Detail TTD';
$subtitle = $signature['kode_unik'];
$qrUrl = url('/qr/' . $signature['kode_unik'] . '.png');
$verifyUrl = url('/verify/' . $signature['kode_unik']);
?>

<div class="detail-grid">
  <div class="card">
    <div class="card-header">
      <h2>Informasi Dokumen</h2>
      <span class="badge <?= e($signature['status']) ?>"><?= $signature['status'] === 'aktif' ? 'Aktif' : 'Dibatalkan' ?></span>
    </div>

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
      <span class="label">Penandatangan</span>
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
    <div class="verify-row">
      <span class="label">Dibuat</span>
      <span class="value"><?= format_tanggal_indo($signature['created_at']) ?></span>
    </div>

    <?php if ($signature['status'] === 'dibatalkan' && !empty($signature['keterangan_pembatalan'])): ?>
      <div class="alert error detail-alert">
        <i class="ti ti-alert-circle"></i>
        <span>Alasan pembatalan: <?= e($signature['keterangan_pembatalan']) ?></span>
      </div>
    <?php endif; ?>

    <div class="detail-actions">
      <?php if ($signature['status'] === 'aktif'): ?>
        <form method="POST" action="<?= url('/ttd/' . $signature['id'] . '/batalkan') ?>" data-confirm="Yakin ingin membatalkan TTD ini? QR akan tampil 'Tidak Valid' saat di-scan." class="detail-actions-inline">
          <?= csrf_field() ?>
          <input type="text" name="keterangan" class="field-input" placeholder="Alasan pembatalan (opsional)">
          <button type="submit" class="btn btn-danger"><i class="ti ti-ban"></i> Batalkan TTD</button>
        </form>
      <?php else: ?>
        <form method="POST" action="<?= url('/ttd/' . $signature['id'] . '/aktifkan') ?>" data-confirm="Aktifkan kembali TTD ini?">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-primary"><i class="ti ti-refresh"></i> Aktifkan Kembali</button>
        </form>
      <?php endif; ?>
    </div>

    <div class="detail-danger-zone">
      <div class="detail-danger-zone-text">
        <strong>Hapus Permanen</strong>
        <span>Gunakan ini jika TTD dibuat salah dan ingin dihapus sepenuhnya dari data. Tindakan ini tidak bisa dibatalkan.</span>
      </div>
      <form method="POST" action="<?= url('/ttd/' . $signature['id'] . '/hapus') ?>" onsubmit="return confirm('Yakin ingin menghapus TTD ini secara PERMANEN? Data tidak bisa dikembalikan.');">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-ghost-danger"><i class="ti ti-trash"></i> Hapus Permanen</button>
      </form>
    </div>
  </div>

  <div class="card qr-box">
    <div class="card-header qr-box-header"><h2>QR Code</h2></div>
    <img src="<?= e($qrUrl) ?>" alt="QR Code <?= e($signature['kode_unik']) ?>">
    <a href="<?= e($qrUrl) ?>" download="<?= e($signature['kode_unik']) ?>.png" class="btn btn-primary btn-block btn-sm">
      <i class="ti ti-download"></i> Download PNG
    </a>
    <a href="<?= e($verifyUrl) ?>" target="_blank" class="btn btn-outline btn-block btn-sm">
      <i class="ti ti-external-link"></i> Lihat Halaman Verifikasi
    </a>
  </div>
</div>