<?php
// hitung jumlah data buat ditampilin di kartu menu utama
$totalJadwal = count($jadwal->getAll());
$totalPasien = count($pasien->getAll());
$totalDoctor = count($doctor->getAll());
$totalRuangan = count($ruangan->getAll());
?>

<!-- banner sambutan -->
<div class="hero-banner">
    <h2>Selamat Datang di Rumah Sakit THB</h2>
    <p>Sistem Informasi Manajemen Rumah Sakit Terpadu: Penjadwalan Operasi, Pengelolaan Pasien & Tenaga Medis Spesialis, serta Ketersediaan Ruang Bedah.</p>
</div>

<!-- daftar pilihan menu utama -->
<h3>Menu Utama Sistem</h3>
<p style="color: #64748b; margin-bottom: 20px;">Silakan pilih modul menu di bawah ini untuk mengelola data rumah sakit:</p>

<div class="menu-grid">
    <!-- kartu menu jadwal operasi -->
    <div class="menu-card" style="border-top-color: #0284c7;">
        <div>
            <h4>📅 Jadwal Operasi</h4>
            <div class="stat-badge">Total: <?= $totalJadwal ?> Jadwal Terdaftar</div>
            <p>Atur jadwal tindakan bedah operasi, pantau status antrean, dan deteksi pencegahan bentrok ruangan atau dokter secara otomatis.</p>
        </div>
        <a href="?page=jadwal" class="btn btn-primary btn-block">Buka Jadwal Operasi &raquo;</a>
    </div>

    <!-- kartu menu data pasien -->
    <div class="menu-card" style="border-top-color: #10b981;">
        <div>
            <h4>🏥 Data Pasien</h4>
            <div class="stat-badge">Total: <?= $totalPasien ?> Pasien Terdaftar</div>
            <p>Pendaftaran pasien baru, pencatatan diagnosa penyakit, serta klasifikasi prioritas triage kondisi darurat.</p>
        </div>
        <a href="?page=pasien" class="btn btn-success btn-block">Kelola Data Pasien &raquo;</a>
    </div>

    <!-- kartu menu data dokter -->
    <div class="menu-card" style="border-top-color: #f59e0b;">
        <div>
            <h4>👨‍⚕️ Data Dokter</h4>
            <div class="stat-badge">Total: <?= $totalDoctor ?> Dokter Aktif</div>
            <p>Kelola data dokter spesialis bedah, rekam tahun awal praktik (pengalaman terbang), serta kontak dokter.</p>
        </div>
        <a href="?page=doctor" class="btn btn-warning btn-block">Kelola Data Dokter &raquo;</a>
    </div>

    <!-- kartu menu ruang operasi -->
    <div class="menu-card" style="border-top-color: #8b5cf6;">
        <div>
            <h4>🚪 Ruang Operasi</h4>
            <div class="stat-badge">Total: <?= $totalRuangan ?> Ruangan Tersedia</div>
            <p>Kelola data ruang operasi, spesifikasi tipe bedah, serta dokumentasi visual foto ruangan operasi.</p>
        </div>
        <a href="?page=ruangan" class="btn btn-primary btn-block" style="background-color: #8b5cf6;">Kelola Ruang Operasi &raquo;</a>
    </div>
</div>
