<?php
// nyalain session buat nampung pesan notifikasi (flash message)
session_start();

// import semua class model yang kita butuhin
require_once 'class/Pasien.php';
require_once 'class/Doctor.php';
require_once 'class/Ruangan.php';
require_once 'class/JadwalOperasi.php';

// bikin objek dari masing-masing class
$pasien = new Pasien();
$doctor = new Doctor();
$ruangan = new Ruangan();
$jadwal = new JadwalOperasi();

// fungsi kecil buat bantu upload foto ruangan ke folder lokal img/
function uploadImageRuangan($fileKey, $defaultOrExisting = 'img/default.jpg') {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$fileKey]['tmp_name'];
        $fileName = $_FILES[$fileKey]['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // batasi ekstensi gambar yang diperbolehkan
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = __DIR__ . '/img/';
            // bikin folder img kalau belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // bikin nama file unik pake timestamp biar ga nimpa file lama
            $cleanName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
            $newFileName = time() . '_' . $cleanName . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            // pindahin file yang diupload ke folder img
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                return 'img/' . $newFileName;
            }
        }
    }
    // kalau ga upload apa-apa, pake gambar default atau gambar lama
    return $defaultOrExisting;
}

// ==========================================
// 1. PROSES AKSI DATA PASIEN (CRUD)
// ==========================================
// nambah pasien baru
if (isset($_POST['add_pasien'])) {
    $pasien->create($_POST['nama'], $_POST['email'], $_POST['no_hp'], $_POST['penyakit'], $_POST['darurat']);
    $_SESSION['msg'] = "Data pasien baru berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=pasien");
    exit;
}
// update data pasien
if (isset($_POST['update_pasien'])) {
    $pasien->update($_POST['id'], $_POST['nama'], $_POST['email'], $_POST['no_hp'], $_POST['penyakit'], $_POST['darurat']);
    $_SESSION['msg'] = "Data pasien berhasil diperbarui!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=pasien");
    exit;
}
// hapus data pasien
if (isset($_GET['page']) && $_GET['page'] === 'pasien' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $pasien->delete($_GET['id']);
    $_SESSION['msg'] = "Data pasien berhasil dihapus!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=pasien");
    exit;
}

// ==========================================
// 2. PROSES AKSI DATA DOKTER (CRUD)
// ==========================================
// nambah dokter baru
if (isset($_POST['add_doctor'])) {
    $doctor->create($_POST['nama'], $_POST['spesialis'], $_POST['tahun_mulai'], $_POST['no_hp'], $_POST['email']);
    $_SESSION['msg'] = "Data dokter baru berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=doctor");
    exit;
}
// update dokter
if (isset($_POST['update_doctor'])) {
    $doctor->update($_POST['id'], $_POST['nama'], $_POST['spesialis'], $_POST['tahun_mulai'], $_POST['no_hp'], $_POST['email']);
    $_SESSION['msg'] = "Data dokter berhasil diperbarui!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=doctor");
    exit;
}
// hapus dokter
if (isset($_GET['page']) && $_GET['page'] === 'doctor' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $doctor->delete($_GET['id']);
    $_SESSION['msg'] = "Data dokter berhasil dihapus!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=doctor");
    exit;
}

// ==========================================
// 3. PROSES AKSI DATA RUANGAN (CRUD + UPLOAD)
// ==========================================
// nambah ruangan sekalian upload gambar
if (isset($_POST['add_ruangan'])) {
    $imagePath = uploadImageRuangan('image_file', 'img/default.jpg');
    $ruangan->create($_POST['nama_ruangan'], $_POST['tipe_ruangan'], $imagePath);
    $_SESSION['msg'] = "Data ruangan operasi berhasil ditambahkan dengan gambar ($imagePath)!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=ruangan");
    exit;
}
// update ruangan
if (isset($_POST['update_ruangan'])) {
    $existing = $_POST['existing_image'] ?? 'img/default.jpg';
    $imagePath = uploadImageRuangan('image_file', $existing);
    $ruangan->update($_POST['id'], $_POST['nama_ruangan'], $_POST['tipe_ruangan'], $imagePath);
    $_SESSION['msg'] = "Data ruangan operasi berhasil diperbarui!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=ruangan");
    exit;
}
// hapus ruangan
if (isset($_GET['page']) && $_GET['page'] === 'ruangan' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $ruangan->delete($_GET['id']);
    $_SESSION['msg'] = "Data ruangan berhasil dihapus!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=ruangan");
    exit;
}

