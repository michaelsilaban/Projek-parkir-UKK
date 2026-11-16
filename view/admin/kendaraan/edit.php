<?php
// view/admin/kendaraan/edit.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Edit Kendaraan #<?= htmlspecialchars($kendaraan['id_kendaraan']) ?></h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=ubah_kendaraan" method="POST">
        <input type="hidden" name="id_kendaraan" value="<?= htmlspecialchars($kendaraan['id_kendaraan']) ?>">

        <div class="fi-field">
            <label>Nomor Polisi (Plat Nomor) <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="plat_nomor" value="<?= htmlspecialchars($kendaraan['plat_nomor']) ?>" style="text-transform: uppercase;" required>
        </div>

        <div class="fi-field">
            <label>Jenis Kendaraan <span style="color:#ef4444;">*</span></label>
            <select class="fi-select" name="jenis_kendaraan" required>
                <option value="motor" <?= $kendaraan['jenis_kendaraan'] === 'motor' ? 'selected' : '' ?>>Motor</option>
                <option value="mobil" <?= $kendaraan['jenis_kendaraan'] === 'mobil' ? 'selected' : '' ?>>Mobil</option>
                <option value="lainnya" <?= $kendaraan['jenis_kendaraan'] === 'lainnya' ? 'selected' : '' ?>>Lainnya (Bus / Truk / Khusus)</option>
            </select>
        </div>

        <div class="fi-row" style="display: flex; gap: 16px;">
            <div class="fi-field" style="flex: 1;">
                <label>Warna Kendaraan</label>
                <input class="fi-input" type="text" name="warna" value="<?= htmlspecialchars($kendaraan['warna']) ?>">
            </div>
            <div class="fi-field" style="flex: 1;">
                <label>Nama Pemilik <span style="color:#ef4444;">*</span></label>
                <input class="fi-input" type="text" name="pemilik" value="<?= htmlspecialchars($kendaraan['pemilik']) ?>" required>
            </div>
        </div>

        <div class="fi-hint" style="margin-bottom: 14px;">Kendaraan yang tersimpan di Data Kendaraan otomatis menjadi <strong>member</strong> (diskon 30% / jam) dan dapat dipilih petugas saat check-in.</div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Perubahan</button>
            <a href="index.php?c=admin&a=kendaraan" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

