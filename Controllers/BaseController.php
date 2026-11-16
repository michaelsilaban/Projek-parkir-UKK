<?php
// Controllers/BaseController.php
// Class induk semua Controller: sesi, cek role, input form, redirect, tampilan, dialog SweetAlert, paginasi.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

abstract class BaseController {

    // Nama controller di URL (diisi oleh controller turunan, dipakai untuk breadcrumb)
    protected $namaController = '';

    private $models = [];

    // ------------------------------------------------------------------
    // Model (dimuat sekali saja saat dibutuhkan)
    // ------------------------------------------------------------------
    protected function model($nama) {
        if (!isset($this->models[$nama])) {
            require_once __DIR__ . '/../model/' . $nama . '.php';
            $this->models[$nama] = new $nama();
        }
        return $this->models[$nama];
    }

    // ------------------------------------------------------------------
    // Autentikasi & otorisasi
    // ------------------------------------------------------------------
    protected function cekLogin() {
        if (empty($_SESSION['data']) || empty($_SESSION['data']['id_user'])) {
            $this->redirect('auth', 'login');
        }
    }

    protected function cekRole(array $rolesDiizinkan) {
        $this->cekLogin();
        $userRole = $_SESSION['data']['role'] ?? '';
        if (!in_array($userRole, $rolesDiizinkan)) {
            echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin untuk halaman ini.'); window.history.back();</script>";
            exit();
        }
    }

    protected function userId($default = 1) {
        return $_SESSION['data']['id_user'] ?? $default;
    }

    // ------------------------------------------------------------------
    // Input dari form / URL
    // ------------------------------------------------------------------
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    // Teks dari form (POST) tanpa spasi di tepi
    protected function post($nama, $default = '') {
        return trim($_POST[$nama] ?? $default);
    }

    // Angka bulat dari form (POST)
    protected function postInt($nama) {
        return (int)($_POST[$nama] ?? 0);
    }

    // Teks dari URL (GET) tanpa spasi di tepi
    protected function get($nama, $default = '') {
        return trim($_GET[$nama] ?? $default);
    }

    // Angka bulat dari URL (GET), misalnya ?id=5
    protected function getInt($nama) {
        return (int)($_GET[$nama] ?? 0);
    }

    // Format angka rupiah: 15000 -> 15.000
    protected function rupiah($angka) {
        return number_format($angka, 0, ',', '.');
    }

    // ------------------------------------------------------------------
    // URL, redirect, tampilan
    // ------------------------------------------------------------------
    public static function url($c, $a = '', $params = []) {
        $url = 'index.php?c=' . $c;
        if ($a !== '') {
            $url .= '&a=' . $a;
        }
        foreach ($params as $k => $v) {
            $url .= '&' . $k . '=' . urlencode($v);
        }
        return $url;
    }

    protected function redirect($c, $a = '', $params = []) {
        header('Location: ' . self::url($c, $a, $params));
        exit();
    }

    // Menampilkan file view (view/<nama>.php) dengan data dari controller
    protected function render($view, $data = []) {
        extract($data);
        require __DIR__ . '/../view/' . $view . '.php';
    }

    // Menampilkan halaman beserta judul, menu aktif, dan breadcrumb
    protected function halaman($view, $pageTitle, $activeMenu, array $breadcrumbs, array $data = []) {
        $this->render($view, array_merge($data, compact('pageTitle', 'activeMenu', 'breadcrumbs')));
    }

    // Breadcrumb 3 tingkat: Dashboard > Menu > Halaman sekarang
    protected function crumbs($labelMenu, $aksiMenu, $labelAkhir) {
        return [
            ['label' => 'Dashboard', 'url' => self::url($this->namaController, 'dashboard')],
            ['label' => $labelMenu,  'url' => self::url($this->namaController, $aksiMenu)],
            ['label' => $labelAkhir]
        ];
    }

    // ------------------------------------------------------------------
    // Dialog SweetAlert
    // ------------------------------------------------------------------
    // $opsi        : pengaturan dialog (title, text/html, icon, warna tombol, dll)
    // $urlOk       : tujuan setelah tombol utama ditekan (kosong = kembali ke halaman sebelumnya)
    // $urlBatal    : tujuan jika tombol batal ditekan (kosong = sama dengan $urlOk)
    // $bersihkanUrl: true = hapus parameter URL agar form tidak terkirim ulang saat di-refresh
    protected function swal(array $opsi, $urlOk = null, $urlBatal = null, $bersihkanUrl = false) {
        $opsiJs  = json_encode($opsi);
        $keOk    = $urlOk === null ? 'window.history.back();' : 'window.location.href = ' . json_encode($urlOk) . ';';
        $keBatal = $urlBatal === null ? $keOk : 'window.location.href = ' . json_encode($urlBatal) . ';';
        $bersih  = $bersihkanUrl
            ? "if (window.history.replaceState) { window.history.replaceState(null, null, window.location.pathname); }"
            : '';

        echo "
        <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap' rel='stylesheet'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $bersih
                Swal.fire($opsiJs).then(function(hasil) {
                    if (hasil.isConfirmed) {
                        $keOk
                    } else {
                        $keBatal
                    }
                });
            });
        </script>";
        exit();
    }

    // Dialog error lalu kembali ke halaman sebelumnya
    protected function alertError($pesan, $judul = 'Perhatian') {
        $this->swal([
            'title'              => $judul,
            'text'               => $pesan,
            'icon'               => 'error',
            'confirmButtonColor' => '#e74c3c'
        ]);
    }

    // Alert "data tidak ditemukan" lalu kembali ke halaman daftar
    protected function alertTidakDitemukan($pesan, $c, $a) {
        echo "<script>alert(" . json_encode($pesan) . "); window.location.href=" . json_encode(self::url($c, $a)) . ";</script>";
        exit();
    }

    // ------------------------------------------------------------------
    // Paginasi (dipakai halaman daftar)
    // ------------------------------------------------------------------
    protected function paginasi($total, array $allowedPerPage = [10, 20, 50]) {
        $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $allowedPerPage, true)
            ? (int)$_GET['per_page'] : $allowedPerPage[0];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $totalPages  = max(1, (int)ceil($total / $perPage));
        $page        = min($page, $totalPages);
        $offset      = ($page - 1) * $perPage;
        $showingFrom = $total === 0 ? 0 : $offset + 1;
        $showingTo   = min($offset + $perPage, $total);

        return compact('allowedPerPage', 'perPage', 'page', 'total', 'totalPages', 'offset', 'showingFrom', 'showingTo');
    }
}
