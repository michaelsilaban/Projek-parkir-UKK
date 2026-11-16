<?php
// view/petugas/riwayat.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Riwayat Transaksi Parkir</h1>
        <p style="color: var(--fi-gray-500); margin-top: 4px; font-size: 13.5px;">Daftar seluruh transaksi kendaraan masuk dan keluar beserta pencetakan karcis / struk.</p>
    </div>
</div>

<form method="get" class="fi-filter-form" style="display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap;">
    <input type="hidden" name="c" value="petugas"><input type="hidden" name="a" value="riwayat">
    <input type="hidden" name="per_page" value="<?= htmlspecialchars($perPage) ?>">
    
    <div class="fi-topbar-search" style="background:#fff; width: 260px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" name="search" placeholder="Cari plat nomor atau area..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
    </div>

    <select class="fi-select" name="status" style="width: 160px; padding: 7px 10px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="masuk" <?= $statusFilter === 'masuk' ? 'selected' : '' ?>>Sedang Masuk</option>
        <option value="keluar" <?= $statusFilter === 'keluar' ? 'selected' : '' ?>>Sudah Keluar</option>
    </select>
</form>

<div class="fi-card">
    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Nomor Polisi</th>
                    <th>Jenis</th>
                    <th>Area</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Durasi</th>
                    <th>Biaya Parkir</th>
                    <th>Status</th>
                    <th style="text-align: right;">Cetak Struk</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="10" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada riwayat transaksi parkir.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= str_pad($row['id_parkir'], 5, '0', STR_PAD_LEFT) ?></span></td>
                    <td>
                        <strong style="font-family: 'Courier New', monospace; font-size: 14px; color: var(--fi-gray-900);">
                            <?= htmlspecialchars($row['plat_nomor']) ?>
                        </strong>
                    </td>
                    <td><span class="fi-badge" style="text-transform: uppercase;"><?= htmlspecialchars($row['jenis_kendaraan']) ?></span></td>
                    <td><?= htmlspecialchars($row['nama_area']) ?></td>
                    <td style="font-size: 12.5px;"><?= date('d/m/y H:i', strtotime($row['waktu_masuk'])) ?></td>
                    <td style="font-size: 12.5px;">
                        <?= !empty($row['waktu_keluar']) ? date('d/m/y H:i', strtotime($row['waktu_keluar'])) : '<span style="color:var(--fi-gray-400);">-</span>' ?>
                    </td>
                    <td><?= $row['status'] === 'keluar' ? ((int)$row['durasi_jam'] . ' Jam') : '<span style="color:#d97706;">Berjalan</span>' ?></td>
                    <td>
                        <strong style="color: <?= $row['status'] === 'keluar' ? '#16a34a' : 'var(--fi-gray-400)' ?>;">
                            <?= $row['status'] === 'keluar' ? ('Rp ' . number_format($row['biaya_total'], 0, ',', '.')) : '-' ?>
                            <?php if ($row['status'] === 'keluar' && (int)($row['diskon_persen'] ?? 0) > 0): ?><span class="fi-badge" style="background:#dcfce7; color:#15803d; font-size:10.5px; margin-left:4px;">Member -<?= (int)$row['diskon_persen'] ?>%</span><?php endif; ?>
                        </strong>
                    </td>
                    <td>
                        <span class="fi-badge <?= $row['status'] === 'masuk' ? 'fi-badge-warning' : 'fi-badge-success' ?>">
                            <?= strtoupper($row['status']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="fi-row-actions" style="justify-content: flex-end;">
                            <?php if ($row['status'] === 'masuk'): ?>
                                <a href="index.php?c=petugas&a=struk_masuk&id=<?= urlencode($row['id_parkir']) ?>" class="fi-btn-secondary" style="padding: 4px 8px; font-size: 11.5px; border-radius: 6px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                    Karcis Masuk
                                </a>
                                <a href="index.php?c=petugas&a=transaksi_keluar&id=<?= urlencode($row['id_parkir']) ?>" class="fi-btn-success" style="padding: 4px 8px; font-size: 11.5px; border-radius: 6px;">
                                    Keluar &rarr;
                                </a>
                            <?php else: ?>
                                <a href="index.php?c=petugas&a=struk_keluar&id=<?= urlencode($row['id_parkir']) ?>" class="fi-btn-secondary" style="padding: 4px 8px; font-size: 11.5px; border-radius: 6px; color: #16a34a;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                    Struk Bayar
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="fi-table-footer">
        <span>Menampilkan <?= $showingFrom ?> sampai <?= $showingTo ?> dari <?= $total ?> transaksi</span>

        <div class="fi-per-page">
            <span>Per halaman</span>
            <select onchange="location.href='index.php?c=petugas&a=riwayat&page=1&per_page='+this.value+'&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>'">
                <?php foreach ($allowedPerPage as $opt): ?>
                    <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fi-pagination">
            <a href="index.php?c=petugas&a=riwayat&page=<?= max(1, $page - 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>">&lt;</a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="is-current"><?= $p ?></span>
                <?php else: ?>
                    <a href="index.php?c=petugas&a=riwayat&page=<?= $p ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <a href="index.php?c=petugas&a=riwayat&page=<?= min($totalPages, $page + 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>">&gt;</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>

