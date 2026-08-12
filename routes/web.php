<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SignatureController;
use App\Controllers\VerifyController;
use App\Core\Router;

/** @var Router $router */

// --- Auth ---
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// --- Redirect root ke dashboard (kalau login) / login ---
$router->get('/', [DashboardController::class, 'index']);

// --- Dashboard (admin, butuh login) ---
$router->get('/dashboard', [DashboardController::class, 'index']);

// --- TTD / Signature management (admin) ---
$router->get('/ttd/buat', [SignatureController::class, 'showCreate']);
$router->post('/ttd/buat', [SignatureController::class, 'store']);
$router->get('/ttd/{id}', [SignatureController::class, 'show']);
$router->post('/ttd/{id}/batalkan', [SignatureController::class, 'revoke']);
$router->post('/ttd/{id}/aktifkan', [SignatureController::class, 'reactivate']);
$router->post('/ttd/{id}/hapus', [SignatureController::class, 'destroy']);

// --- QR image (di luar webroot, di-stream lewat controller) ---
$router->get('/qr/{kode}.png', [SignatureController::class, 'qrImage']);

// --- Verifikasi publik (tanpa login) ---
$router->get('/verify', [VerifyController::class, 'index']);
$router->get('/verify/{kode}', [VerifyController::class, 'show']);