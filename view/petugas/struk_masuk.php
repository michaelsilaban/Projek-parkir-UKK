<?php
// view/petugas/struk_masuk.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karcis Masuk - <?= $nomorTiket ?></title>
    <link rel="stylesheet" href="view/template/style.css">
    <style>
        body {
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .barcode-visual {
            letter-spacing: 4px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 16px;
            padding: 6px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin: 8px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="thermal-wrapper">
    <div class="thermal-receipt" id="printable">
        <div class="thermal-header">
            <h2 style="margin: 0; font-size: 16px; font-weight: 800; letter-spacing: 0.5px;">SISTEM PARKIR UKK</h2>
            <div style="font-size: 11px; margin-top: 2px;">Jl. Pendidikan No. 1, Kota Ujian</div>
            <div style="font-size: 11px;">KARCIS PARKIR MASUK</div>
        </div>

        <div class="barcode-visual">
            ||| | |||| || ||| |||| |
        </div>
        <div style="text-align: center; font-weight: 700; font-size: 13px; margin-bottom: 8px;">
            <?= $nomorTiket ?>
        </div>

        <div class="thermal-divider"></div>

        <div style="text-align: center; margin: 10px 0;">
            <div style="font-size: 11px; color: #555;">NOMOR POLISI</div>
            <div style="font-size: 22px; font-weight: 900; letter-spacing: 1px; font-family: monospace;">
                <?= htmlspecialchars($transaksi['plat_nomor']) ?>
            </div>
        </div>

        <div class="thermal-divider"></div>

        <div class="thermal-row">
            <span>Jenis:</span>
            <strong><?= strtoupper(htmlspecialchars($transaksi['jenis_kendaraan'])) ?></strong>
        </div>
        <div class="thermal-row">
            <span>Area:</span>
            <span><?= htmlspecialchars($transaksi['nama_area']) ?></span>
        </div>
        <div class="thermal-row">
            <span>Waktu Masuk:</span>
            <span><?= date('d/m/Y H:i:s', strtotime($transaksi['waktu_masuk'])) ?></span>
        </div>
        <div class="thermal-row">
            <span>Petugas:</span>
            <span><?= htmlspecialchars($transaksi['nama_petugas']) ?></span>
        </div>
        <div class="thermal-row">
            <span>Tarif:</span>
            <span>Rp <?= number_format($transaksi['tarif_per_jam'], 0, ',', '.') ?> / jam</span>
        </div>
        <?php if ((int)($transaksi['diskon_persen'] ?? 0) > 0): ?>
        <div class="thermal-row">
            <span>Member:</span>
            <strong>DISKON <?= (int)$transaksi['diskon_persen'] ?>% / jam</strong>
        </div>
        <?php endif; ?>

        <div class="thermal-divider"></div>

        <div class="thermal-footer">
            <div>PERHATIAN:</div>
            <div>Harap simpan karcis ini dengan baik.</div>
            <div>Kehilangan karcis dikenakan denda sesuai SOP.</div>
            <div style="margin-top: 6px; font-weight: 700;">TERIMA KASIH</div>
        </div>
    </div>

    <!-- Tombol Tindakan -->
    <div class="no-print" style="display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; justify-content: center;">
        <button onclick="window.print()" class="fi-btn fi-btn-primary" style="padding: 10px 20px; font-size: 14px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Karcis Sekarang
        </button>
        <a href="index.php?c=petugas&a=dashboard" class="fi-btn fi-btn-secondary" style="padding: 10px 18px; font-size: 14px;">
            Kembali ke Dashboard
        </a>
    </div>
</div>

<script>
// Auto print saat halaman selesai dimuat jika dibuka dari check-in baru
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
        window.print();
    }
});
</script>

</body>
</html>

