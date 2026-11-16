<?php
// view/petugas/transaksi_masuk.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/../template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Check-In Kendaraan Masuk</h1>
        <p style="color: var(--fi-gray-500); margin-top: 4px; font-size: 13.5px;">Input data kendaraan yang memasuki area parkir untuk mencetak tiket/karcis parkir.</p>
    </div>
</div>

<div class="fi-form-card">
    <form action="index.php?c=petugas&a=transaksi_masuk" method="POST" id="formMasuk">
        <input type="hidden" name="tipe_masuk" id="tipeMasuk" value="manual">

        <!-- Pilihan tipe kendaraan -->
        <div style="display: flex; gap: 8px; margin-bottom: 20px; background: var(--fi-gray-100); padding: 4px; border-radius: 10px;">
            <button type="button" id="tabManual" class="tipe-tab is-active" onclick="setTipe('manual')">Input Manual (Umum)</button>
            <button type="button" id="tabMember" class="tipe-tab" onclick="setTipe('member')">
                Member <span class="fi-badge" style="background:#dcfce7; color:#15803d; margin-left:4px;">Diskon <?= $diskonMember ?>%</span>
            </button>
        </div>

        <!-- ===== MODE MANUAL ===== -->
        <div id="panelManual">
            <div class="fi-field">
                <label>Nomor Polisi (Plat Nomor) <span style="color:#ef4444;">*</span></label>
                <input class="fi-input" type="text" name="plat_nomor" id="platManual" placeholder="Contoh: B 1234 ABC" style="text-transform: uppercase; font-size: 16px; font-weight: 700; font-family: 'Courier New', monospace;" autofocus required>
            </div>

            <div class="fi-field">
                <label>Jenis Kendaraan <span style="color:#ef4444;">*</span></label>
                <select class="fi-select" name="jenis_kendaraan" id="jenisManual" required>
                    <?php foreach ($tarifs as $t): ?>
                        <option value="<?= htmlspecialchars($t['jenis_kendaraan']) ?>">
                            <?= strtoupper(htmlspecialchars($t['jenis_kendaraan'])) ?> (Rp <?= number_format($t['tarif_per_jam'], 0, ',', '.') ?> / jam)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fi-row" style="display: flex; gap: 16px;">
                <div class="fi-field" style="flex: 1;">
                    <label>Warna Kendaraan <span class="fi-hint">(Opsional)</span></label>
                    <input class="fi-input" type="text" name="warna" id="warnaManual" placeholder="Contoh: Hitam, Putih, Merah">
                </div>

                <div class="fi-field" style="flex: 1;">
                    <label>Nama Pengemudi / Pemilik <span class="fi-hint">(Opsional)</span></label>
                    <input class="fi-input" type="text" name="pemilik" id="pemilikManual" placeholder="Pengunjung Umum">
                </div>
            </div>
        </div>

        <!-- ===== MODE MEMBER ===== -->
        <div id="panelMember" style="display: none;">
            <?php if (empty($members)): ?>
                <div style="padding: 20px; text-align: center; color: var(--fi-gray-500); border: 1px dashed var(--fi-gray-300); border-radius: 10px; margin-bottom: 16px;">
                    Belum ada data member. Admin dapat menandai kendaraan sebagai member pada menu <strong>Data Kendaraan</strong>.
                </div>
            <?php else: ?>
                <div class="fi-field">
                    <label>Cari Member <span class="fi-hint">(plat / nama pemilik)</span></label>
                    <input class="fi-input" type="text" id="cariMember" placeholder="Ketik untuk menyaring daftar member..." autocomplete="off">
                </div>

                <div class="fi-field">
                    <label>Pilih Member <span style="color:#ef4444;">*</span></label>
                    <select class="fi-select" name="id_member" id="selectMember" disabled required>
                        <option value="">-- Pilih Member --</option>
                        <?php foreach ($members as $m):
                            $parkir = (int)$m['sedang_parkir'] > 0;
                            $tarifDasar = $tarifMap[$m['jenis_kendaraan']] ?? 0;
                        ?>
                            <option value="<?= (int)$m['id_kendaraan'] ?>"
                                    data-plat="<?= htmlspecialchars($m['plat_nomor']) ?>"
                                    data-pemilik="<?= htmlspecialchars($m['pemilik']) ?>"
                                    data-jenis="<?= htmlspecialchars($m['jenis_kendaraan']) ?>"
                                    data-warna="<?= htmlspecialchars($m['warna'] ?: '-') ?>"
                                    data-tarif="<?= $tarifDasar ?>"
                                    <?= $parkir ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($m['plat_nomor']) ?> - <?= htmlspecialchars($m['pemilik']) ?> (<?= strtoupper(htmlspecialchars($m['jenis_kendaraan'])) ?>)<?= $parkir ? ' - SEDANG PARKIR' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="infoMember" style="display: none; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; font-size: 13.5px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:#065f46;">Plat Nomor</span>
                        <strong id="infoPlat" style="font-family: monospace; font-size: 15px;"></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:#065f46;">Pemilik</span><span id="infoPemilik"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:#065f46;">Jenis / Warna</span><span id="infoJenis"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; border-top:1px dashed #6ee7b7; padding-top:8px; margin-top:8px;">
                        <span style="color:#065f46;">Tarif / jam (diskon <?= $diskonMember ?>%)</span>
                        <span><s id="infoTarifAsli" style="color:#9ca3af;"></s> <strong id="infoTarifDiskon" style="color:#047857;"></strong></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Area parkir (dipakai kedua mode) -->
        <div class="fi-field">
            <label>Area Parkir <span style="color:#ef4444;">*</span></label>
            <select class="fi-select" name="id_area" required>
                <?php foreach ($areas as $a): 
                    $sisa = max(0, $a['kapasitas'] - $a['terisi']);
                    $disabled = $sisa <= 0 ? 'disabled' : '';
                ?>
                    <option value="<?= $a['id_area'] ?>" <?= $disabled ?>>
                        <?= htmlspecialchars($a['nama_area']) ?> (Tersedia: <?= $sisa ?> Slot) <?= $disabled ? ' - PENUH' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fi-form-actions" style="margin-top: 24px;">
            <button type="submit" class="fi-btn fi-btn-primary" style="padding: 10px 24px; font-size: 14px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                Proses Masuk & Buat Tiket
            </button>
            <a href="index.php?c=petugas&a=dashboard" class="fi-btn fi-btn-secondary">Kembali</a>
        </div>
    </form>
