<?php
require_once __DIR__ . '/../config/db.php';

// class untuk mengelola data ruang operasi
class Ruangan {
    private $db;

    public function __construct() {
        // ambil koneksi db
        $database = new Database();
        $this->db = $database->conn;
    }

    // simpan ruangan baru, kalau user ga upload foto dipakein default img/default.jpg
    public function create($nama_ruangan, $tipe_ruangan, $img = 'img/default.jpg') {
        $stmt = $this->db->prepare("INSERT INTO ruangan (nama_ruangan, tipe_ruangan, img) VALUES (?, ?, ?)");
        return $stmt->execute([$nama_ruangan, $tipe_ruangan, $img]);
    }

    // ambil semua daftar ruangan
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM ruangan ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ambil satu ruangan buat diedit
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ruangan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // simpan perubahan nama ruangan, tipe, dan fotonya
    public function update($id, $nama_ruangan, $tipe_ruangan, $img = 'img/default.jpg') {
        $stmt = $this->db->prepare("UPDATE ruangan SET nama_ruangan = ?, tipe_ruangan = ?, img = ? WHERE id = ?");
        return $stmt->execute([$nama_ruangan, $tipe_ruangan, $img, $id]);
    }

    // hapus data ruangan
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM ruangan WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // cek ketersediaan ruangan: apakah ruangan kosong di rentang waktu yang diminta
    public function isKosong($ruangan_id, $waktu_mulai, $estimasi_selesai, $exclude_jadwal_id = null) {
        $sql = "SELECT COUNT(*) AS total FROM jadwal_operasi 
                WHERE ruangan_id = ? 
                  AND status NOT IN ('Selesai', 'Dibatalkan')
                  AND (waktu_mulai < ? AND estimasi_selesai > ?)";
        $params = [$ruangan_id, $estimasi_selesai, $waktu_mulai];

        // lewati jadwal ini sendiri kalau lagi proses update
        if ($exclude_jadwal_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $exclude_jadwal_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        // bernilai true kalau ga ada jadwal lain yang tabrakan (total = 0)
        return (int)$row['total'] === 0;
    }
}
?>
