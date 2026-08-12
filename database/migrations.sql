-- ============================================================
-- TTD Digital - COM SMKN 2 Pinrang
-- Skema database awal
-- Catatan: tabel `admins` sengaja dibuat sederhana (username/password)
-- karena login saat ini masih mandiri (hardcoded seed). Nanti saat
-- terhubung ke web utama COM, tabel/logic ini tinggal diganti jadi
-- pengecekan session/API ke sistem utama tanpa mengubah tabel signatures.
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+08:00';

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL DEFAULT 'Administrator',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS signatures (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_unik VARCHAR(40) NOT NULL UNIQUE,
    jenis ENUM('surat', 'sertifikat') NOT NULL,

    -- Dipakai untuk jenis = surat
    nomor_surat VARCHAR(150) NULL,
    perihal VARCHAR(255) NULL,

    -- Dipakai untuk jenis = sertifikat
    nama_sertifikat VARCHAR(255) NULL,

    nama_penandatangan VARCHAR(150) NOT NULL,
    jabatan_penandatangan VARCHAR(150) NOT NULL,
    tanggal_ttd DATE NOT NULL,

    status ENUM('aktif', 'dibatalkan') NOT NULL DEFAULT 'aktif',
    keterangan_pembatalan VARCHAR(255) NULL,

    qr_path VARCHAR(255) NULL,

    created_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_signatures_admin FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_kode_unik (kode_unik),
    INDEX idx_jenis (jenis),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
