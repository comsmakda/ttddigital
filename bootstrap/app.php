<?php

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

// --- Load .env (kalau ada file .env, dipakai untuk dev lokal / non-docker) ---
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->safeLoad();
}

// --- BASE_PATH: dipakai kalau app di-deploy di subfolder (mis. /ttd) ---
// Default kosong (root domain). Bisa di-override lewat env APP_BASE_PATH.
define('BASE_PATH', rtrim(env('APP_BASE_PATH', ''), '/'));

// --- Error reporting ---
$debug = env('APP_DEBUG', 'false') === 'true';
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

// --- Timezone ---
date_default_timezone_set('Asia/Makassar');

// --- Session ---
$sessionName = env('SESSION_NAME', 'ttd_digital_session');
$lifetime = (int) env('SESSION_LIFETIME', 7200);

session_name($sessionName);
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => (($_SERVER['HTTPS'] ?? '') === 'on'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// --- Seed admin default kalau tabel admins masih kosong (sekali saja) ---
$seedFlag = ROOT_PATH . '/storage/.seeded';
if (!file_exists($seedFlag)) {
    try {
        if (\App\Models\Admin::count() === 0) {
            \App\Models\Admin::create(
                env('DEFAULT_ADMIN_USERNAME', 'admin'),
                env('DEFAULT_ADMIN_PASSWORD', 'admin123'),
                'Administrator'
            );
        }
        @file_put_contents($seedFlag, date('c'));
    } catch (\Throwable $e) {
        // DB mungkin belum siap (mis. saat container baru start) — biarkan,
        // akan dicoba lagi di request berikutnya karena flag belum dibuat.
        error_log('[SEED WARNING] ' . $e->getMessage());
    }
}
