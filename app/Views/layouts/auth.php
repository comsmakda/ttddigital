<?php
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Login') ?> — TTD Digital COM SMKN 2 Pinrang</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="icon" href="<?= asset('images/logo-com.png') ?>">

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --c-page:#eef2f6;--c-white:#ffffff;--c-ink:#0f172a;--c-muted:#64748b;--c-muted2:#94a3b8;--c-border:#e6ebf1;
  --c-primary:#0e7490;--c-primary-dk:#0b5a70;--c-primary-lt:#06b6d4;
  --c-red-bg:#fef2f2;--c-red-border:#fecaca;--c-red-text:#b91c1c;
  --c-green-bg:#f0fdf4;--c-green-border:#bbf7d0;--c-green-text:#15803d;
  --radius-sm:9px;--radius-md:13px;--radius-lg:22px;
}
html,body{height:100%;font-family:'Plus Jakarta Sans',sans-serif;background:var(--c-page);color:var(--c-ink)}

.page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2.2rem 1.5rem}

.auth-shell{
  width:100%;max-width:980px;height:min(88vh,640px);
  background:var(--c-white);border-radius:var(--radius-lg);
  box-shadow:0 30px 70px -20px rgba(15,23,42,.28),0 4px 18px rgba(15,23,42,.06);
  display:grid;grid-template-columns:1.15fr 1fr;overflow:hidden;position:relative;
}

.auth-left{overflow-y:auto;overflow-x:hidden;padding:3rem 3.2rem;display:flex;flex-direction:column;justify-content:center}
.auth-left::-webkit-scrollbar{width:7px}
.auth-left::-webkit-scrollbar-thumb{background:#dbe3ec;border-radius:10px}

.login-title{font-size:2.1rem;font-weight:800;color:var(--c-primary-dk);letter-spacing:-.03em;margin-bottom:.35rem}
.sub{font-size:.87rem;color:var(--c-muted);margin-bottom:1.6rem;line-height:1.7}

.alert{
  display:flex;align-items:flex-start;gap:10px;border-radius:var(--radius-md);
  padding:11px 14px;font-size:.82rem;font-weight:500;margin-bottom:1rem;animation:slideIn .22s ease;
}
.alert i{font-size:16px;margin-top:1px;flex-shrink:0}
.alert.error{background:var(--c-red-bg);color:var(--c-red-text);border:1px solid var(--c-red-border)}
.alert.success{background:var(--c-green-bg);color:var(--c-green-text);border:1px solid var(--c-green-border)}
@keyframes slideIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}

.field-group{margin-bottom:1.1rem}
.field-label{display:block;font-size:.78rem;font-weight:700;color:var(--c-ink);margin-bottom:6px}
.field-wrap{position:relative}
.field-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:17px;color:var(--c-muted2);pointer-events:none;transition:color .18s}
.field-wrap:focus-within .field-icon{color:var(--c-primary)}
.field-input{
  width:100%;padding:12px 42px 12px 15px;background:#fbfcfe;
  border:1.5px solid var(--c-border);border-radius:var(--radius-sm);
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.9rem;color:var(--c-ink);
  outline:none;transition:border .16s,box-shadow .16s,background .16s;
}
.field-input:focus{border-color:var(--c-primary-lt);box-shadow:0 0 0 3px rgba(6,182,212,.12);background:#fff}
.field-input::placeholder{color:var(--c-muted2)}
.eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:var(--c-muted2);font-size:17px;line-height:1;transition:color .15s}
.eye-btn:hover{color:var(--c-primary)}

.btn-block{width:100%}
.btn-primary{
  padding:13px;background:var(--c-primary);color:#fff;border:none;
  border-radius:var(--radius-sm);font-family:'Plus Jakarta Sans',sans-serif;
  font-size:.9rem;font-weight:800;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:9px;
  transition:background .18s,transform .12s,box-shadow .18s;
  box-shadow:0 8px 22px rgba(14,116,144,.25);
}
.btn-primary:hover:not(:disabled){background:var(--c-primary-lt);transform:translateY(-2px);box-shadow:0 12px 28px rgba(6,182,212,.3)}
.btn-primary:active:not(:disabled){transform:scale(.985)}
.btn-primary:disabled{opacity:.55;cursor:not-allowed}
.btn-primary i{font-size:18px}
.btn-primary .spin{display:none;animation:spinAnim .65s linear infinite}
@keyframes spinAnim{to{transform:rotate(360deg)}}

@keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-4px)}40%{transform:translateX(4px)}60%{transform:translateX(-2px)}80%{transform:translateX(2px)}}
.shake{animation:shake .28s ease}

