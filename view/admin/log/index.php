<?php
// view/admin/log/index.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Log Aktivitas Sistem</h1>
        <p style="color: var(--fi-gray-500); margin-top: 4px; font-size: 13.5px;">Audit trail dan catatan riwayat seluruh aktivitas pengguna dalam sistem aplikasi parkir.</p>
    </div>
</div>

<div class="fi-card">
    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Waktu Aktivitas</th>
                    <th>Pengguna / Pelaku</th>
                    <th>Role</th>
                    <th>Rincian Aktivitas</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada catatan log aktivitas.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= htmlspecialchars($row['id_log']) ?></span></td>
                    <td style="white-space: nowrap; font-family: monospace; font-size: 13px;">
                        <?= date('d/m/Y H:i:s', strtotime($row['waktu_aktivitas'])) ?>
                    </td>
                    <td>
                        <strong style="color: var(--fi-gray-900);"><?= htmlspecialchars($row['nama_lengkap'] ?? 'Sistem') ?></strong>
                        <span style="font-size: 12px; color: var(--fi-gray-400);">(<?= htmlspecialchars($row['username'] ?? '-') ?>)</span>
                    </td>
                    <td>
                        <span class="fi-badge" style="text-transform: capitalize; background: #f3f4f6;">
                            <?= htmlspecialchars($row['role'] ?? '-') ?>
                        </span>
                    </td>
                    <td style="font-weight: 500; color: var(--fi-gray-800);">
                        <?= htmlspecialchars($row['aktivitas']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="fi-table-footer">
        <span>Menampilkan <?= $showingFrom ?> sampai <?= $showingTo ?> dari <?= $total ?> log</span>

        <div class="fi-per-page">
            <span>Per halaman</span>
            <select onchange="location.href='index.php?c=admin&a=log&page=1&per_page='+this.value">
                <?php foreach ($allowedPerPage as $opt): ?>
                    <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fi-pagination">
            <a href="index.php?c=admin&a=log&page=<?= max(1, $page - 1) ?>&per_page=<?= $perPage ?>">&lt;</a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="is-current"><?= $p ?></span>
                <?php else: ?>
                    <a href="index.php?c=admin&a=log&page=<?= $p ?>&per_page=<?= $perPage ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <a href="index.php?c=admin&a=log&page=<?= min($totalPages, $page + 1) ?>&per_page=<?= $perPage ?>">&gt;</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

