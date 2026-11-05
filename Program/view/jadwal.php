<?php
// kalau lagi mau edit jadwal operasi, ambil data lamanya
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editData = $jadwal->getById($_GET['id']);
}

// ambil master data buat dropdown pilihan di form
$allPasien = $pasien->getAll();
$allDoctor = $doctor->getAll();
$allRuangan = $ruangan->getAll();
?>

<h3>Jadwal Pelaksanaan Operasi</h3>

<!-- form tambah jadwal baru atau edit jadwal yang udah ada -->
<div class="form-card">
    <h4><?= $editData ? 'Edit Jadwal Operasi' : 'Pendaftaran Jadwal Operasi Baru' ?></h4>
    <form method="POST" action="index.php?page=jadwal">
        <!-- simpan id jadwal kalau lagi proses edit -->
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label>Pilih Pasien *</label>
                <select name="pasien_id" required>
                    <option value="">-- Pilih Pasien --</option>
                    <?php foreach ($allPasien as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($editData && $editData['pasien_id'] == $p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nama']) ?> 
                            <!-- kasih tanda kalau pasien statusnya darurat -->
                            <?= $p['darurat'] == 1 ? '[DARURAT - ' . htmlspecialchars($p['penyakit']) . ']' : '(' . htmlspecialchars($p['penyakit']) . ')' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Dokter Penanggung Jawab *</label>
                <select name="doctor_id" required>
                    <option value="">-- Pilih Dokter Bedah --</option>
                    <?php foreach ($allDoctor as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($editData && $editData['doctor_id'] == $d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nama']) ?> - <?= htmlspecialchars($d['spesialis']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Ruang Operasi *</label>
                <select name="ruangan_id" required>
                    <option value="">-- Pilih Ruang Operasi --</option>
                    <?php foreach ($allRuangan as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($editData && $editData['ruangan_id'] == $r['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nama_ruangan']) ?> (<?= htmlspecialchars($r['tipe_ruangan']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Tindakan / Prosedur Operasi *</label>
                <input type="text" name="nama_jadwal" value="<?= htmlspecialchars($editData['nama_jadwal'] ?? '') ?>" required placeholder="Contoh: Operasi Apendektomi Laparoskopi">
            </div>

            <div class="form-group">
                <label>Waktu Mulai Operasi *</label>
                <input type="datetime-local" name="waktu_mulai" value="<?= $editData ? date('Y-m-d\TH:i', strtotime($editData['waktu_mulai'])) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Estimasi Waktu Selesai *</label>
                <input type="datetime-local" name="estimasi_selesai" value="<?= $editData ? date('Y-m-d\TH:i', strtotime($editData['estimasi_selesai'])) : '' ?>" required>
            </div>

            <!-- pilihan status cuma muncul waktu edit -->
            <?php if ($editData): ?>
            <div class="form-group">
                <label>Status Operasi *</label>
                <select name="status" required>
                    <option value="Menunggu" <?= $editData['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="Berlangsung" <?= $editData['status'] === 'Berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
                    <option value="Selesai" <?= $editData['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="Dibatalkan" <?= $editData['status'] === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 15px;">
            <?php if ($editData): ?>
                <button type="submit" name="update_jadwal" class="btn btn-primary">Simpan Perubahan Jadwal</button>
                <a href="?page=jadwal" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_jadwal" class="btn btn-success">+ Buat Jadwal Operasi</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- tabel daftar jadwal operasi -->
<h4>Daftar Antrean & Jadwal Operasi</h4>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">ID</th>
                <th>Tindakan Operasi</th>
                <th>Pasien</th>
                <th>Dokter Operator</th>
                <th>Ruangan</th>
                <th>Waktu Mulai & Selesai</th>
                <th>Status</th>
                <th style="width: 220px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $listJadwal = $jadwal->getAll(); 
            if (empty($listJadwal)): 
            ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748b;">Belum ada jadwal operasi yang terdaftar.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($listJadwal as $j): ?>
                <tr>
                    <td><?= htmlspecialchars($j['id']) ?></td>
                    <td><strong><?= htmlspecialchars($j['nama_jadwal']) ?></strong></td>
                    <td>
                        <?= htmlspecialchars($j['nama_pasien']) ?><br>
                        <?php if ($j['pasien_darurat'] == 1): ?>
                            <span class="badge badge-danger">DARURAT</span>
                        <?php else: ?>
                            <span class="badge badge-info">Reguler</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($j['nama_dokter']) ?><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($j['spesialis']) ?></small>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($j['nama_ruangan']) ?></strong><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($j['tipe_ruangan']) ?></small>
                    </td>
                    <td>
                        <small><strong>Mulai:</strong> <?= date('d M Y, H:i', strtotime($j['waktu_mulai'])) ?></small><br>
                        <small><strong>Selesai:</strong> <?= date('d M Y, H:i', strtotime($j['estimasi_selesai'])) ?></small>
                    </td>
                    <td>
                        <!-- badge warna-warni sesuai status jadwal -->
                        <?php 
                        if ($j['status'] === 'Menunggu') {
                            echo '<span class="badge badge-warning">Menunggu</span>';
                        } elseif ($j['status'] === 'Berlangsung') {
                            echo '<span class="badge badge-info">Berlangsung</span>';
                        } elseif ($j['status'] === 'Selesai') {
                            echo '<span class="badge badge-success">Selesai</span>';
                        } else {
                            echo '<span class="badge badge-danger">Dibatalkan</span>';
                        }
                        ?>
                    </td>
                    <td style="text-align: center;">
                        <!-- tombol cepat buat ganti status langsung tanpa harus buka form edit -->
                        <?php if ($j['status'] === 'Menunggu'): ?>
                            <a href="?page=jadwal&action=set_status&id=<?= $j['id'] ?>&status=Berlangsung" class="btn btn-primary btn-sm" title="Mulai Operasi">Mulai</a>
                            <a href="?page=jadwal&action=set_status&id=<?= $j['id'] ?>&status=Dibatalkan" class="btn btn-secondary btn-sm" title="Batalkan" onclick="return confirm('Batalkan jadwal operasi ini?');">Batal</a>
                        <?php elseif ($j['status'] === 'Berlangsung'): ?>
                            <a href="?page=jadwal&action=set_status&id=<?= $j['id'] ?>&status=Selesai" class="btn btn-success btn-sm" title="Tandai Selesai">Selesai</a>
                        <?php endif; ?>

                        <a href="?page=jadwal&action=edit&id=<?= $j['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?page=jadwal&action=delete&id=<?= $j['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
