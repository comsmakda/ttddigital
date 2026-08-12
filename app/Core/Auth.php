<?php

namespace App\Core;

use App\Models\Admin;

/**
 * Auth sederhana berbasis session + tabel `admins` di database.
 *
 * CATATAN UNTUK INTEGRASI DI MASA DEPAN:
 * Saat ini login memeriksa tabel `admins` lokal (di-seed otomatis dengan
 * admin/admin123 saat pertama kali jalan). Ketika nanti sistem ini perlu
 * "nyambung" ke web utama COM SMKN 2 Pinrang, cukup ganti isi method
 * attempt() di bawah ini agar memvalidasi ke session/API web utama,
 * tanpa perlu mengubah controller atau tabel `signatures`.
 */
class Auth
{
    public static function attempt(string $username, string $password): bool
    {
        $admin = Admin::findByUsername($username);

        if (!$admin || !password_verify($password, $admin['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_nama'] = $admin['nama'];

        return true;
    }

    public static function check(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'nama' => $_SESSION['admin_nama'] ?? $_SESSION['admin_username'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function checkCsrf(string $token): bool
    {
        return !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }
}
