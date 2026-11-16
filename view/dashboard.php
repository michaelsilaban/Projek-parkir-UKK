<?php
// view/dashboard.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Dashboard Owner (Pemilik Tempat Parkir)</h1>
        <p style="color: var(--fi-gray-500); margin-top: 4px; font-size: 13.5px;">Selamat datang, <strong><?= htmlspecialchars($userName) ?></strong>. Pantau rekapitulasi operasional parkir secara menyeluruh.</p>
    </div>
    <div class="fi-page-actions">
        <a href="index.php?c=owner&a=laporan" class="fi-btn fi-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
            Buka Rekap Laporan Transaksi
        </a>
    </div>
</div>

<!-- Stat Grid Owner -->
<div class="fi-stat-grid">
    <div class="fi-stat-card">
        <div>
            <div class="fi-stat-label">Kendaraan Parkir Sekarang</div>
            <div class="fi-stat-value" style="color: #d97706;"><?= $stats['kendaraan_aktif'] ?> Unit</div>
            <span style="font-size: 12px; color: var(--fi-gray-400);">Slot terisi: <?= $stats['total_terisi'] ?> / <?= $stats['total_kapasitas'] ?></span>
        </div>
        <div class="fi-stat-icon" style="background: #fef3c7; color: #d97706;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17l1 3h12l1-3"/></svg>
        </div>
    </div>
    <div class="fi-stat-card">
        <div>
            <div class="fi-stat-label">Kendaraan Masuk Hari Ini</div>
            <div class="fi-stat-value" style="color: #2563eb;"><?= $stats['masuk_hari_ini'] ?> Unit</div>
            <span style="font-size: 12px; color: var(--fi-gray-400);">Reset otomatis tiap pergantian hari</span>
        </div>
        <div class="fi-stat-icon" style="background: #dbeafe; color: #2563eb;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
        </div>
    </div>
    <div class="fi-stat-card">
        <div>
            <div class="fi-stat-label">Kendaraan Keluar Hari Ini</div>
            <div class="fi-stat-value" style="color: #db2777;"><?= $stats['keluar_hari_ini'] ?> Unit</div>
            <span style="font-size: 12px; color: var(--fi-gray-400);">Pendapatan hari ini: Rp <?= number_format($stats['pendapatan_hari_ini'], 0, ',', '.') ?></span>
        </div>
        <div class="fi-stat-icon" style="background: #fce7f3; color: #db2777;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        </div>
    </div>
    <div class="fi-stat-card">
        <div>
            <div class="fi-stat-label">Total Pendapatan</div>
            <div class="fi-stat-value" style="color: #16a34a;">Rp <?= number_format($stats['total_pendapatan'], 0, ',', '.') ?></div>
            <span style="font-size: 12px; color: var(--fi-gray-400);">Dari <?= $stats['total_selesai'] ?> transaksi selesai</span>
        </div>
        <div class="fi-stat-icon" style="background: #dcfce7; color: #16a34a;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>
</div>

<!-- Ringkasan Transaksi Terakhir -->
<div class="fi-card">
    <div style="padding: 18px 20px; border-bottom: 1px solid var(--fi-gray-100); display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 16px; font-weight: 700; margin: 0;">Transaksi Terkini</h2>
        <a href="index.php?c=owner&a=laporan" style="font-size: 13px; color: var(--fi-primary); font-weight: 600;">Lihat Laporan Lengkap &rarr;</a>
    </div>

    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Area</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Durasi</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($recentTrx)): ?>
                <tr><td colspan="9" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Belum ada data transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($recentTrx as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= str_pad($row['id_parkir'], 5, '0', STR_PAD_LEFT) ?></span></td>
                    <td>
                        <strong style="font-family: monospace; font-size: 14px;"><?= htmlspecialchars($row['plat_nomor']) ?></strong>
                    </td>
                    <td><span class="fi-badge" style="text-transform: uppercase;"><?= htmlspecialchars($row['jenis_kendaraan']) ?></span></td>
                    <td><?= htmlspecialchars($row['nama_area']) ?></td>
                    <td style="font-size: 12.5px;"><?= date('d/m/y H:i', strtotime($row['waktu_masuk'])) ?></td>
                    <td style="font-size: 12.5px;"><?= !empty($row['waktu_keluar']) ? date('d/m/y H:i', strtotime($row['waktu_keluar'])) : '-' ?></td>
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
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/template/footer.php'; ?>