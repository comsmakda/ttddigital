<?php

namespace App\Models;

use App\Core\Database;

class MainUser
{
    /**
     * Cari admin aktif di web utama berdasarkan email atau NIA.
     * Password TIDAK dicek di sini — verifikasi dilakukan di Auth::attempt().
     */
    public static function findActiveAdminByIdentifier(string $identifier): ?array
    {
        $stmt = Database::mainConnection()->prepare(
            'SELECT id, nia, nisn, nama_lengkap, email, email_alias, password_hash,
                    role, status, is_super_admin
             FROM users
             WHERE (email = ? OR email_alias = ? OR nia = ?)
               AND role = "admin"
               AND status = "aktif"
             LIMIT 1'
        );
        // parameter diulang 3x (bukan named placeholder ganda) karena
        // PDO::ATTR_EMULATE_PREPARES di-nonaktifkan di kelas Database
        $stmt->execute([$identifier, $identifier, $identifier]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}