<?php
// view/admin/kendaraan/index.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Kelola Data Kendaraan</h1>
    <div class="fi-page-actions">
        <a href="index.php?c=admin&a=tambah_kendaraan" class="fi-btn fi-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Kendaraan Baru
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="fi-alert">
        <?php 
            if ($_GET['msg'] === 'sukses_tambah') echo "Data kendaraan baru berhasil ditambahkan!";
            elseif ($_GET['msg'] === 'sukses_edit') echo "Data kendaraan berhasil diperbarui!";
            elseif ($_GET['msg'] === 'sukses_hapus') echo "Data kendaraan berhasil dihapus!";
        ?>
    </div>
<?php endif; ?>

<form method="get" style="margin-bottom:14px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
    <input type="hidden" name="c" value="admin"><input type="hidden" name="a" value="kendaraan">
    <input type="hidden" name="per_page" value="<?= htmlspecialchars($perPage) ?>">
    <div class="fi-topbar-search" style="width:100%; max-width:320px; background:#fff;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" name="search" placeholder="Cari plat nomor atau pemilik..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
    </div>
</form>

<div class="fi-card">
    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Nomor Polisi (Plat)</th>
                    <th>Jenis Kendaraan</th>
                    <th>Warna</th>
                    <th>Nama Pemilik</th>
                    <th>Didaftarkan Oleh</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="7" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada data kendaraan.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= htmlspecialchars($row['id_kendaraan']) ?></span></td>
                    <td>
                        <strong style="font-family: 'Courier New', monospace; font-size: 14.5px; letter-spacing: 0.5px; color: var(--fi-gray-900);">
                            <?= htmlspecialchars($row['plat_nomor']) ?>
                        </strong>
                    </td>
                    <td>
                        <span class="fi-badge" style="text-transform: capitalize; background: #f3f4f6;">
                            <?= htmlspecialchars($row['jenis_kendaraan']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['warna'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($row['pemilik']) ?></td>
                    <td><span style="font-size: 12px; color: var(--fi-gray-500);"><?= htmlspecialchars($row['nama_petugas'] ?? 'Sistem') ?></span></td>
                    <td style="text-align: right;">
                        <div class="fi-row-actions" style="justify-content: flex-end;">
                            <a href="index.php?c=admin&a=ubah_kendaraan&id=<?= urlencode($row['id_kendaraan']) ?>">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>Edit
                            </a>
                            <a href="index.php?c=admin&a=hapus_kendaraan&id=<?= urlencode($row['id_kendaraan']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data kendaraan ini?')" style="color: #ef4444;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Hapus
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="fi-table-footer">
        <span>Menampilkan <?= $showingFrom ?> sampai <?= $showingTo ?> dari <?= $total ?> kendaraan</span>

        <div class="fi-per-page">
            <span>Per halaman</span>
            <select onchange="location.href='index.php?c=admin&a=kendaraan&page=1&per_page='+this.value+'&search=<?= urlencode($search) ?>'">
                <?php foreach ($allowedPerPage as $opt): ?>
                    <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fi-pagination">
            <a href="index.php?c=admin&a=kendaraan&page=<?= max(1, $page - 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">&lt;</a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="is-current"><?= $p ?></span>
                <?php else: ?>
                    <a href="index.php?c=admin&a=kendaraan&page=<?= $p ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <a href="index.php?c=admin&a=kendaraan&page=<?= min($totalPages, $page + 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">&gt;</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

