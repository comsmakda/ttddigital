<?php $title = 'Masuk'; ?>
<h1 class="login-title">Selamat Datang</h1>
<p class="sub">Masuk ke panel admin untuk mengelola TTD digital &amp; QR verifikasi.</p>

<form method="POST" action="<?= url('/login') ?>" id="login-form" novalidate>
  <?= csrf_field() ?>

  <div class="field-group">
    <label class="field-label" for="username">Username</label>
    <div class="field-wrap">
      <input class="field-input" type="text" id="username" name="username"
             placeholder="admin" required autofocus autocomplete="username">
      <i class="ti ti-user field-icon"></i>
    </div>
  </div>

  <div class="field-group">
    <label class="field-label" for="password">Password</label>
    <div class="field-wrap">
      <input class="field-input" type="password" id="password" name="password"
             placeholder="••••••••" required autocomplete="current-password">
      <button type="button" class="eye-btn" data-for="password" aria-label="Tampilkan atau sembunyikan kata sandi">
        <i class="ti ti-eye"></i>
      </button>
    </div>
  </div>

  <button type="submit" class="btn-primary btn-block">
    <i class="ti ti-refresh spin"></i>
    <i class="ti ti-login-2 btn-ico"></i>
    <span class="btn-tx">Masuk</span>
  </button>
</form>