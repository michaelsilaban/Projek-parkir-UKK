<?php
// view/dashboard_petugas.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Dashboard Petugas</h1>
    </div>
    <div class="fi-page-actions">
        <a href="index.php?c=petugas&a=transaksi_masuk" class="fi-btn fi-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            + Kendaraan Masuk (Check-In)
        </a>
    </div>
</div>

<!-- Ketersediaan Area Parkir Saat Ini -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 16px; margin-bottom: 24px;">
    <?php foreach ($areas as $a): 
        $sisa = max(0, $a['kapasitas'] - $a['terisi']);
        $penuh = $sisa <= 0;
    ?>
        <div class="fi-card" style="padding: 16px; border-left: 4px solid <?= $penuh ? '#ef4444' : '#10b981' ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="font-size: 14px; font-weight: 700; margin: 0; color: var(--fi-gray-900);"><?= htmlspecialchars($a['nama_area']) ?></h3>
                    <div style="font-size: 12px; color: var(--fi-gray-500); margin-top: 4px;">Kapasitas: <?= $a['kapasitas'] ?> | Terisi: <?= $a['terisi'] ?></div>
                </div>
                <span class="fi-badge <?= $penuh ? 'fi-badge-danger' : 'fi-badge-success' ?>">
                    <?= $penuh ? 'Penuh' : "$sisa Slot Kosong" ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Tabel Kendaraan yang Sedang Parkir -->
<div class="fi-card">
    <div style="padding: 18px 20px; border-bottom: 1px solid var(--fi-gray-100); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; margin: 0;">Kendaraan Sedang Parkir (Menunggu Keluar)</h2>
            <div style="font-size: 12.5px; color: var(--fi-gray-400); margin-top: 2px;">Klik tombol 'Proses Keluar' untuk menghitung biaya dan mencetak struk.</div>
        </div>
        <a href="index.php?c=petugas&a=transaksi_keluar" class="fi-btn fi-btn-secondary" style="font-size: 12.5px;">
            Cari Plat Nomor / Keluar Cepat &rarr;
        </a>
    </div>

    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Warna</th>
                    <th>Area Parkir</th>
                    <th>Waktu Masuk</th>
                    <th>Durasi Berjalan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kendaraanAktif)): ?>
                <tr><td colspan="8" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Tidak ada kendaraan yang sedang parkir saat ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($kendaraanAktif as $item): 
                $selisih = time() - strtotime($item['waktu_masuk']);
                $jam = floor($selisih / 3600);
                $menit = floor(($selisih % 3600) / 60);
            ?>
                <tr>
                    <td><span class="fi-badge">TIKET-<?= str_pad($item['id_parkir'], 5, '0', STR_PAD_LEFT) ?></span></td>
                    <td>
                        <strong style="font-family: 'Courier New', monospace; font-size: 15px; letter-spacing: 0.5px; color: var(--fi-gray-900);">
                            <?= htmlspecialchars($item['plat_nomor']) ?>
                        </strong>
                    </td>
                    <td>
                        <span class="fi-badge" style="text-transform: uppercase;">
                            <?= htmlspecialchars($item['jenis_kendaraan']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($item['warna'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($item['nama_area']) ?></td>
                    <td style="font-family: monospace; font-size: 12.5px;"><?= date('d/m/Y H:i', strtotime($item['waktu_masuk'])) ?></td>
                    <td>
                        <span class="fi-badge fi-badge-warning">
                            <?= $jam ?> Jam <?= $menit ?> Menit
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="fi-row-actions" style="justify-content: flex-end;">
                            <a href="index.php?c=petugas&a=struk_masuk&id=<?= urlencode($item['id_parkir']) ?>" title="Cetak Ulang Tiket Masuk" style="color: var(--fi-gray-600);">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Tiket
                            </a>
                            <a href="index.php?c=petugas&a=transaksi_keluar&id=<?= urlencode($item['id_parkir']) ?>" class="fi-btn-success" style="padding: 5px 10px; font-size: 12px; border-radius: 6px;">
                                Proses Keluar &rarr;
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/template/footer.php'; ?>