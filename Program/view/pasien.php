<?php
// kalau lagi ngeklik tombol edit, ambil data pasien yang mau diedit
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $pasien->getById($_GET['id']);
}
?>

<h3>Manajemen Data Pasien</h3>

<!-- form buat nambah atau ngedit pasien -->
<div class="form-card">
    <h4><?= $editData ? 'Edit Data Pasien' : 'Pendaftaran Pasien Baru' ?></h4>
    <form method="POST" action="index.php?page=pasien">
        <!-- kalau lagi mode edit, bawa id-nya lewat hidden input -->
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label>Nama Pasien *</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required placeholder="Contoh: Budi Santoso">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($editData['email'] ?? '') ?>" placeholder="budi@example.com">
            </div>

            <div class="form-group">
                <label>No. HP / Telepon *</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($editData['no_hp'] ?? '') ?>" required placeholder="08123456789">
            </div>

            <div class="form-group">
                <label>Penyakit / Diagnosa Awal *</label>
                <input type="text" name="penyakit" value="<?= htmlspecialchars($editData['penyakit'] ?? '') ?>" required placeholder="Contoh: Apendisitis Akut">
            </div>

            <div class="form-group">
                <label>Kondisi Pasien (Triage Darurat) *</label>
                <select name="darurat" required>
                    <option value="0" <?= (isset($editData['darurat']) && $editData['darurat'] == 0) ? 'selected' : '' ?>>Normal (Jadwal Reguler)</option>
                    <option value="1" <?= (isset($editData['darurat']) && $editData['darurat'] == 1) ? 'selected' : '' ?>>DARURAT / Prioritas Tinggi</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 15px;">
            <?php if ($editData): ?>
                <button type="submit" name="update_pasien" class="btn btn-primary">Simpan Perubahan</button>
                <a href="?page=pasien" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_pasien" class="btn btn-success">+ Tambah Pasien</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- tabel buat nampilin list semua pasien -->
<h4>Daftar Pasien</h4>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Nama Pasien</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Diagnosa Penyakit</th>
                <th>Status Triage</th>
                <th style="width: 140px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $listPasien = $pasien->getAll(); 
            if (empty($listPasien)): 
            ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b;">Belum ada data pasien.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($listPasien as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']) ?></td>
                    <td><strong><?= htmlspecialchars($p['nama']) ?></strong></td>
                    <td><?= htmlspecialchars($p['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['no_hp'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['penyakit']) ?></td>
                    <td>
                        <!-- tandain warna merah kalau pasien darurat -->
                        <?php if ($p['darurat'] == 1): ?>
                            <span class="badge badge-danger">DARURAT</span>
                        <?php else: ?>
                            <span class="badge badge-info">Normal</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <a href="?page=pasien&action=edit&id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?page=pasien&action=delete&id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data pasien ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
