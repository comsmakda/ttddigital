<?php
/* dashboard.php */
$title = 'Dashboard';
$subtitle = 'Daftar seluruh TTD digital yang pernah dibuat.';
$items = $result['items'];
?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon"><i class="ti ti-qrcode"></i></div>
    <div>
      <div class="stat-value"><?= $stats['total'] ?></div>
      <div class="stat-label">Total TTD</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green"><i class="ti ti-circle-check"></i></div>
    <div>
      <div class="stat-value"><?= $stats['aktif'] ?></div>
      <div class="stat-label">Aktif</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red"><i class="ti ti-ban"></i></div>
    <div>
      <div class="stat-value"><?= $stats['dibatalkan'] ?></div>
      <div class="stat-label">Dibatalkan</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber"><i class="ti ti-certificate"></i></div>
    <div>
      <div class="stat-value"><?= $stats['sertifikat'] ?></div>
      <div class="stat-label">Sertifikat</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Daftar TTD</h2>
    <a href="<?= url('/ttd/buat') ?>" class="btn btn-primary btn-sm">
      <i class="ti ti-plus"></i> Buat TTD Baru
    </a>
  </div>

  <form method="GET" action="<?= url('/dashboard') ?>" class="filter-bar">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari kode, nomor surat, perihal, nama..." class="field-input filter-search">
    <select name="jenis" class="field-select">
      <option value="">Semua Jenis</option>
      <option value="surat" <?= $jenis === 'surat' ? 'selected' : '' ?>>Surat</option>
      <option value="sertifikat" <?= $jenis === 'sertifikat' ? 'selected' : '' ?>>Sertifikat</option>
    </select>
    <select name="status" class="field-select">
      <option value="">Semua Status</option>
      <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
      <option value="dibatalkan" <?= $status === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
    </select>
    <button type="submit" class="btn btn-outline"><i class="ti ti-search"></i> Cari</button>
  </form>

  <?php if (empty($items)): ?>
    <div class="empty-state">
      <i class="ti ti-file-off"></i>
      Belum ada TTD yang cocok dengan pencarian.
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Jenis</th>
            <th>Perihal / Nama Sertifikat</th>
            <th>Penandatangan</th>
            <th>Tanggal TTD</th>
            <th>Status</th>
            <th class="col-actions"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $row): ?>
            <tr>
              <td><span class="kode-mono"><?= e($row['kode_unik']) ?></span></td>
              <td>
                <span class="badge jenis-<?= e($row['jenis']) ?>">
                  <?= $row['jenis'] === 'surat' ? 'Surat' : 'Sertifikat' ?>
                </span>
              </td>
              <td><?= e($row['jenis'] === 'surat' ? ($row['perihal'] ?? '-') : ($row['nama_sertifikat'] ?? '-')) ?></td>
              <td><?= e($row['nama_penandatangan']) ?></td>
              <td><?= format_tanggal_indo($row['tanggal_ttd']) ?></td>
              <td><span class="badge <?= e($row['status']) ?>"><?= $row['status'] === 'aktif' ? 'Aktif' : 'Dibatalkan' ?></span></td>
              <td>
                <div class="row-actions">
                  <a href="<?= url('/ttd/' . $row['id']) ?>" class="btn btn-outline btn-sm">Detail</a>
                  <?php if ($row['status'] === 'aktif'): ?>
                    <form method="POST" action="<?= url('/ttd/' . $row['id'] . '/batalkan') ?>" onsubmit="return confirm('Yakin ingin membatalkan TTD ini?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-ban"></i> Batalkan</button>
                    </form>
                  <?php endif; ?>
                  <form method="POST" action="<?= url('/ttd/' . $row['id'] . '/hapus') ?>" onsubmit="return confirm('Yakin ingin menghapus TTD ini secara PERMANEN? Data tidak bisa dikembalikan.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost-danger btn-sm" title="Hapus permanen (mis. salah input)">
                      <i class="ti ti-trash"></i> Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($result['total_pages'] > 1): ?>
      <div class="pagination">
        <?php for ($p = 1; $p <= $result['total_pages']; $p++): ?>
          <?php if ($p === $result['page']): ?>
            <span class="current"><?= $p ?></span>
          <?php else: ?>
            <a href="<?= url('/dashboard') ?>?page=<?= $p ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&jenis=<?= urlencode($jenis) ?>"><?= $p ?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>