.brand-panel{
  position:relative;overflow:hidden;
  background:linear-gradient(165deg,var(--c-primary-dk) 0%,#0a3a4c 100%);
  display:flex;flex-direction:column;justify-content:flex-end;padding:2.4rem 2.2rem;
}
.brand-panel-img{
  position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;
  opacity:0;transition:opacity .5s ease;z-index:0;
}
.brand-panel-img.is-loaded{opacity:1}
.brand-panel-overlay{
  position:absolute;inset:0;z-index:1;pointer-events:none;
  background:linear-gradient(175deg,rgba(9,25,38,.35) 0%,rgba(9,45,64,.55) 45%,rgba(6,30,44,.88) 100%);
}
.brand-panel-glow{
  position:absolute;inset:0;z-index:1;pointer-events:none;
  background:radial-gradient(ellipse 90% 70% at 100% 100%,rgba(6,182,212,.18) 0%,transparent 60%);
}
.brand-logo{position:relative;z-index:2;max-width:76px;max-height:76px;width:auto;height:auto;object-fit:contain;margin-bottom:1.1rem;filter:drop-shadow(0 3px 10px rgba(0,0,0,.35))}
.brand-name{position:relative;z-index:2;font-size:1.5rem;font-weight:800;color:#fff;line-height:1.25;letter-spacing:-.02em;text-shadow:0 2px 18px rgba(0,0,0,.25)}
.brand-tagline{position:relative;z-index:2;font-size:.78rem;color:rgba(255,255,255,.75);margin-top:.6rem;line-height:1.6;max-width:280px}

@media (max-width:860px){
  .page{padding:0;align-items:stretch}
  .auth-shell{max-width:100%;height:auto;min-height:100vh;border-radius:0;grid-template-columns:1fr;grid-template-rows:auto 1fr}
  .brand-panel{order:-1;padding:2rem 1.5rem 1.6rem;min-height:170px}
  .brand-name{font-size:1.25rem}
  .brand-logo{max-width:58px;max-height:58px}
  .auth-left{overflow-y:visible;padding:1.9rem 1.4rem 2.2rem}
  .login-title{font-size:1.6rem}
}
</style>
</head>
<body>

<div class="page">
  <div class="auth-shell">

    <div class="auth-left">
      <?php if ($flash): ?>
        <div class="alert <?= e($flash['type']) ?>">
          <i class="ti ti-<?= $flash['type'] === 'success' ? 'circle-check' : 'alert-circle' ?>"></i>
          <span><?= e($flash['message']) ?></span>
        </div>
      <?php endif; ?>
      <?= $content ?>
    </div>

    <div class="brand-panel">
      <img
        class="brand-panel-img"
        src="<?= asset('images/gedung-smkn2.webp') ?>"
        alt=""
        loading="eager"
        decoding="async"
        fetchpriority="high"
        onload="this.classList.add('is-loaded')"
        onerror="this.style.display='none'"
      >
      <div class="brand-panel-overlay"></div>
      <div class="brand-panel-glow"></div>

      <img src="<?= asset('images/logo-com.png') ?>" alt="Logo COM" class="brand-logo"
           loading="eager" decoding="async" onerror="this.style.display='none'">
      <div class="brand-name">COM SMKN 2 Pinrang</div>
      <div class="brand-tagline">Sistem Tanda Tangan Digital &amp; Verifikasi QR Code resmi untuk surat dan sertifikat.</div>
    </div>

  </div>
</div>

<script>
(function(){
  'use strict';
  var img = document.querySelector('.brand-panel-img');
  if (img && img.complete && img.naturalWidth > 0) img.classList.add('is-loaded');

  document.querySelectorAll('.eye-btn[data-for]').forEach(function(btn){
    btn.addEventListener('click', function(){
      var inp = document.getElementById(btn.dataset.for); if (!inp) return;
      var show = inp.type === 'password'; inp.type = show ? 'text' : 'password';
      btn.querySelector('i').className = show ? 'ti ti-eye-off' : 'ti ti-eye';
      inp.focus();
    });
  });

  var frm = document.getElementById('login-form');
  if (frm) frm.addEventListener('submit', function(){
    var btn = frm.querySelector('.btn-primary'),
        spin = frm.querySelector('.spin'),
        ico = frm.querySelector('.btn-ico'),
        tx = frm.querySelector('.btn-tx');
    if (btn) btn.disabled = true;
    if (spin) spin.style.display = 'inline-block';
    if (ico) ico.style.display = 'none';
    if (tx) tx.textContent = 'Memproses…';
  });
}());
</script>
</body>
</html>