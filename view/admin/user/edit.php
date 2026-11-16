<?php
// view/admin/user/edit.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Edit Pengguna #<?= htmlspecialchars($user['id_user']) ?></h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=ubah_pengguna" method="POST">
        <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id_user']) ?>">

        <div class="fi-field">
            <label>Nama Lengkap <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
        </div>

        <div class="fi-field">
            <label>Username <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="fi-field">
            <label>Password Baru <span class="fi-hint">(Kosongkan jika tidak ingin mengubah password)</span></label>
            <input class="fi-input" type="password" name="password" placeholder="Masukkan password baru jika ingin diganti">
        </div>

        <div class="fi-field">
            <label>Role / Hak Akses <span style="color:#ef4444;">*</span></label>
            <select class="fi-select" name="role" required>
                <option value="petugas" <?= $user['role'] === 'petugas' ? 'selected' : '' ?>>Petugas (Operasional Loket)</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator (Akses Penuh Kelola Sistem)</option>
                <option value="owner" <?= $user['role'] === 'owner' ? 'selected' : '' ?>>Owner (Akses Rekap Laporan Eksekutif)</option>
            </select>
        </div>

        <div class="fi-field">
            <label>Status Akun</label>
            <div class="fi-toggle-row">
                <input type="checkbox" name="status_aktif" id="status_aktif" value="1" <?= (int)$user['status_aktif'] === 1 ? 'checked' : '' ?> style="width:16px;height:16px;">
                <label for="status_aktif" style="margin:0; font-weight:400; color:var(--fi-gray-600);">Akun aktif dan diizinkan login</label>
            </div>
        </div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Perubahan</button>
            <a href="index.php?c=admin&a=pengguna" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

