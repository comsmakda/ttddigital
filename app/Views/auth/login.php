<?php $title = 'Masuk'; ?>
<h1>Selamat Datang</h1>
<p class="sub">Masuk ke panel admin untuk mengelola TTD digital &amp; QR verifikasi.</p>

<form method="POST" action="<?= url('/login') ?>">
  <?= csrf_field() ?>

  <div class="field-group">
    <label class="field-label" for="username">Username</label>
    <input class="field-input" type="text" id="username" name="username" placeholder="admin" required autofocus>
  </div>

  <div class="field-group">
    <label class="field-label" for="password">Password</label>
    <input class="field-input" type="password" id="password" name="password" placeholder="••••••••" required>
  </div>

  <button type="submit" class="btn btn-primary btn-block">
    <i class="ti ti-login-2"></i> Masuk
  </button>
</form>
