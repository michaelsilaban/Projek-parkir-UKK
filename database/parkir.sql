-- ==========================================================
-- SKEMA BASIS DATA APLIKASI PARKIR (VERSI HOSTING)
-- Uji Kompetensi Keahlian (UKK) Paket 2 - Rekayasa Perangkat Lunak
-- ----------------------------------------------------------
-- Struktur tabel & fungsi SAMA dengan parkir.sql (versi asli),
-- hanya tanpa CREATE DATABASE / USE agar bisa diimport di hosting.
--
-- CARA PAKAI (hosting gratis):
-- 1. Buat database lewat panel hosting (MySQL Databases).
-- 2. Di phpMyAdmin, KLIK nama database tersebut terlebih dahulu.
-- 3. Baru import / jalankan file ini di tab SQL.
-- ==========================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tb_log_aktivitas, tb_transaksi, tb_kendaraan, tb_tarif, tb_area_parkir, tb_user;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------
-- 1. Tabel: tb_user
-- Menyimpan data pengguna dengan 3 level role (admin, petugas, owner)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_user (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'petugas', 'owner') NOT NULL DEFAULT 'petugas',
    status_aktif TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 2. Tabel: tb_area_parkir
-- Menyimpan master data area parkir beserta kapasitas dan slot terisi
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_area_parkir (
    id_area INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_area VARCHAR(50) NOT NULL,
    kapasitas INT(5) NOT NULL DEFAULT 0,
    terisi INT(5) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 3. Tabel: tb_tarif
-- Menyimpan master tarif parkir per jam berdasarkan jenis kendaraan
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_tarif (
    id_tarif INT(11) AUTO_INCREMENT PRIMARY KEY,
    jenis_kendaraan ENUM('motor', 'mobil', 'lainnya') NOT NULL UNIQUE,
    tarif_per_jam DECIMAL(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 4. Tabel: tb_kendaraan
-- Menyimpan data kendaraan yang terdaftar / pernah parkir
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_kendaraan (
    id_kendaraan INT(11) AUTO_INCREMENT PRIMARY KEY,
    plat_nomor VARCHAR(15) NOT NULL UNIQUE,
    jenis_kendaraan VARCHAR(20) NOT NULL,
    warna VARCHAR(20) NOT NULL,
    pemilik VARCHAR(100) NOT NULL,
    id_user INT(11) NULL,
    CONSTRAINT fk_kendaraan_user FOREIGN KEY (id_user) 
        REFERENCES tb_user (id_user) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 5. Tabel: tb_transaksi
-- Menyimpan transaksi parkir masuk dan keluar
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_transaksi (
    id_parkir INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_kendaraan INT(11) NOT NULL,
    waktu_masuk DATETIME NOT NULL,
    waktu_keluar DATETIME NULL,
    id_tarif INT(11) NOT NULL,
    durasi_jam INT(5) NOT NULL DEFAULT 0,
    biaya_total DECIMAL(10,0) NOT NULL DEFAULT 0,
    status ENUM('masuk', 'keluar') NOT NULL DEFAULT 'masuk',
    id_user INT(11) NOT NULL,
    id_area INT(11) NOT NULL,
    CONSTRAINT fk_transaksi_kendaraan FOREIGN KEY (id_kendaraan) 
        REFERENCES tb_kendaraan (id_kendaraan) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_transaksi_tarif FOREIGN KEY (id_tarif) 
        REFERENCES tb_tarif (id_tarif) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_transaksi_user FOREIGN KEY (id_user) 
        REFERENCES tb_user (id_user) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_transaksi_area FOREIGN KEY (id_area) 
        REFERENCES tb_area_parkir (id_area) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 6. Tabel: tb_log_aktivitas
-- Menyimpan catatan riwayat aktivitas pengguna pada sistem
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_log_aktivitas (
    id_log INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11) NOT NULL,
    aktivitas VARCHAR(100) NOT NULL,
    waktu_aktivitas DATETIME NOT NULL,
    CONSTRAINT fk_log_user FOREIGN KEY (id_user) 
        REFERENCES tb_user (id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ==========================================================
-- DATA CONTOH (sudah terisi) - waktu transaksi dibuat relatif terhadap NOW()
-- sehingga dashboard 'hari ini' & rekap laporan langsung menampilkan data.
-- Login default:  admin / admin123 | petugas / petugas123 | owner / owner123
-- (petugas2 memakai password petugas123; petugas3 non-aktif)
-- ==========================================================

INSERT INTO tb_user (id_user, nama_lengkap, username, password, role, status_aktif) VALUES
(1,'Administrator Parkir','admin','$2y$10$T5ggGYIDuTIgdNIXU3DMn.u7q880rDD212bM5dpUu8ffQ1AcRmmHy','admin',1),
(2,'Petugas Loket 1','petugas','$2y$10$tLrLh6/1DN13MR/OKUbJ1eYrYp9wLsFTjN9/Hi9KvOeI0rqXh0gdS','petugas',1),
(3,'Owner / Pimpinan','owner','$2y$10$WWfPPmvmqzN.MmtPzoOvc.YghrxFndXOtuDOpFhB1BBcKbIGDxNum','owner',1),
(4,'Rina Wulandari','petugas2','$2y$10$tLrLh6/1DN13MR/OKUbJ1eYrYp9wLsFTjN9/Hi9KvOeI0rqXh0gdS','petugas',1),
(5,'Agus Prasetyo','petugas3','$2y$10$tLrLh6/1DN13MR/OKUbJ1eYrYp9wLsFTjN9/Hi9KvOeI0rqXh0gdS','petugas',0);

INSERT INTO tb_tarif (id_tarif, jenis_kendaraan, tarif_per_jam) VALUES
(1,'motor',2000),
(2,'mobil',5000),
(3,'lainnya',10000);

INSERT INTO tb_area_parkir (id_area, nama_area, kapasitas, terisi) VALUES
(1,'Area A - Parkir Motor',100,1),
(2,'Area B - Parkir Mobil',50,2),
(3,'Area C - Khusus / VIP',20,1),
(4,'Area D - Parkir Motor 2',60,0);

INSERT INTO tb_kendaraan (id_kendaraan, plat_nomor, jenis_kendaraan, warna, pemilik, id_user) VALUES
(1,'B 1234 ABC','mobil','Hitam','Budi Santoso',2),
(2,'B 5678 XYZ','motor','Merah','Siti Rahma',2),
(3,'D 4321 KL','mobil','Putih','Andi Wijaya',2),
(4,'B 9012 DEF','motor','Hitam','Dewi Lestari',2),
(5,'F 7788 GH','mobil','Silver','Hendra Gunawan',2),
(6,'B 3344 JKL','motor','Biru','Rizky Ramadhan',2),
(7,'B 2201 MNO','mobil','Abu-abu','Maya Sari',2),
(8,'E 1020 PQ','motor','Putih','Fajar Nugroho',2),
(9,'B 6655 RST','motor','Hijau','Nina Marlina',2),
(10,'AB 5521 UV','mobil','Merah','Yoga Pratama',2),
(11,'B 7001 WXY','lainnya','Kuning','CV Maju Jaya',2),
(12,'D 8899 ZA','motor','Hitam','Tono Hartono',2),
(13,'B 4567 BCD','mobil','Biru','Lina Marlina',2),
(14,'B 1357 EFG','motor','Merah','Candra Kusuma',2),
(15,'T 2468 HI','mobil','Hitam','Slamet Riyadi',2),
(16,'B 9753 JKM','motor','Putih','Putri Ayu',2),
(17,'B 8080 NOP','lainnya','Putih','PT Sentosa Logistik',2),
(18,'Z 1122 QR','motor','Biru','Eko Saputra',2);

INSERT INTO tb_transaksi (id_parkir, id_kendaraan, waktu_masuk, waktu_keluar, id_tarif, durasi_jam, biaya_total, status, id_user, id_area) VALUES
(1,2,DATE_SUB(NOW(), INTERVAL 16500 MINUTE),DATE_SUB(NOW(), INTERVAL 16080 MINUTE),1,7,14000,'keluar',2,4),
(2,1,DATE_SUB(NOW(), INTERVAL 16221 MINUTE),DATE_SUB(NOW(), INTERVAL 16146 MINUTE),2,2,10000,'keluar',4,2),
(3,16,DATE_SUB(NOW(), INTERVAL 14997 MINUTE),DATE_SUB(NOW(), INTERVAL 14797 MINUTE),1,4,8000,'keluar',2,4),
(4,10,DATE_SUB(NOW(), INTERVAL 14673 MINUTE),DATE_SUB(NOW(), INTERVAL 14253 MINUTE),2,7,35000,'keluar',2,3),
(5,3,DATE_SUB(NOW(), INTERVAL 14630 MINUTE),DATE_SUB(NOW(), INTERVAL 14580 MINUTE),2,1,5000,'keluar',2,3),
(6,13,DATE_SUB(NOW(), INTERVAL 13629 MINUTE),DATE_SUB(NOW(), INTERVAL 13309 MINUTE),2,6,30000,'keluar',2,2),
(7,15,DATE_SUB(NOW(), INTERVAL 13441 MINUTE),DATE_SUB(NOW(), INTERVAL 13271 MINUTE),2,3,15000,'keluar',2,2),
(8,13,DATE_SUB(NOW(), INTERVAL 13213 MINUTE),DATE_SUB(NOW(), INTERVAL 12893 MINUTE),2,6,30000,'keluar',4,2),
(9,2,DATE_SUB(NOW(), INTERVAL 13185 MINUTE),DATE_SUB(NOW(), INTERVAL 13055 MINUTE),1,3,6000,'keluar',2,1),
(10,11,DATE_SUB(NOW(), INTERVAL 12024 MINUTE),DATE_SUB(NOW(), INTERVAL 11704 MINUTE),3,6,60000,'keluar',4,3),
(11,15,DATE_SUB(NOW(), INTERVAL 9206 MINUTE),DATE_SUB(NOW(), INTERVAL 8946 MINUTE),2,5,25000,'keluar',4,2),
(12,15,DATE_SUB(NOW(), INTERVAL 9151 MINUTE),DATE_SUB(NOW(), INTERVAL 9101 MINUTE),2,1,5000,'keluar',2,3),
(13,3,DATE_SUB(NOW(), INTERVAL 9108 MINUTE),DATE_SUB(NOW(), INTERVAL 8788 MINUTE),2,6,30000,'keluar',2,3),
(14,12,DATE_SUB(NOW(), INTERVAL 8967 MINUTE),DATE_SUB(NOW(), INTERVAL 8892 MINUTE),1,2,4000,'keluar',4,1),
(15,18,DATE_SUB(NOW(), INTERVAL 7761 MINUTE),DATE_SUB(NOW(), INTERVAL 7501 MINUTE),1,5,10000,'keluar',2,4),
(16,2,DATE_SUB(NOW(), INTERVAL 4554 MINUTE),DATE_SUB(NOW(), INTERVAL 4424 MINUTE),1,3,6000,'keluar',2,1),
(17,14,DATE_SUB(NOW(), INTERVAL 3467 MINUTE),DATE_SUB(NOW(), INTERVAL 3267 MINUTE),1,4,8000,'keluar',2,4),
(18,10,DATE_SUB(NOW(), INTERVAL 3132 MINUTE),DATE_SUB(NOW(), INTERVAL 3002 MINUTE),2,3,15000,'keluar',2,2),
(19,11,DATE_SUB(NOW(), INTERVAL 1993 MINUTE),DATE_SUB(NOW(), INTERVAL 1573 MINUTE),3,7,70000,'keluar',4,3),
(20,1,DATE_SUB(NOW(), INTERVAL 1854 MINUTE),DATE_SUB(NOW(), INTERVAL 1434 MINUTE),2,7,35000,'keluar',2,3),
(21,11,DATE_SUB(NOW(), INTERVAL 1842 MINUTE),DATE_SUB(NOW(), INTERVAL 1817 MINUTE),3,1,10000,'keluar',2,3),
(22,18,DATE_SUB(NOW(), INTERVAL 1827 MINUTE),DATE_SUB(NOW(), INTERVAL 1802 MINUTE),1,1,2000,'keluar',4,1),
(23,8,DATE_SUB(NOW(), INTERVAL 1682 MINUTE),DATE_SUB(NOW(), INTERVAL 1607 MINUTE),1,2,4000,'keluar',2,1),
(24,14,DATE_SUB(NOW(), INTERVAL 692 MINUTE),DATE_SUB(NOW(), INTERVAL 642 MINUTE),1,1,2000,'keluar',4,4),
(25,15,DATE_SUB(NOW(), INTERVAL 630 MINUTE),DATE_SUB(NOW(), INTERVAL 580 MINUTE),2,1,5000,'keluar',2,2),
(26,3,DATE_SUB(NOW(), INTERVAL 574 MINUTE),DATE_SUB(NOW(), INTERVAL 404 MINUTE),2,3,15000,'keluar',4,3),
(27,18,DATE_SUB(NOW(), INTERVAL 488 MINUTE),DATE_SUB(NOW(), INTERVAL 463 MINUTE),1,1,2000,'keluar',4,1),
(28,8,DATE_SUB(NOW(), INTERVAL 448 MINUTE),DATE_SUB(NOW(), INTERVAL 373 MINUTE),1,2,4000,'keluar',2,4),
(29,2,DATE_SUB(NOW(), INTERVAL 422 MINUTE),DATE_SUB(NOW(), INTERVAL 162 MINUTE),1,5,10000,'keluar',2,1),
(30,16,DATE_SUB(NOW(), INTERVAL 311 MINUTE),DATE_SUB(NOW(), INTERVAL 141 MINUTE),1,3,6000,'keluar',2,1),
(31,1,DATE_SUB(NOW(), INTERVAL 185 MINUTE),NULL,2,0,0,'masuk',2,2),
(32,2,DATE_SUB(NOW(), INTERVAL 120 MINUTE),NULL,1,0,0,'masuk',4,1),
(33,5,DATE_SUB(NOW(), INTERVAL 60 MINUTE),NULL,2,0,0,'masuk',2,2),
(34,11,DATE_SUB(NOW(), INTERVAL 45 MINUTE),NULL,3,0,0,'masuk',4,3);

INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES
(1,'Sistem diinisialisasi pertama kali',DATE_SUB(NOW(), INTERVAL 20000 MINUTE)),
(1,'Admin menambahkan akun petugas2',DATE_SUB(NOW(), INTERVAL 19990 MINUTE)),
(2,'Check-in kendaraan masuk B 5678 XYZ',DATE_SUB(NOW(), INTERVAL 16500 MINUTE)),
(4,'Check-in kendaraan masuk B 1234 ABC',DATE_SUB(NOW(), INTERVAL 16221 MINUTE)),
(4,'Check-out parkir plat: B 1234 ABC (Total: Rp 10.000)',DATE_SUB(NOW(), INTERVAL 16146 MINUTE)),
(2,'Check-out parkir plat: B 5678 XYZ (Total: Rp 14.000)',DATE_SUB(NOW(), INTERVAL 16080 MINUTE)),
(2,'Check-in kendaraan masuk B 9753 JKM',DATE_SUB(NOW(), INTERVAL 14997 MINUTE)),
(2,'Check-out parkir plat: B 9753 JKM (Total: Rp 8.000)',DATE_SUB(NOW(), INTERVAL 14797 MINUTE)),
(2,'Check-in kendaraan masuk AB 5521 UV',DATE_SUB(NOW(), INTERVAL 14673 MINUTE)),
(2,'Check-in kendaraan masuk D 4321 KL',DATE_SUB(NOW(), INTERVAL 14630 MINUTE)),
(2,'Check-out parkir plat: D 4321 KL (Total: Rp 5.000)',DATE_SUB(NOW(), INTERVAL 14580 MINUTE)),
(2,'Check-out parkir plat: AB 5521 UV (Total: Rp 35.000)',DATE_SUB(NOW(), INTERVAL 14253 MINUTE)),
(2,'Check-in kendaraan masuk B 4567 BCD',DATE_SUB(NOW(), INTERVAL 13629 MINUTE)),
(2,'Check-in kendaraan masuk T 2468 HI',DATE_SUB(NOW(), INTERVAL 13441 MINUTE)),
(2,'Check-out parkir plat: B 4567 BCD (Total: Rp 30.000)',DATE_SUB(NOW(), INTERVAL 13309 MINUTE)),
(2,'Check-out parkir plat: T 2468 HI (Total: Rp 15.000)',DATE_SUB(NOW(), INTERVAL 13271 MINUTE)),
(4,'Check-in kendaraan masuk B 4567 BCD',DATE_SUB(NOW(), INTERVAL 13213 MINUTE)),
(2,'Check-in kendaraan masuk B 5678 XYZ',DATE_SUB(NOW(), INTERVAL 13185 MINUTE)),
(2,'Check-out parkir plat: B 5678 XYZ (Total: Rp 6.000)',DATE_SUB(NOW(), INTERVAL 13055 MINUTE)),
(4,'Check-out parkir plat: B 4567 BCD (Total: Rp 30.000)',DATE_SUB(NOW(), INTERVAL 12893 MINUTE)),
(4,'Check-in kendaraan masuk B 7001 WXY',DATE_SUB(NOW(), INTERVAL 12024 MINUTE)),
(4,'Check-out parkir plat: B 7001 WXY (Total: Rp 60.000)',DATE_SUB(NOW(), INTERVAL 11704 MINUTE)),
(4,'Check-in kendaraan masuk T 2468 HI',DATE_SUB(NOW(), INTERVAL 9206 MINUTE)),
(2,'Check-in kendaraan masuk T 2468 HI',DATE_SUB(NOW(), INTERVAL 9151 MINUTE)),
(2,'Check-in kendaraan masuk D 4321 KL',DATE_SUB(NOW(), INTERVAL 9108 MINUTE)),
(2,'Check-out parkir plat: T 2468 HI (Total: Rp 5.000)',DATE_SUB(NOW(), INTERVAL 9101 MINUTE)),
(4,'Check-in kendaraan masuk D 8899 ZA',DATE_SUB(NOW(), INTERVAL 8967 MINUTE)),
(4,'Check-out parkir plat: T 2468 HI (Total: Rp 25.000)',DATE_SUB(NOW(), INTERVAL 8946 MINUTE)),
(4,'Check-out parkir plat: D 8899 ZA (Total: Rp 4.000)',DATE_SUB(NOW(), INTERVAL 8892 MINUTE)),
(2,'Check-out parkir plat: D 4321 KL (Total: Rp 30.000)',DATE_SUB(NOW(), INTERVAL 8788 MINUTE)),
(2,'Check-in kendaraan masuk Z 1122 QR',DATE_SUB(NOW(), INTERVAL 7761 MINUTE)),
(2,'Check-out parkir plat: Z 1122 QR (Total: Rp 10.000)',DATE_SUB(NOW(), INTERVAL 7501 MINUTE)),
(2,'Check-in kendaraan masuk B 5678 XYZ',DATE_SUB(NOW(), INTERVAL 4554 MINUTE)),
(2,'Check-out parkir plat: B 5678 XYZ (Total: Rp 6.000)',DATE_SUB(NOW(), INTERVAL 4424 MINUTE)),
(2,'Check-in kendaraan masuk B 1357 EFG',DATE_SUB(NOW(), INTERVAL 3467 MINUTE)),
(2,'Check-out parkir plat: B 1357 EFG (Total: Rp 8.000)',DATE_SUB(NOW(), INTERVAL 3267 MINUTE)),
(2,'Check-in kendaraan masuk AB 5521 UV',DATE_SUB(NOW(), INTERVAL 3132 MINUTE)),
(2,'Check-out parkir plat: AB 5521 UV (Total: Rp 15.000)',DATE_SUB(NOW(), INTERVAL 3002 MINUTE)),
(4,'Check-in kendaraan masuk B 7001 WXY',DATE_SUB(NOW(), INTERVAL 1993 MINUTE)),
(2,'Check-in kendaraan masuk B 1234 ABC',DATE_SUB(NOW(), INTERVAL 1854 MINUTE)),
(2,'Check-in kendaraan masuk B 7001 WXY',DATE_SUB(NOW(), INTERVAL 1842 MINUTE)),
(4,'Check-in kendaraan masuk Z 1122 QR',DATE_SUB(NOW(), INTERVAL 1827 MINUTE)),
(2,'Check-out parkir plat: B 7001 WXY (Total: Rp 10.000)',DATE_SUB(NOW(), INTERVAL 1817 MINUTE)),
(4,'Check-out parkir plat: Z 1122 QR (Total: Rp 2.000)',DATE_SUB(NOW(), INTERVAL 1802 MINUTE)),
(2,'Check-in kendaraan masuk E 1020 PQ',DATE_SUB(NOW(), INTERVAL 1682 MINUTE)),
(2,'Check-out parkir plat: E 1020 PQ (Total: Rp 4.000)',DATE_SUB(NOW(), INTERVAL 1607 MINUTE)),
(4,'Check-out parkir plat: B 7001 WXY (Total: Rp 70.000)',DATE_SUB(NOW(), INTERVAL 1573 MINUTE)),
(2,'Check-out parkir plat: B 1234 ABC (Total: Rp 35.000)',DATE_SUB(NOW(), INTERVAL 1434 MINUTE)),
(4,'Check-in kendaraan masuk B 1357 EFG',DATE_SUB(NOW(), INTERVAL 692 MINUTE)),
(4,'Check-out parkir plat: B 1357 EFG (Total: Rp 2.000)',DATE_SUB(NOW(), INTERVAL 642 MINUTE)),
(2,'Check-in kendaraan masuk T 2468 HI',DATE_SUB(NOW(), INTERVAL 630 MINUTE)),
(2,'Check-out parkir plat: T 2468 HI (Total: Rp 5.000)',DATE_SUB(NOW(), INTERVAL 580 MINUTE)),
(4,'Check-in kendaraan masuk D 4321 KL',DATE_SUB(NOW(), INTERVAL 574 MINUTE)),
(4,'Check-in kendaraan masuk Z 1122 QR',DATE_SUB(NOW(), INTERVAL 488 MINUTE)),
(4,'Check-out parkir plat: Z 1122 QR (Total: Rp 2.000)',DATE_SUB(NOW(), INTERVAL 463 MINUTE)),
(2,'Check-in kendaraan masuk E 1020 PQ',DATE_SUB(NOW(), INTERVAL 448 MINUTE)),
(2,'Check-in kendaraan masuk B 5678 XYZ',DATE_SUB(NOW(), INTERVAL 422 MINUTE)),
(4,'Check-out parkir plat: D 4321 KL (Total: Rp 15.000)',DATE_SUB(NOW(), INTERVAL 404 MINUTE)),
(2,'Check-out parkir plat: E 1020 PQ (Total: Rp 4.000)',DATE_SUB(NOW(), INTERVAL 373 MINUTE)),
(2,'Check-in kendaraan masuk B 9753 JKM',DATE_SUB(NOW(), INTERVAL 311 MINUTE)),
(2,'Check-in kendaraan masuk B 1234 ABC',DATE_SUB(NOW(), INTERVAL 185 MINUTE)),
(2,'Check-out parkir plat: B 5678 XYZ (Total: Rp 10.000)',DATE_SUB(NOW(), INTERVAL 162 MINUTE)),
(2,'Check-out parkir plat: B 9753 JKM (Total: Rp 6.000)',DATE_SUB(NOW(), INTERVAL 141 MINUTE)),
(4,'Check-in kendaraan masuk B 5678 XYZ',DATE_SUB(NOW(), INTERVAL 120 MINUTE)),
(2,'Check-in kendaraan masuk F 7788 GH',DATE_SUB(NOW(), INTERVAL 60 MINUTE)),
(4,'Check-in kendaraan masuk B 7001 WXY',DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(3,'Owner membuka rekap transaksi',DATE_SUB(NOW(), INTERVAL 30 MINUTE));
