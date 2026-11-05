<?php
require_once __DIR__ . '/Manusia.php';

// class dokter turunan dari manusia (inheritance)
class Doctor extends Manusia {
    protected $spesialis;
    protected $tahun_mulai;

    public function __construct() {
        // jalanin constructor parent buat inisialisasi koneksi db
        parent::__construct();
    }

    // masukin dokter baru ke database
    public function create($nama, $spesialis, $tahun_mulai, $no_hp, $email) {
        $stmt = $this->db->prepare("INSERT INTO doctor (nama, spesialis, tahun_mulai, no_hp, email) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nama, $spesialis, $tahun_mulai, $no_hp, $email]);
    }

    // ambil semua daftar dokter
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM doctor ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ambil data satu dokter buat form edit
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM doctor WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // update profil dokter
    public function update($id, $nama, $spesialis, $tahun_mulai, $no_hp, $email) {
        $stmt = $this->db->prepare("UPDATE doctor SET nama = ?, spesialis = ?, tahun_mulai = ?, no_hp = ?, email = ? WHERE id = ?");
        return $stmt->execute([$nama, $spesialis, $tahun_mulai, $no_hp, $email, $id]);
    }

    // hapus dokter dari daftar
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM doctor WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // cek ketersediaan dokter: jangan sampai dokter ada jadwal operasi lain di jam yang bentrok
    public function isAvailable($doctor_id, $waktu_mulai, $estimasi_selesai, $exclude_jadwal_id = null) {
        // rumus tabrakan waktu: jadwal lama mulai sebelum jadwal baru kelar, dan selesai setelah jadwal baru mulai
        $sql = "SELECT COUNT(*) AS total FROM jadwal_operasi 
                WHERE doctor_id = ? 
                  AND status NOT IN ('Selesai', 'Dibatalkan')
                  AND (waktu_mulai < ? AND estimasi_selesai > ?)";
        $params = [$doctor_id, $estimasi_selesai, $waktu_mulai];

        // kalau lagi edit jadwal, jangan hitung jadwal yang lagi diedit itu sendiri
        if ($exclude_jadwal_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $exclude_jadwal_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        // kalau 0 berarti dokter lagi nganggur di jam segitu, boleh dijadwalin
        return (int)$row['total'] === 0;
    }
}
?>
