<?php
/* layout.php */
use App\Core\Auth;

$admin = Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(BASE_PATH, '/');
if ($basePath !== '' && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath));
}
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Dashboard') ?> — TTD Digital COM SMKN 2 Pinrang</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="icon" href="<?= asset('images/logo-com.png') ?>">
</head>
<body>

<div class="app-shell">
  <div class="sidebar-overlay" data-menu-toggle></div>

  <aside class="sidebar" id="appSidebar">
    <div class="sidebar-brand">
      <div class="brand-seal">
        <img src="<?= asset('images/logo-com.png') ?>" alt="Logo COM" width="30" height="30">
      </div>
      <div class="sidebar-brand-text">
        <strong>TTD Digital</strong>
        <span>COM SMKN 2 Pinrang</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <a href="<?= url('/dashboard') ?>" class="<?= $currentPath === '/dashboard' || $currentPath === '/' ? 'active' : '' ?>">
        <i class="ti ti-layout-dashboard"></i> Dashboard
      </a>
      <a href="<?= url('/ttd/buat') ?>" class="<?= str_starts_with($currentPath, '/ttd/buat') ? 'active' : '' ?>">
        <i class="ti ti-qrcode"></i> Buat TTD Baru
      </a>
      <a href="<?= url('/verify') ?>" target="_blank">
        <i class="ti ti-shield-check"></i> Halaman Verifikasi
      </a>
    </nav>

    <div class="sidebar-footer">
      <span class="admin-name"><?= e($admin['nama'] ?? $admin['username'] ?? 'Admin') ?></span>
      Masuk sebagai <?= e($admin['username'] ?? '-') ?>
      <br>
      <a href="<?= url('/logout') ?>" class="logout-link"><i class="ti ti-logout-2"></i> Keluar</a>
    </div>
  </aside>

  <main class="main-content">
    <div class="topbar">
      <div style="display:flex; align-items:center; gap:.9rem;">
        <button class="menu-toggle" data-menu-toggle aria-label="Menu"><i class="ti ti-menu-2"></i></button>
        <div>
          <h1><?= e($title ?? 'Dashboard') ?></h1>
          <?php if (!empty($subtitle)): ?><p><?= e($subtitle) ?></p><?php endif; ?>
        </div>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert <?= e($flash['type']) ?>">
        <i class="ti ti-<?= $flash['type'] === 'success' ? 'circle-check' : ($flash['type'] === 'error' ? 'alert-circle' : 'info-circle') ?>"></i>
        <span><?= e($flash['message']) ?></span>
      </div>
    <?php endif; ?>

    <?= $content ?>
  </main>
</div>

<script src="<?= asset('js/app.js') ?>"></script>
<script>
  document.querySelectorAll('[data-menu-toggle]').forEach(function (el) {
    el.addEventListener('click', function () {
      document.getElementById('appSidebar').classList.toggle('sidebar-open');
      document.querySelector('.sidebar-overlay').classList.toggle('show');
    });
  });
</script>
</body>
</html>