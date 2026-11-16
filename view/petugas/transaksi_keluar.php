<?php
// view/petugas/transaksi_keluar.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Check-Out & Pembayaran Parkir</h1>
        <p style="color: var(--fi-gray-500); margin-top: 4px; font-size: 13.5px;">Pilih kendaraan yang akan keluar untuk menghitung total durasi dan biaya parkir.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 24px;">
    <!-- Form Pilih / Cari Kendaraan -->
    <div class="fi-card" style="padding: 24px;">
        <h2 style="font-size: 16px; font-weight: 700; margin-top: 0; margin-bottom: 16px;">1. Pilih Kendaraan Aktif</h2>
        
        <form method="GET" style="margin-bottom: 20px;">
    <input type="hidden" name="c" value="petugas"><input type="hidden" name="a" value="transaksi_keluar">
            <div class="fi-field">
                <label>Pilih Dari Daftar Kendaraan Parkir</label>
                <select class="fi-select" name="id" onchange="this.form.submit()">
                    <option value="">-- Pilih Plat Nomor / Tiket --</option>
                    <?php foreach ($kendaraanAktif as $k): ?>
                        <option value="<?= $k['id_parkir'] ?>" <?= $selectedId === (int)$k['id_parkir'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['plat_nomor']) ?> - TIKET-<?= str_pad($k['id_parkir'], 5, '0', STR_PAD_LEFT) ?> (<?= strtoupper($k['jenis_kendaraan']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <div style="border-top: 1px solid var(--fi-gray-100); padding-top: 14px;">
            <div style="font-size: 12.5px; font-weight: 600; color: var(--fi-gray-500); margin-bottom: 10px;">
                DAFTAR KENDARAAN SEDANG PARKIR (<?= count($kendaraanAktif) ?>):
            </div>
            <div style="max-height: 280px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                <?php if (empty($kendaraanAktif)): ?>
                    <div style="font-size: 13px; color: var(--fi-gray-400); text-align: center; padding: 20px;">
                        Tidak ada kendaraan parkir yang aktif saat ini.
                    </div>
                <?php endif; ?>
                <?php foreach ($kendaraanAktif as $k): ?>
                    <a href="index.php?c=petugas&a=transaksi_keluar&id=<?= $k['id_parkir'] ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border: 1px solid <?= $selectedId === (int)$k['id_parkir'] ? 'var(--fi-primary)' : 'var(--fi-gray-200)' ?>; background: <?= $selectedId === (int)$k['id_parkir'] ? 'var(--fi-primary-light)' : '#fff' ?>; border-radius: 8px; font-size: 13px;">
                        <div>
                            <strong style="font-family: monospace; font-size: 14px;"><?= htmlspecialchars($k['plat_nomor']) ?></strong>
                            <div style="font-size: 11.5px; color: var(--fi-gray-500);"><?= htmlspecialchars($k['nama_area']) ?> &bull; <?= strtoupper($k['jenis_kendaraan']) ?></div>
                        </div>
                        <span class="fi-badge" style="background: #fff;">TIKET-<?= str_pad($k['id_parkir'], 5, '0', STR_PAD_LEFT) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Rincian Biaya & Tombol Bayar -->
    <div class="fi-card" style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; margin-top: 0; margin-bottom: 16px;">2. Kalkulasi Biaya Parkir</h2>

            <?php if ($selectedTransaksi && $selectedTransaksi['status'] === 'masuk'): ?>
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Nomor Plat Kendaraan:</span>
                        <strong style="font-family: monospace; font-size: 16px;"><?= htmlspecialchars($selectedTransaksi['plat_nomor']) ?></strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Jenis / Area Parkir:</span>
                        <span><?= strtoupper(htmlspecialchars($selectedTransaksi['jenis_kendaraan'])) ?> &bull; <?= htmlspecialchars($selectedTransaksi['nama_area']) ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Waktu Masuk:</span>
                        <span style="font-family: monospace;"><?= date('d/m/Y H:i:s', strtotime($selectedTransaksi['waktu_masuk'])) ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Waktu Keluar (Saat Ini):</span>
                        <span style="font-family: monospace; color: #d97706;"><?= date('d/m/Y H:i:s') ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Tarif per Jam:</span>
                        <span>Rp <?= number_format($selectedTransaksi['tarif_per_jam'], 0, ',', '.') ?> / jam</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Durasi Dihitung:</span>
                        <strong><?= $durasiEstimasi ?> Jam</strong>
                    </div>

                    <?php if ($diskonPersen > 0): ?>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Subtotal (tanpa diskon):</span>
                        <span>Rp <?= number_format($biayaSebelumDiskon, 0, ',', '.') ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--fi-gray-200);">
                        <span style="color: var(--fi-gray-500);">Diskon Member <?= $diskonPersen ?>%:</span>
                        <strong style="color: #16a34a;">- Rp <?= number_format($biayaSebelumDiskon - $biayaEstimasi, 0, ',', '.') ?></strong>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: 14px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 16px; text-align: center;">
                        <div style="font-size: 13px; color: #065f46; font-weight: 600; text-transform: uppercase;">Total Yang Harus Dibayar</div>
                        <div style="font-size: 32px; font-weight: 800; color: #047857; margin-top: 4px;">
                            Rp <?= number_format($biayaEstimasi, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>

                <form action="index.php?c=petugas&a=transaksi_keluar" method="POST" style="margin-top: 24px;">
                    <input type="hidden" name="id_parkir" value="<?= $selectedTransaksi['id_parkir'] ?>">
                    <button type="submit" class="fi-btn fi-btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 15px; font-weight: 700; background: #16a34a; border-color: #16a34a;" onclick="return confirm('Konfirmasi pembayaran dan selesaikan transaksi parkir?')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6L9 17l-5-5"/></svg>
                        Proses Keluar & Selesaikan Pembayaran
                    </button>
                </form>

            <?php else: ?>
                <div style="text-align: center; color: var(--fi-gray-400); padding: 48px 16px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; display: block; color: var(--fi-gray-300);"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <p style="font-size: 14px; margin: 0;">Silakan pilih kendaraan di sebelah kiri untuk menghitung durasi dan biaya parkir.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>

