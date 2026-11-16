<?php
// view/owner/cetak_laporan.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Parkir (<?= date('d/m/Y', strtotime($tgl_mulai)) ?> - <?= date('d/m/Y', strtotime($tgl_selesai)) ?>)</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #111;
            margin: 0;
            padding: 30px;
            background: #f8fafc;
        }
        .report-page {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 36px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .report-header {
            text-align: center;
            border-bottom: 2px solid #111;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-subtitle {
            font-size: 13px;
            color: #555;
            margin-top: 4px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 18px;
            font-size: 12.5px;
        }
        .meta-table td {
            padding: 4px 0;
        }
        .summary-box {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }
        .summary-item {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 14px;
            background: #f8fafc;
        }
        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }
        .summary-value {
            font-size: 18px;
            font-weight: 800;
            margin-top: 4px;
            color: #0f172a;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table th {
            background: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
        }
        .data-table tr:nth-child(even) {
            background: #fafafa;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 220px;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        .action-bar {
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        .btn-print { background: #16a34a; color: #fff; }
        .btn-back { background: #64748b; color: #fff; }

        @media screen and (max-width: 700px) {
            body { padding: 12px; }
            .report-page { padding: 20px 14px; }
            .report-title { font-size: 16px; }
            .action-bar { flex-direction: column; align-items: stretch; gap: 10px; }
            .action-bar .btn { justify-content: center; }
            .meta-table, .meta-table tbody, .meta-table tr, .meta-table td {
                display: block;
                width: 100% !important;
                text-align: left !important;
            }
            .meta-table td { padding: 2px 0; }
            .meta-table tr { margin-bottom: 8px; }
            .summary-box { flex-direction: column; gap: 10px; }
            .data-table { display: block; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; }
            .signature-section { flex-direction: column; align-items: center; gap: 28px; }
        }

        @media print {
            body { background: #fff; padding: 0; }
            .report-page { box-shadow: none; border: none; padding: 0; max-width: 100%; }
            .action-bar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="index.php?c=owner&a=laporan" class="btn btn-back">&larr; Kembali ke Laporan</a>
    <button onclick="window.print()" class="btn btn-print">Cetak Laporan (Print / Simpan PDF)</button>
</div>

<div class="report-page">
    <div class="report-header">
        <h1 class="report-title">LAPORAN REKAPITULASI TRANSAKSI PARKIR</h1>
        <div class="report-subtitle">Sistem Pengelolaan Parkir Berbasis Web &bull; Uji Kompetensi Keahlian RPL</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 140px; font-weight: bold;">Periode Transaksi:</td>
            <td><?= date('d F Y', strtotime($tgl_mulai)) ?> s/d <?= date('d F Y', strtotime($tgl_selesai)) ?></td>
            <td style="width: 120px; font-weight: bold; text-align: right;">Tanggal Cetak:</td>
            <td style="width: 160px; text-align: right;"><?= date('d/m/Y H:i') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Filter Jenis:</td>
            <td><?= !empty($jenis) ? strtoupper($jenis) : 'Semua Jenis Kendaraan' ?></td>
            <td style="font-weight: bold; text-align: right;">Dicetak Oleh:</td>
            <td style="text-align: right;"><?= htmlspecialchars($_SESSION['data']['nama_lengkap'] ?? 'Owner') ?></td>
        </tr>
    </table>

    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">Total Transaksi</div>
            <div class="summary-value"><?= $rekap['total_transaksi'] ?> Kendaraan</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Durasi</div>
            <div class="summary-value"><?= $rekap['total_durasi'] ?> Jam</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Pendapatan</div>
            <div class="summary-value" style="color: #15803d;">Rp <?= number_format($rekap['total_pendapatan'], 0, ',', '.') ?></div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>No. Tiket</th>
                <th>Nomor Polisi</th>
                <th>Jenis</th>
                <th>Area</th>
                <th>Waktu Masuk</th>
                <th>Waktu Keluar</th>
                <th style="text-align: center;">Durasi</th>
                <th style="text-align: right;">Total Biaya</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="10" style="text-align:center; padding:20px; color:#999;">Tidak ada transaksi pada periode ini.</td></tr>
        <?php endif; ?>
        <?php $no = 1; foreach ($rows as $r): ?>
            <tr>
                <td style="text-align: center;"><?= $no++ ?></td>
                <td style="font-family: monospace;">#<?= str_pad($r['id_parkir'], 5, '0', STR_PAD_LEFT) ?></td>
                <td style="font-family: monospace; font-weight: bold;"><?= htmlspecialchars($r['plat_nomor']) ?></td>
                <td><?= strtoupper(htmlspecialchars($r['jenis_kendaraan'])) ?></td>
                <td><?= htmlspecialchars($r['nama_area']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['waktu_masuk'])) ?></td>
                <td><?= !empty($r['waktu_keluar']) ? date('d/m/Y H:i', strtotime($r['waktu_keluar'])) : '-' ?></td>
                <td style="text-align: center;"><?= (int)$r['durasi_jam'] ?> Jam</td>
                <td style="text-align: right; font-weight: bold;"><?= $r['status'] === 'keluar' ? ('Rp ' . number_format($r['biaya_total'], 0, ',', '.')) : '-' ?><?php if ($r['status'] === 'keluar' && (int)($r['diskon_persen'] ?? 0) > 0): ?><br><small style="font-weight: normal;">(Member -<?= (int)$r['diskon_persen'] ?>%)</small><?php endif; ?></td>
                <td style="text-align: center; font-weight: bold; color: <?= $r['status'] === 'keluar' ? '#15803d' : '#b45309' ?>;"><?= strtoupper($r['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f8fafc;">
                <td colspan="8" style="text-align: right; padding: 10px;">TOTAL KESELURUHAN:</td>
                <td style="text-align: right; color: #15803d; font-size: 13px;">Rp <?= number_format($rekap['total_pendapatan'], 0, ',', '.') ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div style="font-weight: bold;">Petugas Operasional</div>
            <div class="signature-space"></div>
            <div style="text-decoration: underline; font-weight: bold;">( ..................................... )</div>
            <div style="font-size: 11px; color: #666; margin-top: 3px;">NIP / ID Petugas</div>
        </div>

        <div class="signature-box">
            <div>Kota Ujian, <?= date('d F Y') ?></div>
            <div style="font-weight: bold;">Owner / Pimpinan Parkir</div>
            <div class="signature-space"></div>
            <div style="text-decoration: underline; font-weight: bold;">( <?= htmlspecialchars($_SESSION['data']['nama_lengkap'] ?? 'Pimpinan Parkir') ?> )</div>
            <div style="font-size: 11px; color: #666; margin-top: 3px;">Penanggung Jawab</div>
        </div>
    </div>
</div>

</body>
</html>

