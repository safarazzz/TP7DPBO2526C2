DROP DATABASE IF EXISTS db_rumahsakit;
CREATE DATABASE db_rumahsakit;
USE db_rumahsakit;

-- 1. Tabel Pasien (Extends Manusia di OOP)
CREATE TABLE pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    penyakit VARCHAR(255) NOT NULL,
    darurat TINYINT(1) NOT NULL DEFAULT 0
);
-- 2. Tabel Doctor (Extends Manusia di OOP)
CREATE TABLE doctor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    spesialis VARCHAR(100) NOT NULL,
    tahun_mulai INT NOT NULL,
    no_hp VARCHAR(20),
    email VARCHAR(100)
);
-- 3. Tabel Ruangan (Memiliki kolom img VARCHAR yang mengarah ke path lokal img/)
CREATE TABLE ruangan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ruangan VARCHAR(100) NOT NULL,
    tipe_ruangan VARCHAR(50) NOT NULL DEFAULT 'Operasi Umum',
    img VARCHAR(255) NOT NULL DEFAULT 'img/default.jpg'
);
-- 4. Tabel Jadwal Operasi (Relasi / Transaksi)
CREATE TABLE jadwal_operasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pasien_id INT NOT NULL,
    doctor_id INT NOT NULL,
    ruangan_id INT NOT NULL,
    nama_jadwal VARCHAR(150) NOT NULL,
    waktu_mulai DATETIME NOT NULL,
    estimasi_selesai DATETIME NOT NULL,
    status ENUM('Menunggu', 'Berlangsung', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Menunggu',
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctor(id) ON DELETE CASCADE,
    FOREIGN KEY (ruangan_id) REFERENCES ruangan(id) ON DELETE CASCADE
);
-- Seed Data Dummy Awal
INSERT INTO pasien (nama, email, no_hp, penyakit, darurat) VALUES
('Budi Santoso', 'budi@gmail.com', '081234567890', 'Apendisitis Akut', 1),
('Siti Rahma', 'siti@gmail.com', '082345678901', 'Katarak', 0),
('Ahmad Fauzi', 'ahmad@gmail.com', '083456789012', 'Fraktur Tulang Kering', 1);

INSERT INTO doctor (nama, spesialis, tahun_mulai, no_hp, email) VALUES
('dr. Tirta Wijaya, Sp.B', 'Spesialis Bedah Umum', 2015, '08111222333', 'tirta@hospital.com'),
('dr. Sarah Amelia, Sp.M', 'Spesialis Mata', 2018, '08222333444', 'sarah@hospital.com'),
('dr. Hendra Pratama, Sp.OT', 'Spesialis Orthopedi', 2012, '08333444555', 'hendra@hospital.com');

INSERT INTO ruangan (nama_ruangan, tipe_ruangan, img) VALUES
('Ruang Operasi Melati', 'Bedah Umum', 'img/melati.jpg'),
('Ruang Operasi Mawar', 'Bedah Mata', 'img/mawar.jpg'),
('Ruang Operasi Anggrek', 'Bedah Tulang (Orthopedi)', 'img/anggrek.jpg');

INSERT INTO jadwal_operasi (pasien_id, doctor_id, ruangan_id, nama_jadwal, waktu_mulai, estimasi_selesai, status) VALUES
(1, 1, 1, 'Operasi Usus Buntu (Apendektomi)', '2026-11-05 08:00:00', '2026-11-05 10:00:00', 'Menunggu'),
(2, 2, 2, 'Operasi Fakoemulsifikasi Katarak', '2026-11-05 10:30:00', '2026-11-05 12:00:00', 'Menunggu');
