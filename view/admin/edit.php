<?php
// view/admin/edit.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Edit Area Parkir #<?= htmlspecialchars($area['id_area']) ?></h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=ubah_area" method="POST">
        <input type="hidden" name="id_area" value="<?= htmlspecialchars($area['id_area']) ?>">

        <div class="fi-field">
            <label>Nama Area Parkir <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="nama_area" value="<?= htmlspecialchars($area['nama_area']) ?>" required>
        </div>

        <div class="fi-row" style="display:flex; gap:16px;">
            <div class="fi-field" style="flex:1;">
                <label>Kapasitas Maksimal (Slot) <span style="color:#ef4444;">*</span></label>
                <input class="fi-input" type="number" name="kapasitas" value="<?= (int)$area['kapasitas'] ?>" min="1" required>
            </div>
            <div class="fi-field" style="flex:1;">
                <label>Slot Terisi Saat Ini</label>
                <input class="fi-input" type="number" name="terisi" value="<?= (int)$area['terisi'] ?>" min="0" required>
            </div>
        </div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Perubahan</button>
            <a href="index.php?c=admin&a=area" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>
