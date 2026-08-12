<?php
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Login') ?> — TTD Digital COM SMKN 2 Pinrang</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="icon" href="<?= asset('images/logo-com.png') ?>">
</head>
<body>

<div class="auth-wrap">
  <div class="auth-container">
    <div class="brand-panel">
      <div class="brand-panel-overlay"></div>
      <img src="<?= asset('images/logo-com.png') ?>" alt="Logo COM" class="brand-logo">
      <div class="brand-name">COM SMKN 2 Pinrang</div>
      <div class="brand-tagline">Sistem Tanda Tangan Digital &amp; Verifikasi QR Code resmi untuk surat dan sertifikat.</div>
    </div>

    <div class="auth-form-panel">
      <?php if ($flash): ?>
        <div class="alert <?= e($flash['type']) ?>">
          <i class="ti ti-<?= $flash['type'] === 'success' ? 'circle-check' : 'alert-circle' ?>"></i>
          <span><?= e($flash['message']) ?></span>
        </div>
      <?php endif; ?>
      <?= $content ?>
    </div>
  </div>
</div>

</body>
</html>
