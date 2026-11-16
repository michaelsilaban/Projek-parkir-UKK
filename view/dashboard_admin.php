<?php
// view/dashboard_admin.php
// Template tampilan (View). Semua data/variabel disediakan oleh Controller.
require __DIR__ . '/template/header.php';
?>

<div class="fi-page-header">
    <div>
        <h1 class="fi-page-title">Dashboard Admin</h1>
    </div>
</div>

    <!-- Status Area Parkir -->
  <div class="fi-page-header">
    <div>
        <h2 class="fi-page-title"></h2>
    </div>
  </div>
    <div class="fi-card" style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 16px; font-weight: 700; margin: 0;">Status Keterisian Area Parkir</h2>
            <a href="index.php?c=admin&a=area" style="font-size: 12.5px; color: var(--fi-primary); font-weight: 600;">Kelola Area &rarr;</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            <?php if (empty($areas)): ?>
                <div style="color: var(--fi-gray-400); text-align: center; padding: 20px;">Belum ada data area parkir.</div>
            <?php endif; ?>
            <?php foreach ($areas as $area): 
                $persen = $area['kapasitas'] > 0 ? round(($area['terisi'] / $area['kapasitas']) * 100) : 0;
                $barColor = $persen >= 90 ? '#ef4444' : ($persen >= 70 ? '#f59e0b' : '#10b981');
            ?>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 6px;">
                        <span><?= htmlspecialchars($area['nama_area']) ?></span>
                        <span><?= $area['terisi'] ?> / <?= $area['kapasitas'] ?> (<?= $persen ?>%)</span>
                    </div>
                    <div style="background: var(--fi-gray-100); border-radius: 999px; height: 8px; overflow: hidden;">
                        <div style="background: <?= $barColor ?>; width: <?= min(100, $persen) ?>%; height: 100%; border-radius: 999px; transition: width .3s;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<br>


    <!-- Log Aktivitas Terbaru -->
    <div class="fi-card" style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 16px; font-weight: 700; margin: 0;">Log Aktivitas Terkini</h2>
            <a href="index.php?c=admin&a=log" style="font-size: 12.5px; color: var(--fi-primary); font-weight: 600;">Lihat Semua &rarr;</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if (empty($recentLogs)): ?>
                <div style="color: var(--fi-gray-400); text-align: center; padding: 20px;">Belum ada log aktivitas.</div>
            <?php endif; ?>
            <?php foreach ($recentLogs as $log): ?>
                <div style="display: flex; align-items: flex-start; gap: 12px; padding-bottom: 10px; border-bottom: 1px solid var(--fi-gray-100);">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--fi-primary); margin-top: 6px; flex-shrink: 0;"></div>
                    <div style="flex: 1;">
                        <div style="font-size: 13px; color: var(--fi-gray-900); font-weight: 500;">
                            <?= htmlspecialchars($log['aktivitas']) ?>
                        </div>
                        <div style="font-size: 11.5px; color: var(--fi-gray-400); margin-top: 2px;">
                            Oleh: <strong><?= htmlspecialchars($log['nama_lengkap'] ?? 'Sistem') ?></strong> (<?= htmlspecialchars($log['role'] ?? '-') ?>) &bull; <?= date('d M Y H:i', strtotime($log['waktu_aktivitas'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/template/footer.php'; ?>
