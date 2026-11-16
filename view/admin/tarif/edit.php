<?php
// view/admin/tarif/edit.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Edit Tarif Parkir #<?= htmlspecialchars($tarif['id_tarif']) ?></h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=ubah_tarif" method="POST">
        <input type="hidden" name="id_tarif" value="<?= htmlspecialchars($tarif['id_tarif']) ?>">

        <div class="fi-field">
            <label>Jenis Kendaraan <span style="color:#ef4444;">*</span></label>
            <select class="fi-select" name="jenis_kendaraan" required>
                <option value="motor" <?= $tarif['jenis_kendaraan'] === 'motor' ? 'selected' : '' ?>>Motor</option>
                <option value="mobil" <?= $tarif['jenis_kendaraan'] === 'mobil' ? 'selected' : '' ?>>Mobil</option>
                <option value="lainnya" <?= $tarif['jenis_kendaraan'] === 'lainnya' ? 'selected' : '' ?>>Lainnya (Bus / Truk / Khusus)</option>
            </select>
        </div>

        <div class="fi-field">
            <label>Tarif Per Jam (Rp) <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="number" name="tarif_per_jam" value="<?= (float)$tarif['tarif_per_jam'] ?>" min="0" step="500" required>
        </div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Perubahan</button>
            <a href="index.php?c=admin&a=tarif" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

