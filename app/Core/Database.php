<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $name = env('DB_NAME', 'ttd_digital');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // Jangan bocorkan detail koneksi ke publik
                error_log('[DB CONNECTION ERROR] ' . $e->getMessage());
                http_response_code(500);
                if (env('APP_DEBUG', 'false') === 'true') {
                    die('Database connection failed: ' . $e->getMessage());
                }
                die('Terjadi kesalahan pada server. Silakan coba lagi nanti.');
            }
        }

        return self::$instance;
    }
}
