<?php
// view/owner/laporan.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<!-- Form Filter Rentang Waktu -->
<div class="fi-card" style="padding: 20px; margin-bottom: 24px;">
  <div>
        <h1 class="fi-page-title">Cari Data Transaksi Parkir Anda</h1>
  </div>
  <br>
    <form method="GET" class="fi-filter-form" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
    <input type="hidden" name="c" value="owner"><input type="hidden" name="a" value="laporan">
        <div style="flex: 1; min-width: 160px;">
            <label style="display: block; font-size: 12.5px; font-weight: 600; color: var(--fi-gray-700); margin-bottom: 6px;">Dari Tanggal</label>
            <input type="date" name="tgl_mulai" class="fi-input" value="<?= htmlspecialchars($tgl_mulai) ?>" required>
        </div>

        <div style="flex: 1; min-width: 160px;">
            <label style="display: block; font-size: 12.5px; font-weight: 600; color: var(--fi-gray-700); margin-bottom: 6px;">Sampai Tanggal</label>
            <input type="date" name="tgl_selesai" class="fi-input" value="<?= htmlspecialchars($tgl_selesai) ?>" required>
        </div>

        <div style="width: 160px;">
            <label style="display: block; font-size: 12.5px; font-weight: 600; color: var(--fi-gray-700); margin-bottom: 6px;">Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="fi-select">
                <option value="">Semua Jenis</option>
                <?php foreach ($tarifs as $t): ?>
                    <option value="<?= htmlspecialchars($t['jenis_kendaraan']) ?>" <?= $jenis === $t['jenis_kendaraan'] ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($t['jenis_kendaraan'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="width: 150px;">
            <label style="display: block; font-size: 12.5px; font-weight: 600; color: var(--fi-gray-700); margin-bottom: 6px;">Status Parkir</label>
            <select name="status" class="fi-select">
                <option value="">Semua Status</option>
                <option value="keluar" <?= $status === 'keluar' ? 'selected' : '' ?>>Selesai (Keluar)</option>
                <option value="masuk" <?= $status === 'masuk' ? 'selected' : '' ?>>Masih Parkir</option>
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="fi-btn fi-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                Filter Data
            </button>
            <a href="index.php?c=owner&a=laporan" class="fi-btn fi-btn-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Ringkasan Metrik Periode Terpilih -->
<div class="fi-stat-grid">
    <div class="fi-stat-card">
        <div>
            <div class="fi-stat-label">Total Transaksi</div>
            <div class="fi-stat-value"><?= $rekap['total_transaksi'] ?> Kendaraan</div>
            <span style="font-size: 12px; color: var(--fi-gray-400);"><?= $rekap['count_keluar'] ?> selesai, <?= $rekap['count_masuk'] ?> aktif</span>
        </div>
        <div class="fi-stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
    </div>

    <div class="fi-stat-card">
        <div>
            <div class="fi-stat-label">Total Durasi Parkir</div>
            <div class="fi-stat-value" style="color: #2563eb;"><?= $rekap['total_durasi'] ?> Jam</div>
            <span style="font-size: 12px; color: var(--fi-gray-400);">Akumulasi seluruh kendaraan</span>
        </div>
        <div class="fi-stat-icon" style="background: #dbeafe; color: #2563eb;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
    </div>
</div>

<!-- Tabel Rincian Data Transaksi -->
<div class="fi-card">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--fi-gray-100); display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 15px; font-weight: 700; margin: 0;">Rincian Transaksi Parkir (<?= count($rows) ?> Data)</h2>
    </div>

    <div class="fi-table-wrap">
        <table class="fi-table">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Nomor Polisi</th>
                    <th>Jenis Kendaraan</th>
                    <th>Area Parkir</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Durasi</th>
                    <th>Tarif / Jam</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="10" style="text-align:center; color:var(--fi-gray-400); padding:32px;">Tidak ditemukan data transaksi pada rentang tanggal ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><span class="fi-badge">#<?= str_pad($row['id_parkir'], 5, '0', STR_PAD_LEFT) ?></span></td>
                    <td>
                        <strong style="font-family: monospace; font-size: 14px; color: var(--fi-gray-900);">
                            <?= htmlspecialchars($row['plat_nomor']) ?>
                        </strong>
                    </td>
                    <td><span class="fi-badge" style="text-transform: uppercase;"><?= htmlspecialchars($row['jenis_kendaraan']) ?></span></td>
                    <td><?= htmlspecialchars($row['nama_area']) ?></td>
                    <td style="font-size: 12.5px;"><?= date('d/m/Y H:i', strtotime($row['waktu_masuk'])) ?></td>
                    <td style="font-size: 12.5px;">
                        <?= !empty($row['waktu_keluar']) ? date('d/m/Y H:i', strtotime($row['waktu_keluar'])) : '<span style="color:var(--fi-gray-400);">-</span>' ?>
                    </td>
                    <td><?= $row['status'] === 'keluar' ? ((int)$row['durasi_jam'] . ' Jam') : '<span style="color:#d97706;">Berjalan</span>' ?></td>
                    <td>Rp <?= number_format($row['tarif_per_jam'], 0, ',', '.') ?></td>
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

<?php require __DIR__ . '/../template/footer.php'; ?>

