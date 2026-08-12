<?php

use App\Core\Auth;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return env($key, $default);
    }
}

if (!function_exists('base_url')) {
    function base_url(): string
    {
        $url = rtrim(env('APP_URL', ''), '/');
        return $url . rtrim(BASE_PATH, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = '/' . ltrim($path, '/');
        return base_url() . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        return e($_SESSION['_old'][$key] ?? $default);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(Auth::csrfToken()) . '">';
    }
}

if (!function_exists('flash_get')) {
    function flash_get(): ?array
    {
        if (empty($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
}

if (!function_exists('format_tanggal_indo')) {
    function format_tanggal_indo(?string $date): string
    {
        if (!$date) {
            return '-';
        }
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $ts = strtotime($date);
        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
}
