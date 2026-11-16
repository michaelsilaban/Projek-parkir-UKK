<?php
// view/admin/user/index.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Kelola Data Pengguna</h1>
    <div class="fi-page-actions">
        <a href="index.php?c=admin&a=tambah_pengguna" class="fi-btn fi-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pengguna Baru
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="fi-alert">
        <?php 
            if ($_GET['msg'] === 'sukses_tambah') echo "Pengguna baru berhasil ditambahkan!";
            elseif ($_GET['msg'] === 'sukses_edit') echo "Data pengguna berhasil diperbarui!";
            elseif ($_GET['msg'] === 'sukses_hapus') echo "Pengguna berhasil dihapus!";
            elseif ($_GET['msg'] === 'sukses_status') echo "Status akun pengguna berhasil diubah!";
        ?>
    </div>
<?php endif; ?>

<form method="get" class="fi-filter-form" style="display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap;">
    <input type="hidden" name="c" value="admin"><input type="hidden" name="a" value="pengguna">
    <input type="hidden" name="per_page" value="<?= htmlspecialchars($perPage) ?>">
    
    <div class="fi-topbar-search" style="background:#fff; width: 260px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" name="search" placeholder="Cari nama atau username..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
    </div>

    <select class="fi-select" name="role" style="width: 150px; padding: 7px 10px;" onchange="this.form.submit()">
        <option value="">Semua Role</option>
        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="petugas" <?= $roleFilter === 'petugas' ? 'selected' : '' ?>>Petugas</option>
        <option value="owner" <?= $roleFilter === 'owner' ? 'selected' : '' ?>>Owner</option>
    </select>
</form>

<div class="fi-card">
    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status Akun</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="6" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada data pengguna.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= htmlspecialchars($row['id_user']) ?></span></td>
                    <td style="font-weight: 600; color: var(--fi-gray-900);"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                    <td><code><?= htmlspecialchars($row['username']) ?></code></td>
                    <td>
                        <span class="fi-badge" style="text-transform: capitalize; background: <?= $row['role'] === 'admin' ? '#fef3c7; color:#b45309;' : ($row['role'] === 'petugas' ? '#dbeafe; color:#1d4ed8;' : '#f3e8ff; color:#7e22ce;') ?>">
                            <?= htmlspecialchars($row['role']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ((int)$row['status_aktif'] === 1): ?>
                            <a href="index.php?c=admin&a=toggle_pengguna&id=<?= urlencode($row['id_user']) ?>" title="Klik untuk nonaktifkan" class="fi-badge fi-badge-success" onclick="return confirm('Nonaktifkan akun pengguna ini?')">
                                &bull; Aktif
                            </a>
                        <?php else: ?>
                            <a href="index.php?c=admin&a=toggle_pengguna&id=<?= urlencode($row['id_user']) ?>" title="Klik untuk aktifkan" class="fi-badge fi-badge-danger" onclick="return confirm('Aktifkan kembali akun ini?')">
                                &bull; Non-Aktif
                            </a>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        <div class="fi-row-actions" style="justify-content: flex-end;">
                            <a href="index.php?c=admin&a=ubah_pengguna&id=<?= urlencode($row['id_user']) ?>">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>Edit
                            </a>
                            <?php if ($row['id_user'] != ($_SESSION['data']['id_user'] ?? 0)): ?>
                                <a href="index.php?c=admin&a=hapus_pengguna&id=<?= urlencode($row['id_user']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')" style="color: #ef4444;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Hapus
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
        <span>Menampilkan <?= $showingFrom ?> sampai <?= $showingTo ?> dari <?= $total ?> pengguna</span>

        <div class="fi-per-page">
            <span>Per halaman</span>
            <select onchange="location.href='index.php?c=admin&a=pengguna&page=1&per_page='+this.value+'&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>'">
                <?php foreach ($allowedPerPage as $opt): ?>
                    <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fi-pagination">
            <a href="index.php?c=admin&a=pengguna&page=<?= max(1, $page - 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>">&lt;</a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="is-current"><?= $p ?></span>
                <?php else: ?>
                    <a href="index.php?c=admin&a=pengguna&page=<?= $p ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <a href="index.php?c=admin&a=pengguna&page=<?= min($totalPages, $page + 1) ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>">&gt;</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

