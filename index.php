<?php
// index.php - pintu masuk aplikasi (router MVC)
// Format URL: index.php?c=<controller>&a=<aksi>

// Aktifkan pelaporan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

// Daftar halaman yang boleh diakses:
// 'controller' => [nama class, aksi bawaan, daftar aksi yang diizinkan]
$routes = [
    'home' => ['HomeController', 'index', ['index']],

    'admin' => ['AdminController', 'dashboard', [
        'dashboard',
        'area', 'tambah_area', 'ubah_area', 'detail_area', 'hapus_area',
        'kendaraan', 'tambah_kendaraan', 'ubah_kendaraan', 'hapus_kendaraan',
        'tarif', 'tambah_tarif', 'ubah_tarif', 'hapus_tarif',
        'pengguna', 'tambah_pengguna', 'ubah_pengguna', 'hapus_pengguna', 'toggle_pengguna',
        'log'
    ]],

    'owner' => ['OwnerController', 'dashboard', ['dashboard', 'laporan', 'cetak_laporan']],

    'petugas' => ['PetugasController', 'dashboard', [
        'dashboard', 'transaksi_masuk', 'transaksi_keluar', 'riwayat', 'struk_masuk', 'struk_keluar'
    ]],

    'auth' => ['AuthController', 'login', ['login', 'logout']],
];

$controller = strtolower(trim($_GET['c'] ?? ''));
$action     = strtolower(trim($_GET['a'] ?? ''));

// Tanpa parameter -> tampilkan landing page
if ($controller === '') {
    $controller = 'home';
}

// Controller tidak dikenal -> kembali ke landing page
if (!isset($routes[$controller])) {
    header('Location: index.php?c=home&a=index');
    exit;
}

list($namaClass, $aksiBawaan, $aksiDiizinkan) = $routes[$controller];

// Aksi tidak dikenal -> pakai aksi bawaan controller tersebut
if (!in_array($action, $aksiDiizinkan, true)) {
    $action = $aksiBawaan;
}

require_once __DIR__ . '/Controllers/' . $namaClass . '.php';
$objek = new $namaClass();
$objek->$action();
