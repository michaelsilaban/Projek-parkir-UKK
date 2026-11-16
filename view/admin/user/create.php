<?php
// view/admin/user/create.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../../template/header.php';
?>

<div class="fi-page-header">
    <h1 class="fi-page-title">Tambah Pengguna Baru</h1>
</div>

<div class="fi-form-card">
    <form action="index.php?c=admin&a=tambah_pengguna" method="POST">
        <div class="fi-field">
            <label>Nama Lengkap <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="fi-field">
            <label>Username <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="text" name="username" placeholder="Masukkan username unik" required>
        </div>

        <div class="fi-field">
            <label>Password <span style="color:#ef4444;">*</span></label>
            <input class="fi-input" type="password" name="password" placeholder="Masukkan password" required>
        </div>

        <div class="fi-field">
            <label>Role / Hak Akses <span style="color:#ef4444;">*</span></label>
            <select class="fi-select" name="role" required>
                <option value="petugas" selected>Petugas (Operasional Loket)</option>
                <option value="admin">Administrator (Akses Penuh Kelola Sistem)</option>
                <option value="owner">Owner (Akses Rekap Laporan Eksekutif)</option>
            </select>
        </div>

        <div class="fi-field">
            <label>Status Akun</label>
            <div class="fi-toggle-row">
                <input type="checkbox" name="status_aktif" id="status_aktif" value="1" checked style="width:16px;height:16px;">
                <label for="status_aktif" style="margin:0; font-weight:400; color:var(--fi-gray-600);">Akun aktif dan diizinkan login</label>
            </div>
        </div>

        <div class="fi-form-actions">
            <button type="submit" class="fi-btn fi-btn-primary">Simpan Pengguna</button>
            <a href="index.php?c=admin&a=pengguna" class="fi-btn fi-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../template/footer.php'; ?>

