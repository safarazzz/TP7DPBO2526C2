<?php
// kalau user ngeklik tombol edit, ambil data dokter yang bersangkutan
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $doctor->getById($_GET['id']);
}
?>

<h3>Manajemen Data Dokter</h3>

<!-- form tambah dokter baru atau edit dokter -->
<div class="form-card">
    <h4><?= $editData ? 'Edit Data Dokter' : 'Tambah Dokter Baru' ?></h4>
    <form method="POST" action="index.php?page=doctor">
        <!-- simpan id dokter saat edit -->
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label>Nama Lengkap Dokter & Gelar *</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required placeholder="Contoh: dr. Andi Pratama, Sp.B">
            </div>

            <div class="form-group">
                <label>Bidang Spesialisasi *</label>
                <input type="text" name="spesialis" value="<?= htmlspecialchars($editData['spesialis'] ?? '') ?>" required placeholder="Contoh: Spesialis Bedah Umum">
            </div>

            <div class="form-group">
                <label>Tahun Mulai Praktik *</label>
                <input type="number" name="tahun_mulai" min="1970" max="<?= date('Y') ?>" value="<?= htmlspecialchars($editData['tahun_mulai'] ?? date('Y')) ?>" required>
            </div>

            <div class="form-group">
                <label>No. HP / Kontak *</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($editData['no_hp'] ?? '') ?>" required placeholder="08123456789">
            </div>

            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($editData['email'] ?? '') ?>" placeholder="dokter@hospital.com">
            </div>
        </div>

        <div style="margin-top: 15px;">
            <?php if ($editData): ?>
                <button type="submit" name="update_doctor" class="btn btn-primary">Simpan Perubahan</button>
                <a href="?page=doctor" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_doctor" class="btn btn-success">+ Tambah Dokter</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- tabel daftar dokter yang terdaftar -->
<h4>Daftar Tenaga Medis / Dokter</h4>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Nama Dokter</th>
                <th>Spesialis</th>
                <th>Tahun Mulai (Pengalaman)</th>
                <th>No. HP</th>
                <th>Email</th>
                <th style="width: 140px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $listDoctor = $doctor->getAll(); 
            if (empty($listDoctor)): 
            ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b;">Belum ada data dokter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($listDoctor as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['id']) ?></td>
                    <td><strong><?= htmlspecialchars($d['nama']) ?></strong></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($d['spesialis']) ?></span></td>
                    <td>
                        <?= htmlspecialchars($d['tahun_mulai']) ?> 
                        <!-- hitung otomatis pengalaman berapa tahun berdasarkan tahun sekarang -->
                        <small style="color: #64748b;">(<?= max(0, date('Y') - (int)$d['tahun_mulai']) ?> tahun)</small>
                    </td>
                    <td><?= htmlspecialchars($d['no_hp'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($d['email'] ?? '-') ?></td>
                    <td style="text-align: center;">
                        <a href="?page=doctor&action=edit&id=<?= $d['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?page=doctor&action=delete&id=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data dokter ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
