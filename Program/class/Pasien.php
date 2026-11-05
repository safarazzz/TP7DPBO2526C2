<?php
require_once __DIR__ . '/Manusia.php';

// class pasien mewarisi class manusia (inheritance)
class Pasien extends Manusia {
    protected $penyakit;
    protected $darurat;

    public function __construct() {
        // panggil constructor class manusia biar koneksi db langsung siap
        parent::__construct();
    }

    // simpan data pasien baru ke tabel pasien
    public function create($nama, $email, $no_hp, $penyakit, $darurat) {
        $stmt = $this->db->prepare("INSERT INTO pasien (nama, email, no_hp, penyakit, darurat) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nama, $email, $no_hp, $penyakit, $darurat]);
    }

    // ambil semua data pasien, yang darurat diutamakan di urutan teratas
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM pasien ORDER BY darurat DESC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ambil 1 pasien buat ditampilin di form edit
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pasien WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // simpan perubahan data pasien
    public function update($id, $nama, $email, $no_hp, $penyakit, $darurat) {
        $stmt = $this->db->prepare("UPDATE pasien SET nama = ?, email = ?, no_hp = ?, penyakit = ?, darurat = ? WHERE id = ?");
        return $stmt->execute([$nama, $email, $no_hp, $penyakit, $darurat, $id]);
    }

    // hapus pasien berdasarkan id
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pasien WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
