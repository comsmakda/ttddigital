<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Signature
{
    /**
     * Ambil daftar TTD dengan filter opsional (pencarian & status) + pagination.
     */
    public static function paginate(int $page = 1, int $perPage = 10, string $search = '', string $status = '', string $jenis = ''): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(kode_unik LIKE ? OR nomor_surat LIKE ? OR perihal LIKE ? OR nama_sertifikat LIKE ? OR nama_penandatangan LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like, $like);
        }
        if ($status !== '' && in_array($status, ['aktif', 'dibatalkan'], true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        if ($jenis !== '' && in_array($jenis, ['surat', 'sertifikat'], true)) {
            $where[] = 'jenis = ?';
            $params[] = $jenis;
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $db = Database::connection();

        $countStmt = $db->prepare("SELECT COUNT(*) AS c FROM signatures {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['c'];

        $sql = "SELECT * FROM signatures {$whereSql} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public static function stats(): array
    {
        $db = Database::connection();
        $row = $db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'aktif') AS aktif,
                SUM(status = 'dibatalkan') AS dibatalkan,
                SUM(jenis = 'surat') AS surat,
                SUM(jenis = 'sertifikat') AS sertifikat
             FROM signatures"
        )->fetch();

        return [
            'total' => (int) ($row['total'] ?? 0),
            'aktif' => (int) ($row['aktif'] ?? 0),
            'dibatalkan' => (int) ($row['dibatalkan'] ?? 0),
            'surat' => (int) ($row['surat'] ?? 0),
            'sertifikat' => (int) ($row['sertifikat'] ?? 0),
        ];
    }

    public static function findByKode(string $kode): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM signatures WHERE kode_unik = ? LIMIT 1');
        $stmt->execute([$kode]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM signatures WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function kodeExists(string $kode): bool
    {
        $stmt = Database::connection()->prepare('SELECT 1 FROM signatures WHERE kode_unik = ? LIMIT 1');
        $stmt->execute([$kode]);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO signatures
                (kode_unik, jenis, nomor_surat, perihal, nama_sertifikat, nama_penandatangan, jabatan_penandatangan, tanggal_ttd, status, created_by)
             VALUES
                (:kode_unik, :jenis, :nomor_surat, :perihal, :nama_sertifikat, :nama_penandatangan, :jabatan_penandatangan, :tanggal_ttd, "aktif", :created_by)'
        );
        $stmt->execute([
            'kode_unik' => $data['kode_unik'],
            'jenis' => $data['jenis'],
            'nomor_surat' => $data['nomor_surat'] ?? null,
            'perihal' => $data['perihal'] ?? null,
            'nama_sertifikat' => $data['nama_sertifikat'] ?? null,
            'nama_penandatangan' => $data['nama_penandatangan'],
            'jabatan_penandatangan' => $data['jabatan_penandatangan'],
            'tanggal_ttd' => $data['tanggal_ttd'],
            'created_by' => $data['created_by'] ?? null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function updateQrPath(int $id, string $qrPath): void
    {
        $stmt = Database::connection()->prepare('UPDATE signatures SET qr_path = ? WHERE id = ?');
        $stmt->execute([$qrPath, $id]);
    }

    public static function revoke(int $id, string $keterangan = ''): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE signatures SET status = "dibatalkan", keterangan_pembatalan = ? WHERE id = ?'
        );
        $stmt->execute([$keterangan, $id]);
    }

    public static function reactivate(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE signatures SET status = "aktif", keterangan_pembatalan = NULL WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    /**
     * Hapus permanen baris TTD dari database.
     */
    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM signatures WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Buat kode unik yang belum pernah dipakai.
     */
    public static function generateUniqueKode(): string
    {
        do {
            // Format: TTD-XXXXXXXX (8 karakter alfanumerik kapital, mudah dibaca)
            $kode = 'TTD-' . strtoupper(bin2hex(random_bytes(4)));
        } while (self::kodeExists($kode));

        return $kode;
    }
}