// ==========================================
// 4. PROSES AKSI JADWAL OPERASI (CRUD + CEK BENTROK)
// ==========================================
// bikin jadwal operasi baru
if (isset($_POST['add_jadwal'])) {
    $result = $jadwal->create(
        $_POST['pasien_id'], 
        $_POST['doctor_id'], 
        $_POST['ruangan_id'], 
        $_POST['nama_jadwal'], 
        $_POST['waktu_mulai'], 
        $_POST['estimasi_selesai']
    );
    // simpan notifikasi berhasil atau gagal
    $_SESSION['msg'] = $result['message'];
    $_SESSION['msg_type'] = $result['status'] ? 'success' : 'danger';
    header("Location: index.php?page=jadwal");
    exit;
}
// update jadwal operasi
if (isset($_POST['update_jadwal'])) {
    $result = $jadwal->update(
        $_POST['id'],
        $_POST['pasien_id'], 
        $_POST['doctor_id'], 
        $_POST['ruangan_id'], 
        $_POST['nama_jadwal'], 
        $_POST['waktu_mulai'], 
        $_POST['estimasi_selesai'],
        $_POST['status']
    );
    $_SESSION['msg'] = $result['message'];
    $_SESSION['msg_type'] = $result['status'] ? 'success' : 'danger';
    header("Location: index.php?page=jadwal");
    exit;
}
// ganti status jadwal (misal dari Menunggu jadi Berlangsung/Selesai)
if (isset($_GET['page']) && $_GET['page'] === 'jadwal' && isset($_GET['action']) && $_GET['action'] === 'set_status') {
    $jadwal->updateStatus($_GET['id'], $_GET['status']);
    $_SESSION['msg'] = "Status operasi berhasil diubah menjadi: " . htmlspecialchars($_GET['status']);
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=jadwal");
    exit;
}
// hapus jadwal operasi
if (isset($_GET['page']) && $_GET['page'] === 'jadwal' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $jadwal->delete($_GET['id']);
    $_SESSION['msg'] = "Jadwal operasi berhasil dihapus!";
    $_SESSION['msg_type'] = "success";
    header("Location: index.php?page=jadwal");
    exit;
}

// halaman default buka menu utama (home)
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjadwalan Operasi - Rumah Sakit THB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- bagian header aplikasi -->
        <?php include 'view/header.php'; ?>
        
        <!-- navigasi menu -->
        <nav>
            <ul>
                <li><a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">Menu Utama</a></li>
                <li><a href="?page=jadwal" class="<?= $page === 'jadwal' ? 'active' : '' ?>">Jadwal Operasi</a></li>
                <li><a href="?page=pasien" class="<?= $page === 'pasien' ? 'active' : '' ?>">Data Pasien</a></li>
                <li><a href="?page=doctor" class="<?= $page === 'doctor' ? 'active' : '' ?>">Data Dokter</a></li>
                <li><a href="?page=ruangan" class="<?= $page === 'ruangan' ? 'active' : '' ?>">Ruang Operasi</a></li>
            </ul>
        </nav>

        <main>
            <!-- nampilin pesan notifikasi kalau ada aksi yang baru selesai -->
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['msg_type'] ?? 'info') ?>">
                    <?= htmlspecialchars($_SESSION['msg']) ?>
                </div>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
            <?php endif; ?>

            <!-- load halaman sesuai parameter url ?page=... -->
            <?php
            if ($page === 'home') {
                include 'view/home.php';
            } elseif ($page === 'jadwal') {
                include 'view/jadwal.php';
            } elseif ($page === 'pasien') {
                include 'view/pasien.php';
            } elseif ($page === 'doctor') {
                include 'view/doctor.php';
            } elseif ($page === 'ruangan') {
                include 'view/ruangan.php';
            } else {
                echo "<p>Halaman tidak ditemukan.</p>";
            }
            ?>
        </main>

        <!-- bagian footer -->
        <?php include 'view/footer.php'; ?>
    </div>
</body>
</html>
