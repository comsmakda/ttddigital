<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static array $instances = [];

    /**
     * Koneksi ke database aplikasi ini sendiri (ttd_digital).
     */
    public static function connection(): PDO
    {
        return self::get('default');
    }

    /**
     * Koneksi ke database web utama (com_smkn2_pinrang).
     * Dipakai untuk validasi login admin & data anggota.
     */
    public static function mainConnection(): PDO
    {
        return self::get('main');
    }

    private static function get(string $name): PDO
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = self::connect($name);
        }
        return self::$instances[$name];
    }

    private static function connect(string $name): PDO
    {
        if ($name === 'main') {
            // Fallback ke kredensial DB utama (DB_USER/DB_PASS) jika
            // DB_MAIN_USER/DB_MAIN_PASS tidak diset — berguna kalau satu
            // user MySQL sudah punya akses ke kedua database (umum di
            // shared hosting/cPanel).
            $host = env('DB_MAIN_HOST', env('DB_HOST', '127.0.0.1'));
            $port = env('DB_MAIN_PORT', env('DB_PORT', '3306'));
            $dbname = env('DB_MAIN_NAME', 'com_smkn2_pinrang');
            $user = env('DB_MAIN_USER', env('DB_USER', 'root'));
            $pass = env('DB_MAIN_PASS', env('DB_PASS', ''));
        } else {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $dbname = env('DB_NAME', 'ttd_digital');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("[DB CONNECTION ERROR:{$name}] " . $e->getMessage());
            http_response_code(500);
            if (env('APP_DEBUG', 'false') === 'true') {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('Terjadi kesalahan pada server. Silakan coba lagi nanti.');
        }
    }
}   