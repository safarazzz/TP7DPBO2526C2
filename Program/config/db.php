<?php
// pengaturan koneksi ke database mysql pake pdo
class Database {
    private $host = "localhost";
    private $db_name = "db_rumahsakit";
    private $username = "root";
    private $password = "";
    public $conn;

    public function __construct() {
        $this->conn = null;
        try {
            // bikin koneksi pdo
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // aktifin exception error biar gampang dilacak kalau ada query yang salah
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // biar hasil query default-nya array asosiatif
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // tampilkan pesan kalau database gagal konek
            echo "Koneksi database error: " . $exception->getMessage();
        }
    }
}
?>