</div>

<style>
    .tipe-tab {
        flex: 1; border: 0; background: transparent; padding: 9px 12px; border-radius: 8px;
        font-size: 13.5px; font-weight: 600; color: var(--fi-gray-500); cursor: pointer;
        font-family: inherit; display: inline-flex; align-items: center; justify-content: center;
    }
    .tipe-tab.is-active { background: #fff; color: var(--fi-gray-900); box-shadow: 0 1px 3px rgba(0,0,0,.12); }
</style>

<script>
(function () {
    const rp = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const diskon = <?= (int)$diskonMember ?>;

    const tipeInput   = document.getElementById('tipeMasuk');
    const panelManual = document.getElementById('panelManual');
    const panelMember = document.getElementById('panelMember');
    const tabManual   = document.getElementById('tabManual');
    const tabMember   = document.getElementById('tabMember');
    const selMember   = document.getElementById('selectMember');
    const cari        = document.getElementById('cariMember');
    const info        = document.getElementById('infoMember');

    const manualFields = ['platManual', 'jenisManual', 'warnaManual', 'pemilikManual']
        .map(id => document.getElementById(id));

    window.setTipe = function (tipe) {
        const isMember = tipe === 'member';
        tipeInput.value = tipe;
        panelManual.style.display = isMember ? 'none' : 'block';
        panelMember.style.display = isMember ? 'block' : 'none';
        tabManual.classList.toggle('is-active', !isMember);
        tabMember.classList.toggle('is-active', isMember);

        // field yang disembunyikan di-disable agar tidak ikut tervalidasi / terkirim
        manualFields.forEach(f => { f.disabled = isMember; });
        if (selMember) selMember.disabled = !isMember;

        if (isMember && cari) cari.focus();
        if (!isMember) document.getElementById('platManual').focus();
    };

    function tampilInfo() {
        if (!selMember) return;
        const opt = selMember.options[selMember.selectedIndex];
        if (!opt || !opt.value) { info.style.display = 'none'; return; }
        const tarif = parseFloat(opt.dataset.tarif) || 0;
        document.getElementById('infoPlat').textContent   = opt.dataset.plat;
        document.getElementById('infoPemilik').textContent = opt.dataset.pemilik;
        document.getElementById('infoJenis').textContent  = opt.dataset.jenis.toUpperCase() + ' / ' + opt.dataset.warna;
        document.getElementById('infoTarifAsli').textContent   = rp(tarif);
        document.getElementById('infoTarifDiskon').textContent = rp(tarif * (100 - diskon) / 100) + ' / jam';
        info.style.display = 'block';
    }

    if (selMember) {
        selMember.addEventListener('change', tampilInfo);
    }

    // Saring daftar member berdasarkan plat / nama pemilik
    if (cari) {
        cari.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            Array.from(selMember.options).forEach((opt, i) => {
                if (i === 0) return;
                const teks = (opt.dataset.plat + ' ' + opt.dataset.pemilik).toLowerCase();
                opt.hidden = q !== '' && !teks.includes(q);
            });
            // jika satu-satunya hasil, langsung pilih
            const tampil = Array.from(selMember.options).filter((o, i) => i > 0 && !o.hidden && !o.disabled);
            if (q !== '' && tampil.length === 1) {
                selMember.value = tampil[0].value;
                tampilInfo();
            }
        });
    }
})();
</script>

<?php require __DIR__ . '/../template/footer.php'; ?>

