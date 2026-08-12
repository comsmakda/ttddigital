<?php

namespace App\Core;

use App\Models\MainUser;

/**
 * Auth berbasis session yang memvalidasi kredensial langsung ke database
 * web utama COM SMKN 2 Pinrang (tabel `users`, role=admin, status=aktif).
 * Login bisa pakai email ATAU NIA. Karena tidak ada tabel lokal yang
 * disalin, perubahan password/email di web utama otomatis berlaku di sini.
 */
class Auth
{
    public static function attempt(string $identifier, string $password): bool
    {
        $user = MainUser::findActiveAdminByIdentifier($identifier);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['email'] ?: $user['nia'];
        $_SESSION['admin_nama'] = $user['nama_lengkap'];

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