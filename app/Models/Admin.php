<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Admin
{
    public static function findByUsername(string $username): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) AS c FROM admins')->fetch()['c'];
    }

    public static function create(string $username, string $password, string $nama = 'Administrator'): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO admins (username, password, nama) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), $nama]);
    }
}
