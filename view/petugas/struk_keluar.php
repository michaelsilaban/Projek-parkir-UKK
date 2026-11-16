<?php
// view/petugas/struk_keluar.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - <?= $nomorTrx ?></title>
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
    </style>
</head>
<body>

<div class="thermal-wrapper">
    <div class="thermal-receipt" id="printable">
        <div class="thermal-header">
            <h2 style="margin: 0; font-size: 16px; font-weight: 800; letter-spacing: 0.5px;">SISTEM PARKIR UKK</h2>
            <div style="font-size: 11px; margin-top: 2px;">Jl. Pendidikan No. 1, Kota Ujian</div>
            <div style="font-size: 11px; font-weight: 700; margin-top: 4px;">BUKTI PEMBAYARAN PARKIR</div>
        </div>

        <div class="thermal-row">
            <span>No. Transaksi:</span>
            <strong><?= $nomorTrx ?></strong>
        </div>
        <div class="thermal-row">
            <span>Kasir / Petugas:</span>
            <span><?= htmlspecialchars($transaksi['nama_petugas']) ?></span>
        </div>

        <div class="thermal-divider"></div>

        <div style="text-align: center; margin: 8px 0;">
            <div style="font-size: 11px; color: #555;">NOMOR POLISI</div>
            <div style="font-size: 20px; font-weight: 900; letter-spacing: 1px; font-family: monospace;">
                <?= htmlspecialchars($transaksi['plat_nomor']) ?>
            </div>
            <div style="font-size: 11.5px; color: #333; margin-top: 2px;">
                <?= strtoupper(htmlspecialchars($transaksi['jenis_kendaraan'])) ?> &bull; <?= htmlspecialchars($transaksi['nama_area']) ?>
            </div>
        </div>

        <div class="thermal-divider"></div>

        <div class="thermal-row">
            <span>Waktu Masuk:</span>
            <span><?= date('d/m/Y H:i', strtotime($transaksi['waktu_masuk'])) ?></span>
        </div>
        <div class="thermal-row">
            <span>Waktu Keluar:</span>
            <span><?= !empty($transaksi['waktu_keluar']) ? date('d/m/Y H:i', strtotime($transaksi['waktu_keluar'])) : date('d/m/Y H:i') ?></span>
        </div>
        <div class="thermal-row">
            <span>Durasi:</span>
            <strong><?= (int)$transaksi['durasi_jam'] ?> Jam</strong>
        </div>
        <div class="thermal-row">
            <span>Tarif/Jam:</span>
            <span>Rp <?= number_format($transaksi['tarif_per_jam'], 0, ',', '.') ?></span>
        </div>
        <?php $diskonPersen = (int)($transaksi['diskon_persen'] ?? 0);
        if ($diskonPersen > 0):
            $subtotal = (int)$transaksi['durasi_jam'] * (float)$transaksi['tarif_per_jam'];
        ?>
        <div class="thermal-row">
            <span>Subtotal:</span>
            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
        </div>
        <div class="thermal-row">
            <span>Diskon Member <?= $diskonPersen ?>%:</span>
            <span>- Rp <?= number_format($subtotal - (float)$transaksi['biaya_total'], 0, ',', '.') ?></span>
        </div>
        <?php endif; ?>

        <div class="thermal-divider"></div>

        <div class="thermal-row" style="font-size: 15px; font-weight: 900; margin: 8px 0;">
            <span>TOTAL BAYAR:</span>
            <span>Rp <?= number_format($transaksi['biaya_total'], 0, ',', '.') ?></span>
        </div>

        <div style="text-align: center; margin: 8px 0; font-weight: 700; color: #15803d; border: 1px dashed #15803d; padding: 4px; border-radius: 4px;">
            *** LUNAS ***
        </div>

        <div class="thermal-divider"></div>

        <div class="thermal-footer">
            <div>Terima kasih atas kunjungan Anda.</div>
            <div>Semoga perjalanan Anda aman & menyenangkan.</div>
            <div style="margin-top: 6px; font-size: 10px; color: #666;">Dicetak: <?= date('d/m/Y H:i:s') ?></div>
        </div>
    </div>

    <!-- Tombol Tindakan -->
    <div class="no-print" style="display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; justify-content: center;">
        <button onclick="window.print()" class="fi-btn fi-btn-primary" style="padding: 10px 20px; font-size: 14px; background: #16a34a; border-color: #16a34a;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Struk Pembayaran
        </button>
        <a href="index.php?c=petugas&a=dashboard" class="fi-btn fi-btn-secondary" style="padding: 10px 18px; font-size: 14px;">
            Kembali ke Dashboard
        </a>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
        window.print();
    }
});
</script>

</body>
</html>

