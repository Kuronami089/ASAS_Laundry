-- ============================================================================
-- SISTEM INFORMASI MANAJEMEN LAUNDRY (ASAS Laundry)
-- Skema Database Relasional MySQL / MariaDB
-- Tabel: users, layanan, transaksi (dengan Foreign Key Constraint)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS db_laundry
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE db_laundry;

-- Hapus tabel lama (urutan dari tabel anak ke induk agar aman terhadap FK)
DROP TABLE IF EXISTS transaksi;
DROP TABLE IF EXISTS layanan;
DROP TABLE IF EXISTS users;

-- ----------------------------------------------------------------------------
-- 1. Tabel users: menyimpan akun petugas berdasarkan hak akses (role)
-- ----------------------------------------------------------------------------
CREATE TABLE users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap VARCHAR(100) NOT NULL,
  username     VARCHAR(50) NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,
  role         ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 2. Tabel layanan: menyimpan jenis layanan cuci, tarif per kg, dan estimasi
-- ----------------------------------------------------------------------------
CREATE TABLE layanan (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nama_layanan     VARCHAR(100) NOT NULL,
  harga_per_kg     INT UNSIGNED NOT NULL,
  estimasi_selesai VARCHAR(60) NOT NULL,
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 3. Tabel transaksi: mencatat order pelanggan yang berelasi ke tabel layanan
--    ON DELETE RESTRICT mencegah layanan yang sudah terpakai dihapus sembarangan
--    agar rekap transaksi dan laporan keuangan tidak rusak.
-- ----------------------------------------------------------------------------
CREATE TABLE transaksi (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  kode_transaksi       VARCHAR(20) NOT NULL UNIQUE,
  nama_pelanggan       VARCHAR(100) NOT NULL,
  no_hp                VARCHAR(20) NOT NULL,
  layanan_id           INT NOT NULL,
  user_id              INT NULL,
  berat_kg             DECIMAL(6,2) NOT NULL,
  harga_saat_transaksi INT UNSIGNED NOT NULL,
  total_harga          INT UNSIGNED NOT NULL,
  status               ENUM('Proses', 'Selesai') NOT NULL DEFAULT 'Proses',
  tanggal_masuk        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tanggal_selesai      DATETIME NULL,

  CONSTRAINT fk_transaksi_layanan
    FOREIGN KEY (layanan_id) REFERENCES layanan(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT fk_transaksi_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- DATA AWAL (SEED DATA)
-- ----------------------------------------------------------------------------

-- Akun login default (password di-hash menggunakan bcrypt PHP password_hash)
-- Admin : username = admin | password = admin123
-- Kasir : username = kasir | password = kasir123
INSERT INTO users (nama_lengkap, username, password, role) VALUES
  ('Faqih Rachman', 'admin', '$2y$12$WYtxl.6EYDhQcXQfcj5eQetbv7XH772zbQ2OcR7C4OOmcE5z74VLS', 'admin'),
  ('Nadia Putri',   'kasir', '$2y$12$mxAH8W6shntgQjABW.MaReis6qspLejyMvhw4sJRcJqlobB3hsDjG', 'kasir');

-- Tarif layanan sesuai spesifikasi tugas:
-- Reguler    : Rp 7.000/kg  (3-4 hari)
-- Ekspres    : Rp 12.000/kg (1-2 hari)
-- Superkilat : Rp 25.000/kg (5 jam)
INSERT INTO layanan (nama_layanan, harga_per_kg, estimasi_selesai) VALUES
  ('Reguler',    7000,  '3-4 hari'),
  ('Ekspres',    12000, '1-2 hari'),
  ('Superkilat', 25000, '5 jam');

-- Contoh transaksi awal (14 order) agar Dashboard, filter Rekap, dan cetak struk siap didemokan
INSERT INTO transaksi
  (kode_transaksi, nama_pelanggan, no_hp, layanan_id, user_id, berat_kg, harga_saat_transaksi, total_harga, status, tanggal_masuk, tanggal_selesai)
VALUES
  ('LDR-0001', 'Budi Santoso',    '081234567890', 1, 2, 4.00, 7000,  28000, 'Selesai', DATE_SUB(NOW(), INTERVAL 7 DAY),  DATE_SUB(NOW(), INTERVAL 4 DAY)),
  ('LDR-0002', 'Siti Aminah',     '085611223344', 2, 2, 3.50, 12000, 42000, 'Selesai', DATE_SUB(NOW(), INTERVAL 6 DAY),  DATE_SUB(NOW(), INTERVAL 5 DAY)),
  ('LDR-0003', 'Andi Wijaya',     '082198765432', 3, 1, 2.00, 25000, 50000, 'Selesai', DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_SUB(NOW(), INTERVAL 5 DAY)),
  ('LDR-0004', 'Dewi Lestari',    '087855667788', 1, 2, 5.00, 7000,  35000, 'Selesai', DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_SUB(NOW(), INTERVAL 2 DAY)),
  ('LDR-0005', 'Hendra Gunawan',  '081377889900', 2, 1, 4.50, 12000, 54000, 'Selesai', DATE_SUB(NOW(), INTERVAL 4 DAY),  DATE_SUB(NOW(), INTERVAL 3 DAY)),
  ('LDR-0006', 'Rina Kusuma',     '085722334455', 1, 2, 6.00, 7000,  42000, 'Selesai', DATE_SUB(NOW(), INTERVAL 4 DAY),  DATE_SUB(NOW(), INTERVAL 1 DAY)),
  ('LDR-0007', 'Dimas Pratama',   '082244556677', 3, 1, 1.50, 25000, 37500, 'Selesai', DATE_SUB(NOW(), INTERVAL 3 DAY),  DATE_SUB(NOW(), INTERVAL 3 DAY)),
  ('LDR-0008', 'Kartika Sari',    '081566778899', 2, 2, 2.50, 12000, 30000, 'Selesai', DATE_SUB(NOW(), INTERVAL 3 DAY),  DATE_SUB(NOW(), INTERVAL 2 DAY)),
  ('LDR-0009', 'Fajar Ramadhan',  '087711229988', 1, 2, 7.50, 7000,  52500, 'Selesai', DATE_SUB(NOW(), INTERVAL 2 DAY),  DATE_SUB(NOW(), INTERVAL 8 HOUR)),
  ('LDR-0010', 'Maya Anggraini',  '081299001122', 3, 1, 3.00, 25000, 75000, 'Selesai', DATE_SUB(NOW(), INTERVAL 1 DAY),  DATE_SUB(NOW(), INTERVAL 19 HOUR)),
  ('LDR-0011', 'Reza Mahendra',   '085233445566', 2, 2, 3.00, 12000, 36000, 'Proses',  DATE_SUB(NOW(), INTERVAL 9 HOUR), NULL),
  ('LDR-0012', 'Putri Wulandari', '081844552233', 1, 2, 4.50, 7000,  31500, 'Proses',  DATE_SUB(NOW(), INTERVAL 6 HOUR), NULL),
  ('LDR-0013', 'Bagus Setiawan',  '082166554433', 3, 1, 2.50, 25000, 62500, 'Proses',  DATE_SUB(NOW(), INTERVAL 3 HOUR), NULL),
  ('LDR-0014', 'Aulia Rahmawati', '085877665544', 2, 2, 5.00, 12000, 60000, 'Proses',  DATE_SUB(NOW(), INTERVAL 1 HOUR), NULL);
