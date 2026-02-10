-- ===================================
-- DIGITAL GUEST BOOK DATABASE SCHEMA
-- ===================================

CREATE DATABASE IF NOT EXISTS bukutamu 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE bukutamu;

-- ===================================
-- TABLE: visits (Tabel Kunjungan)
-- ===================================
CREATE TABLE IF NOT EXISTS visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_identitas VARCHAR(50) NOT NULL COMMENT 'NIK atau Nomor ID Card',
    visit_date DATE NOT NULL COMMENT 'Tanggal kunjungan',
    nama VARCHAR(100) NOT NULL COMMENT 'Nama pengunjung',
    asal VARCHAR(200) NOT NULL COMMENT 'Asal/alamat pengunjung',
    fungsi VARCHAR(100) NOT NULL COMMENT 'Fungsi (tamu/magang/dll)',
    jenis_identitas ENUM('KTP', 'ID_CARD') NOT NULL COMMENT 'Jenis identitas',
    keperluan TEXT NOT NULL COMMENT 'Keperluan kunjungan',
    jam_masuk DATETIME NOT NULL COMMENT 'Waktu masuk otomatis',
    tanda_tangan_masuk TEXT NOT NULL COMMENT 'Canvas signature base64',
    jam_keluar DATETIME NULL COMMENT 'Waktu keluar (null jika belum)',
    tanda_tangan_keluar TEXT NULL COMMENT 'Signature keluar base64',
    keterangan TEXT NULL COMMENT 'Keterangan tambahan (opsional)',
    status ENUM('MASUK', 'SELESAI') DEFAULT 'MASUK' COMMENT 'Status kunjungan',
    is_flagged BOOLEAN DEFAULT FALSE COMMENT 'Flag otomatis jam 23:59',
    flag_note VARCHAR(255) NULL COMMENT 'Catatan flag (tidak absen keluar)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes untuk performa
    INDEX idx_identity_date (nomor_identitas, visit_date),
    INDEX idx_status (status),
    INDEX idx_date (visit_date),
    INDEX idx_flagged (is_flagged)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLE: admin_users (Tabel Admin)
-- ===================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Password hash bcrypt',
    nama VARCHAR(100) NOT NULL COMMENT 'Nama lengkap admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- DEFAULT ADMIN USER
-- Username: admin
-- Password: admin123
-- ===================================
INSERT INTO admin_users (username, password, nama) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator')
ON DUPLICATE KEY UPDATE username = username;

-- ===================================
-- SAMPLE DATA (Optional - for testing)
-- ===================================
-- Uncomment untuk insert data sample

-- INSERT INTO visits (nomor_identitas, visit_date, nama, asal, fungsi, jenis_identitas, keperluan, jam_masuk, tanda_tangan_masuk, status)
-- VALUES 
-- ('3201234567890123', CURDATE(), 'John Doe', 'Jakarta', 'Tamu', 'KTP', 'Meeting dengan HRD', NOW(), 'data:image/png;base64,sample', 'MASUK'),
-- ('ID001234', CURDATE(), 'Jane Smith', 'Universitas ABC', 'Anak Magang - Universitas ABC', 'ID_CARD', 'Magang bagian IT', NOW(), 'data:image/png;base64,sample', 'MASUK');
