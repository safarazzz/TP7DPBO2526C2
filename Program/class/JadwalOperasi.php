<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Doctor.php';
require_once __DIR__ . '/Ruangan.php';

// class transaksi buat jadwal operasi, menghubungkan pasien, dokter, dan ruangan
class JadwalOperasi {
    private $db;
    private $doctorModel;
    private $ruanganModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
        // butuh instance dokter dan ruangan buat validasi bentrok waktu
        $this->doctorModel = new Doctor();
        $this->ruanganModel = new Ruangan();
    }

    // bikin jadwal baru dengan proteksi bentrok ruangan dan dokter
    public function create($pasien_id, $doctor_id, $ruangan_id, $nama_jadwal, $waktu_mulai, $estimasi_selesai) {
        // validasi jam: jangan sampai jam mulai malah lebih lambat dari jam selesai
        if (strtotime($waktu_mulai) >= strtotime($estimasi_selesai)) {
            return ['status' => false, 'message' => 'Waktu mulai harus lebih awal dari estimasi waktu selesai!'];
        }

        // cek apakah ruangannya lagi dipake orang lain
        if (!$this->ruanganModel->isKosong($ruangan_id, $waktu_mulai, $estimasi_selesai)) {
            return ['status' => false, 'message' => 'Ruangan operasi tersebut sudah terisi pada rentang waktu yang dipilih!'];
        }

        // cek apakah dokternya lagi ada operasi lain di jam yang sama
        if (!$this->doctorModel->isAvailable($doctor_id, $waktu_mulai, $estimasi_selesai)) {
            return ['status' => false, 'message' => 'Dokter tersebut sudah memiliki jadwal operasi lain pada jam yang sama!'];
        }

        // kalau aman semua, baru simpan ke tabel jadwal_operasi
        $stmt = $this->db->prepare("
            INSERT INTO jadwal_operasi (pasien_id, doctor_id, ruangan_id, nama_jadwal, waktu_mulai, estimasi_selesai, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')
        ");
        $success = $stmt->execute([$pasien_id, $doctor_id, $ruangan_id, $nama_jadwal, $waktu_mulai, $estimasi_selesai]);

        if ($success) {
            return ['status' => true, 'message' => 'Jadwal operasi berhasil dijadwalkan!'];
        }
        return ['status' => false, 'message' => 'Gagal menyimpan jadwal operasi ke database.'];
    }

    // ambil semua data jadwal, di-join ke tabel pasien, dokter, dan ruangan biar informasinya lengkap
    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT j.*, 
                   p.nama AS nama_pasien, p.darurat AS pasien_darurat,
                   d.nama AS nama_dokter, d.spesialis,
                   r.nama_ruangan, r.tipe_ruangan
            FROM jadwal_operasi j
            JOIN pasien p ON j.pasien_id = p.id
            JOIN doctor d ON j.doctor_id = d.id
            JOIN ruangan r ON j.ruangan_id = r.id
            ORDER BY j.waktu_mulai ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ambil 1 jadwal detail buat form edit
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT j.*, 
                   p.nama AS nama_pasien, p.darurat AS pasien_darurat,
                   d.nama AS nama_dokter, d.spesialis,
                   r.nama_ruangan, r.tipe_ruangan
            FROM jadwal_operasi j
            JOIN pasien p ON j.pasien_id = p.id
            JOIN doctor d ON j.doctor_id = d.id
            JOIN ruangan r ON j.ruangan_id = r.id
            WHERE j.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // update jadwal operasi
    public function update($id, $pasien_id, $doctor_id, $ruangan_id, $nama_jadwal, $waktu_mulai, $estimasi_selesai, $status) {
        // validasi urutan jam
        if (strtotime($waktu_mulai) >= strtotime($estimasi_selesai)) {
            return ['status' => false, 'message' => 'Waktu mulai harus lebih awal dari estimasi waktu selesai!'];
        }

        // kalau statusnya masih aktif (bukan Selesai/Dibatalkan), periksa lagi apakah bentrok
        if (!in_array($status, ['Selesai', 'Dibatalkan'])) {
            if (!$this->ruanganModel->isKosong($ruangan_id, $waktu_mulai, $estimasi_selesai, $id)) {
                return ['status' => false, 'message' => 'Ruangan operasi tersebut bentrok dengan jadwal lain!'];
            }

            if (!$this->doctorModel->isAvailable($doctor_id, $waktu_mulai, $estimasi_selesai, $id)) {
                return ['status' => false, 'message' => 'Dokter tersebut bentrok dengan jadwal operasi lain!'];
            }
        }

        $stmt = $this->db->prepare("
            UPDATE jadwal_operasi 
            SET pasien_id = ?, doctor_id = ?, ruangan_id = ?, nama_jadwal = ?, waktu_mulai = ?, estimasi_selesai = ?, status = ?
            WHERE id = ?
        ");
        $success = $stmt->execute([$pasien_id, $doctor_id, $ruangan_id, $nama_jadwal, $waktu_mulai, $estimasi_selesai, $status, $id]);

        if ($success) {
            return ['status' => true, 'message' => 'Jadwal operasi berhasil diperbarui!'];
        }
        return ['status' => false, 'message' => 'Gagal memperbarui data jadwal operasi.'];
    }

    // ganti status secara instan (misal tombol selesai operasi atau batalkan)
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE jadwal_operasi SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // hapus jadwal operasi dari database
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM jadwal_operasi WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
