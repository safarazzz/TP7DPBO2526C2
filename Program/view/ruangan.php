<?php
// kalau ada parameter edit, tarik data ruangan yang mau diubah
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $ruangan->getById($_GET['id']);
}
?>

<h3>Manajemen Ruang Operasi</h3>

<!-- form tambah / edit ruangan, ada upload foto juga -->
<div class="form-card">
    <h4><?= $editData ? 'Edit Data Ruangan' : 'Tambah Ruang Operasi Baru' ?></h4>
    <form method="POST" action="index.php?page=ruangan" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <!-- simpan path gambar lama biar ga ilang kalau user ga upload file baru -->
            <?php $currentImg = $editData['img'] ?? $editData['image'] ?? 'img/default.jpg'; ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($currentImg) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label>Nama Ruangan *</label>
                <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($editData['nama_ruangan'] ?? '') ?>" required placeholder="Contoh: Ruang Operasi Melati">
            </div>

            <div class="form-group">
                <label>Tipe / Fasilitas Ruangan *</label>
                <input type="text" name="tipe_ruangan" value="<?= htmlspecialchars($editData['tipe_ruangan'] ?? '') ?>" required placeholder="Contoh: Bedah Umum / Bedah Jantung">
            </div>

            <div class="form-group">
                <label>Upload Foto Ruangan (Disimpan otomatis ke folder img/)</label>
                <input type="file" name="image_file" accept="image/*">
                
                <?php if ($editData): ?>
                    <!-- tampilin pratinjau foto yang sekarang dipakai -->
                    <?php $imgFile = $editData['img'] ?? $editData['image'] ?? 'img/default.jpg'; ?>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
                        <img src="<?= htmlspecialchars($imgFile) ?>" alt="Preview" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;" onerror="this.src='img/default.jpg';">
                        <span style="font-size: 12px; color: #64748b;">
                            File saat ini: <code><?= htmlspecialchars($imgFile) ?></code> (Pilih file baru jika ingin mengganti)
                        </span>
                    </div>
                <?php else: ?>
                    <small style="color: #64748b; margin-top: 4px;">Format: JPG, PNG, WEBP. Jika dikosongkan, otomatis menggunakan <code>img/default.jpg</code>.</small>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 15px;">
            <?php if ($editData): ?>
                <button type="submit" name="update_ruangan" class="btn btn-primary">Simpan Perubahan</button>
                <a href="?page=ruangan" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_ruangan" class="btn btn-success">+ Tambah Ruangan</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- tabel daftar ruangan operasi -->
<h4>Daftar Ruang Operasi Tersedia</h4>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 140px;">Foto Ruangan</th>
                <th>Link / Path Gambar</th>
                <th>Nama Ruangan</th>
                <th>Tipe / Fasilitas</th>
                <th style="width: 140px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $listRuangan = $ruangan->getAll(); 
            if (empty($listRuangan)): 
            ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b;">Belum ada data ruangan operasi.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($listRuangan as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['id']) ?></td>
                    <td>
                        <!-- fallback ke default kalau gambarnya kosong atau ga kebaca -->
                        <?php 
                        $imgPath = !empty($r['img']) ? $r['img'] : (!empty($r['image']) ? $r['image'] : 'img/default.jpg');
                        ?>
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="Foto <?= htmlspecialchars($r['nama_ruangan']) ?>" style="width: 100px; height: 65px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; display: block;" onerror="this.src='img/default.jpg';">
                    </td>
                    <td>
                        <code><?= htmlspecialchars($imgPath) ?></code>
                    </td>
                    <td><strong><?= htmlspecialchars($r['nama_ruangan']) ?></strong></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($r['tipe_ruangan']) ?></span></td>
                    <td style="text-align: center;">
                        <a href="?page=ruangan&action=edit&id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?page=ruangan&action=delete&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ruangan ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
