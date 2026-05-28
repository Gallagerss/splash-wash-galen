CREATE DATABASE IF NOT EXISTS splashwash_db;
USE splashwash_db;

CREATE TABLE kendaraan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE layanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  jenis VARCHAR(50) NOT NULL,
  harga DECIMAL(10,2) NOT NULL,
  durasi_menit INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  layanan_id INT NOT NULL,
  nama_customer VARCHAR(100) NOT NULL,
  plat_nomor VARCHAR(20) NOT NULL,
  total_bayar DECIMAL(10,2) NOT NULL,
  status ENUM('PROSES','SELESAI') NOT NULL DEFAULT 'PROSES',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pesanan_layananId_fkey FOREIGN KEY (layanan_id) REFERENCES layanan(id)
);

INSERT INTO kendaraan (nama) VALUES ('Motor'), ('Mobil'), ('Truk / Pick Up');

INSERT INTO layanan (nama, jenis, harga, durasi_menit) VALUES
('Cuci Kilat', 'Motor', 15000, 15),
('Cuci Kilat', 'Mobil', 35000, 30),
('Cuci Kilat', 'Truk / Pick Up', 75000, 45),
('Cuci Detail', 'Motor', 30000, 30),
('Cuci Detail', 'Mobil', 85000, 90),
('Cuci Detail', 'Truk / Pick Up', 120000, 120),
('Waxing', 'Motor', 50000, 45),
('Waxing', 'Mobil', 150000, 120);