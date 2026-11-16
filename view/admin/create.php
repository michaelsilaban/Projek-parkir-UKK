<?php
// view/admin/create.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Tambah Area Parkir Baru</h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=tambah_area" method="POST">
        <div class="fi-field">
            <label>Nama Area Parkir <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="nama_area" placeholder="Contoh: Area A - Motor, Basement B1 - Mobil" required>
        </div>

        <div class="fi-row" style="display:flex; gap:16px;">
            <div class="fi-field" style="flex:1;">
                <label>Kapasitas Maksimal (Slot) <span style="color:#ef4444;">*</span></label>
                <input class="fi-input" type="number" name="kapasitas" min="1" placeholder="Contoh: 50" required>
            </div>
            <div class="fi-field" style="flex:1;">
                <label>Slot Terisi Awal <span class="fi-hint">(Opsional)</span></label>
                <input class="fi-input" type="number" name="terisi" min="0" value="0">
            </div>
        </div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Area</button>
            <a href="index.php?c=admin&a=area" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>
