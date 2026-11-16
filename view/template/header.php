<?php
// view/template/header.php
// Template Header & Sidebar Navigasi Dinamis Berbasis Role

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUser = $_SESSION['data'] ?? null;
$userRole    = $currentUser['role'] ?? '';
$userName    = $currentUser['nama_lengkap'] ?? 'Tamu';

$activeMenu  = $activeMenu ?? '';
$breadcrumbs = $breadcrumbs ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Aplikasi Parkir UKK') ?></title>
<link rel="stylesheet" id="fi-stylesheet" href="view/template/style.css">
</head>
<body>
<div class="fi-shell">

    <div class="fi-overlay" id="fiOverlay" aria-hidden="true"></div>

    <aside class="fi-sidebar" id="fiSidebar">
        <div class="fi-sidebar-brand">
            <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#d97706"/>
                <path d="M10 24V8h6.5a4.5 4.5 0 0 1 0 9H10" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Parkir UKK</span>
        </div>
        
        <div style="padding: 12px 18px 6px; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: .05em; color: var(--fi-gray-400);">
            Menu <?= strtoupper($userRole ?: 'Utama') ?>
        </div>

        <nav class="fi-nav">
            <?php if ($userRole === 'admin'): ?>
                <!-- Menu Khusus Admin -->
                <a href="index.php?c=admin&a=dashboard" class="fi-nav-item <?= $activeMenu === 'dashboard' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>

                <a href="index.php?c=admin&a=area" class="fi-nav-item <?= $activeMenu === 'parking-areas' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                    Area Parkir
                </a>

                <a href="index.php?c=admin&a=tarif" class="fi-nav-item <?= $activeMenu === 'tarif' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Tarif Parkir
                </a>

                <a href="index.php?c=admin&a=kendaraan" class="fi-nav-item <?= $activeMenu === 'kendaraan' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17l1 3h12l1-3"/></svg>
                    Data Kendaraan
                </a>

                <a href="index.php?c=admin&a=pengguna" class="fi-nav-item <?= $activeMenu === 'users' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/></svg>
                    Data Pengguna
                </a>

                <a href="index.php?c=admin&a=log" class="fi-nav-item <?= $activeMenu === 'log' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    Log Aktivitas
                </a>

            <?php elseif ($userRole === 'petugas'): ?>
                <!-- Menu Khusus Petugas -->
                <a href="index.php?c=petugas&a=dashboard" class="fi-nav-item <?= $activeMenu === 'dashboard' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard Petugas
                </a>

                <a href="index.php?c=petugas&a=transaksi_masuk" class="fi-nav-item <?= $activeMenu === 'transaksi-masuk' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    Kendaraan Masuk
                </a>

                <a href="index.php?c=petugas&a=transaksi_keluar" class="fi-nav-item <?= $activeMenu === 'transaksi-keluar' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    Kendaraan Keluar
                </a>

                <a href="index.php?c=petugas&a=riwayat" class="fi-nav-item <?= $activeMenu === 'riwayat' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                    Riwayat Transaksi
                </a>

            <?php elseif ($userRole === 'owner'): ?>
                <!-- Menu Khusus Owner -->
                <a href="index.php?c=owner&a=dashboard" class="fi-nav-item <?= $activeMenu === 'dashboard' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard Owner
                </a>

                <a href="index.php?c=owner&a=laporan" class="fi-nav-item <?= $activeMenu === 'laporan' ? 'is-active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                    Rekap Transaksi
                </a>
            <?php endif; ?>

            <hr style="border: 0; border-top: 1px solid var(--fi-gray-100); margin: 12px 0;">

            <a href="index.php?c=auth&a=logout" class="fi-nav-item" style="color: #ef4444;" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                Keluar (Logout)
            </a>
        </nav>
    </aside>

    <div class="fi-main">
        <header class="fi-topbar">
            <div class="fi-topbar-left">
            <button type="button" class="fi-menu-toggle" id="fiMenuToggle" aria-label="Buka menu navigasi" aria-controls="fiSidebar" aria-expanded="false">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="fi-breadcrumbs">
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                    <?php if ($i > 0): ?><span>&gt;</span><?php endif; ?>
                    <?php if (!empty($crumb['url'])): ?>
                        <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                    <?php else: ?>
                        <span><?= htmlspecialchars($crumb['label']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            </div>

            <div class="fi-topbar-user" style="display: flex; align-items: center; gap: 14px;">
                <div style="text-align: right;">
                    <div class="fi-topbar-name" style="font-weight: 600; font-size: 13px; color: var(--fi-gray-900);"><?= htmlspecialchars($userName) ?></div>
                    <span class="fi-badge" style="text-transform: capitalize; background: #fef3c7; color: #b45309; font-size: 11px;">
                        <?= htmlspecialchars($userRole ?: 'Guest') ?>
                    </span>
                </div>
                <a href="index.php?c=auth&a=logout" title="Keluar" onclick="return confirm('Keluar dari sistem?')" style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; background:var(--fi-gray-100); color:var(--fi-gray-600);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                </a>
            </div>
        </header>

        <main class="fi-page">
