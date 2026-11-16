<?php
// view/admin/tarif/index.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Kelola Tarif Parkir</h1>
    <div class="fi-page-actions">
        <a href="index.php?c=admin&a=tambah_tarif" class="fi-btn fi-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Tarif Baru
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="fi-alert">
        <?php 
            if ($_GET['msg'] === 'sukses_tambah') echo "Tarif parkir baru berhasil ditambahkan!";
            elseif ($_GET['msg'] === 'sukses_edit') echo "Data tarif parkir berhasil diperbarui!";
            elseif ($_GET['msg'] === 'sukses_hapus') echo "Tarif parkir berhasil dihapus!";
        ?>
    </div>
<?php endif; ?>

<div class="fi-card">
    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Jenis Kendaraan</th>
                    <th>Tarif Per Jam</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="4" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada master tarif parkir.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= htmlspecialchars($row['id_tarif']) ?></span></td>
                    <td style="font-weight: 600; color: var(--fi-gray-900); text-transform: uppercase;">
                        <?= htmlspecialchars($row['jenis_kendaraan']) ?>
                    </td>
                    <td>
                        <strong style="color: #16a34a; font-size: 15px;">
                            Rp <?= number_format($row['tarif_per_jam'], 0, ',', '.') ?>
                        </strong> / jam
                    </td>
                    <td style="text-align: right;">
                        <div class="fi-row-actions" style="justify-content: flex-end;">
                            <a href="index.php?c=admin&a=ubah_tarif&id=<?= urlencode($row['id_tarif']) ?>">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>Edit
                            </a>
                            <a href="index.php?c=admin&a=hapus_tarif&id=<?= urlencode($row['id_tarif']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus tarif ini?')" style="color: #ef4444;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Hapus
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

