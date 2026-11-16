<?php
// view/admin/tarif/create.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Tambah Tarif Parkir Baru</h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=tambah_tarif" method="POST">
        <div class="fi-field">
            <label>Jenis Kendaraan <span style="color:#ef4444;">*</span></label>
            <select class="fi-select" name="jenis_kendaraan" required>
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
                <option value="lainnya">Lainnya (Bus / Truk / Khusus)</option>
            </select>
        </div>

        <div class="fi-field">
            <label>Tarif Per Jam (Rp) <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="number" name="tarif_per_jam" min="0" step="500" placeholder="Contoh: 2000, 5000" required>
        </div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Tarif</button>
            <a href="index.php?c=admin&a=tarif" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

