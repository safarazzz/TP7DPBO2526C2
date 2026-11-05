<?php
require_once __DIR__ . '/../config/db.php';

// class parent/induk: nurunin atribut dasar manusia ke class Pasien dan Doctor
abstract class Manusia {
    protected $db;
    protected $nama;
    protected $email;
    protected $no_hp;

    public function __construct() {
        // otomatis sambungin ke database, jadi kelas anak ga perlu bikin koneksi lagi
        $database = new Database();
        $this->db = $database->conn;
    }

    // getter dan setter buat properti dasar
    public function getNama() {
        return $this->nama;
    }

    public function setNama($nama) {
        $this->nama = $nama;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getNoHp() {
        return $this->no_hp;
    }

    public function setNoHp($no_hp) {
        $this->no_hp = $no_hp;
    }
}
?>
