<?php
// view/admin/data-parking-admin.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Kelola Area Parkir</h1>
    <div class="fi-page-actions">
        <a href="index.php?c=admin&a=tambah_area" class="fi-btn fi-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Area Baru
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="fi-alert">
        <?php 
            if ($_GET['msg'] === 'sukses_tambah') echo "Area parkir baru berhasil ditambahkan!";
            elseif ($_GET['msg'] === 'sukses_edit') echo "Data area parkir berhasil diperbarui!";
            elseif ($_GET['msg'] === 'sukses_hapus') echo "Area parkir berhasil dihapus!";
        ?>
    </div>
<?php endif; ?>

<form method="get" style="margin-bottom:14px; max-width:320px;">
    <input type="hidden" name="c" value="admin"><input type="hidden" name="a" value="area">
    <input type="hidden" name="per_page" value="<?= htmlspecialchars($perPage) ?>">
    <div class="fi-topbar-search" style="width:100%; background:#fff;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" name="search" placeholder="Cari nama atau ID area..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
    </div>
</form>

<div class="fi-card">
    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID Area</th>
                    <th>Nama Area Parkir</th>
                    <th>Kapasitas</th>
                    <th>Terisi</th>
                    <th>Slot Tersedia</th>
                    <th>Status Keterisian</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="7" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada data area parkir.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): 
                $sisa = max(0, $row['kapasitas'] - $row['terisi']);
                $persen = $row['kapasitas'] > 0 ? round(($row['terisi'] / $row['kapasitas']) * 100) : 0;
            ?>
                <tr>
                    <td><span class="fi-badge">#<?= htmlspecialchars($row['id_area']) ?></span></td>
                    <td style="font-weight: 600; color: var(--fi-gray-900);"><?= htmlspecialchars($row['nama_area']) ?></td>
                    <td><?= (int)$row['kapasitas'] ?> Slot</td>
                    <td><?= (int)$row['terisi'] ?> Terisi</td>
                    <td><strong style="color: <?= $sisa > 0 ? '#16a34a' : '#ef4444' ?>;"><?= $sisa ?> Slot</strong></td>
                    <td>
                        <span class="fi-badge <?= $persen >= 100 ? 'fi-badge-danger' : ($persen >= 75 ? 'fi-badge-warning' : 'fi-badge-success') ?>">
                            <?= $persen ?>% (<?= $persen >= 100 ? 'Penuh' : 'Tersedia' ?>)
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="fi-row-actions" style="justify-content: flex-end;">
                            <a href="index.php?c=admin&a=detail_area&id=<?= urlencode($row['id_area']) ?>"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>Detail</a>
                            <a href="index.php?c=admin&a=ubah_area&id=<?= urlencode($row['id_area']) ?>"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>Edit</a>
                            <a href="index.php?c=admin&a=hapus_area&id=<?= urlencode($row['id_area']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus area ini?')" style="color: #ef4444;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Hapus</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="fi-table-footer">
        <span>Menampilkan <?= $showingFrom ?> sampai <?= $showingTo ?> dari <?= $total ?> area</span>

        <div class="fi-per-page">
            <span>Per halaman</span>
            <select onchange="location.href='index.php?c=admin&a=area&page=1&per_page='+this.value+'&search=<?= urlencode($search) ?>'">
                <?php foreach ($allowedPerPage as $opt): ?>
                    <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fi-pagination">
            <a href="index.php?c=admin&a=area&page=<?= max(1, $page - 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">&lt;</a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="is-current"><?= $p ?></span>
                <?php else: ?>
                    <a href="index.php?c=admin&a=area&page=<?= $p ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <a href="index.php?c=admin&a=area&page=<?= min($totalPages, $page + 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">&gt;</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>
