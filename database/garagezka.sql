-- =============================================
-- GARAGEZKA DATABASE SCHEMA
-- =============================================

CREATE DATABASE IF NOT EXISTS garagezka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE garagezka;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    no_hp VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    foto_profil VARCHAR(255) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_token_expires DATETIME DEFAULT NULL,
    api_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Kendaraan table
CREATE TABLE IF NOT EXISTS kendaraan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_kendaraan VARCHAR(100) NOT NULL,
    nomor_plat VARCHAR(20) NOT NULL,
    tahun_produksi YEAR NOT NULL,
    warna VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Layanan table
CREATE TABLE IF NOT EXISTS layanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_layanan VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL,
    icon VARCHAR(50) DEFAULT 'wrench',
    estimasi_waktu VARCHAR(50) DEFAULT '1-2 jam',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Booking table
CREATE TABLE IF NOT EXISTS booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kendaraan_id INT NOT NULL,
    layanan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam TIME NOT NULL,
    status ENUM('pending', 'dikonfirmasi', 'selesai', 'dibatalkan') DEFAULT 'pending',
    catatan TEXT DEFAULT NULL,
    biaya_servis DECIMAL(12,2) DEFAULT 0,
    biaya_admin DECIMAL(12,2) DEFAULT 10000,
    total_biaya DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON DELETE CASCADE,
    FOREIGN KEY (layanan_id) REFERENCES layanan(id) ON DELETE CASCADE
);

-- Notifikasi table
CREATE TABLE IF NOT EXISTS notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT DEFAULT NULL,
    judul VARCHAR(200) NOT NULL,
    pesan TEXT NOT NULL,
    tipe ENUM('berhasil', 'selesai', 'info', 'peringatan') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE SET NULL
);

-- Pesan kontak table
CREATE TABLE IF NOT EXISTS pesan_kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample layanan data
INSERT INTO layanan (nama_layanan, deskripsi, harga, icon, estimasi_waktu) VALUES
('Ganti Oli', 'Penggantian oli mesin dengan oli berkualitas untuk menjaga performa mesin tetap prima.', 100000, 'oil', '30-60 menit'),
('Tune Up', 'Kalibrasi yang xtrem dari segala aspek dan pembaruan kondisi kecil untuk motor Anda.', 150000, 'tune', '1-2 jam'),
('Servis Lengkap', 'Perawatan menyeluruh 24 titik inspeksi, dan kalibrasi untuk kendaraan yang sempurna.', 250000, 'service', '2-3 jam'),
('Kelistrikan', 'Diagnostik komputerisasi akurat 24 titik listrik, sensor, dan optimalisasi sistem pengisian baterai.', 100000, 'electric', '1-2 jam'),
('Ban & Roda', 'Penggantian dan balancing ban untuk kenyamanan berkendara yang optimal.', 50000, 'tire', '30-45 menit'),
('Sistem Pengereman', 'Servis rem menyeluruh untuk keselamatan berkendara yang maksimal.', 75000, 'brake', '1-2 jam'),
('Cuci Detailing', 'Pembersihan menyeluruh dengan hasil tampilan motor Anda seperti baru.', 120000, 'wash', '2-3 jam'),
('Overhaul', 'Perbaikan besar pada komponen mesin untuk mengembalikan performa puncak motor Anda.', 500000, 'overhaul', '1-3 hari');

-- Insert admin user (password: admin123)
INSERT INTO users (nama_lengkap, email, no_hp, password, role) VALUES
('Admin Garagezka', 'admin@garagezka.com', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample user (password: user123)
INSERT INTO users (nama_lengkap, email, no_hp, password, role) VALUES
('Azka Gans', 'azka@email.com', '081298765432', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
