<?php
// view/admin/view.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title"><?= htmlspecialchars($area['nama_area']) ?></h1>
</div>

<div class="fi-form-card">
    <div class="fi-field">
        <label>ID Area</label>
        <div style="font-weight: 600; color: var(--fi-gray-900);">#<?= htmlspecialchars($area['id_area']) ?></div>
    </div>
    <div class="fi-field">
        <label>Nama Area</label>
        <div style="font-size: 15px; font-weight: 600; color: var(--fi-gray-900);"><?= htmlspecialchars($area['nama_area']) ?></div>
    </div>
    <div class="fi-row" style="display: flex; gap: 24px; margin-bottom: 18px;">
        <div class="fi-field" style="flex:1;">
            <label>Kapasitas Maksimal</label>
            <div style="font-size: 16px; font-weight: 700;"><?= (int)$area['kapasitas'] ?> Slot</div>
        </div>
        <div class="fi-field" style="flex:1;">
            <label>Terisi Saat Ini</label>
            <div style="font-size: 16px; font-weight: 700; color: #d97706;"><?= (int)$area['terisi'] ?> Slot</div>
        </div>
        <div class="fi-field" style="flex:1;">
            <label>Slot Tersedia</label>
            <div style="font-size: 16px; font-weight: 700; color: #16a34a;"><?= $sisaSlot ?> Slot</div>
        </div>
    </div>

    <div class="fi-field">
        <label>Persentase Keterisian</label>
        <div style="display: flex; align-items: center; gap: 12px; margin-top: 4px;">
            <div style="flex: 1; background: var(--fi-gray-100); border-radius: 999px; height: 10px; overflow: hidden;">
                <div style="background: <?= $persen >= 100 ? '#ef4444' : ($persen >= 75 ? '#f59e0b' : '#10b981') ?>; width: <?= min(100, $persen) ?>%; height: 100%;"></div>
            </div>
            <span style="font-weight: 600; font-size: 13px;"><?= $persen ?>%</span>
        </div>
    </div>

    <div class="fi-form-actions" style="margin-top: 24px;">
        <a href="index.php?c=admin&a=ubah_area&id=<?= urlencode($area['id_area']) ?>" class="fi-btn fi-btn-primary">Edit Area Ini</a>
        <a href="index.php?c=admin&a=area" class="fi-btn fi-btn-secondary">Kembali</a>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>
