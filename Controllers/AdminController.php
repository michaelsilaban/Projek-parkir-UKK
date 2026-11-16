<?php
// Controllers/AdminController.php
// Semua fitur Administrator: Dashboard, Area Parkir, Kendaraan, Tarif, Pengguna, Log Aktivitas

require_once __DIR__ . '/BaseController.php';

class AdminController extends BaseController {

    protected $namaController = 'admin';

    public function __construct() {
        $this->cekRole(['admin']);
    }

    // ==================================================================
    // FUNGSI BANTU (dipakai semua menu di bawah)
    // ==================================================================

    // Hasil simpan berhasil -> catat log lalu kembali ke daftar. Gagal -> lempar error (ditangkap try/catch).
    private function simpanLalu($berhasil, $pesanLog, $halaman, $kodePesan, $pesanGagal) {
        if (!$berhasil) {
            throw new Exception($pesanGagal);
        }
        $this->model('m_log')->catat($this->userId(), $pesanLog);
        $this->redirect('admin', $halaman, ['msg' => $kodePesan]);
    }

    // Hapus data berdasarkan ?id=... (area, kendaraan, tarif punya alur yang sama)
    private function hapusData($namaModel, $halaman, $labelId, $logTeks, $pesanGagal) {
        try {
            $id = $this->getInt('id');
            if ($id <= 0) {
                throw new Exception("ID $labelId tidak valid!");
            }
            $berhasil = $this->model($namaModel)->delete($id);
            $this->simpanLalu($berhasil, "$logTeks $id", $halaman, 'sukses_hapus', $pesanGagal);
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    // Ambil data untuk form edit/detail. Jika tidak ada, tampilkan alert lalu kembali ke daftar.
    private function ambilAtauTolak($namaModel, $id, $pesan, $halaman) {
        $data = $this->model($namaModel)->getById($id);
        if (!$data) {
            $this->alertTidakDitemukan($pesan, 'admin', $halaman);
        }
        return $data;
    }

    // ==================================================================
    // DASHBOARD
    // ==================================================================
    public function dashboard() {
        $areas      = $this->model('m_area')->getAll('', 10);
        $recentLogs = $this->model('m_log')->getRecent(6);

        $this->halaman('dashboard_admin', 'Dashboard Admin', 'dashboard',
            [['label' => 'Dashboard']],
            compact('areas', 'recentLogs'));
    }

    // ==================================================================
    // AREA PARKIR
    // ==================================================================
    public function area() {
        $mArea  = $this->model('m_area');
        $search = $this->get('search');

        $pg   = $this->paginasi($mArea->countAll($search));
        $rows = $mArea->getAll($search, $pg['perPage'], $pg['offset']);

        $this->halaman('admin/data-parking-admin', 'Area Parkir', 'parking-areas',
            $this->crumbs('Area Parkir', 'area', 'Daftar'),
            array_merge($pg, compact('search', 'rows')));
    }

    public function tambah_area() {
        if (!$this->isPost()) {
            $this->halaman('admin/create', 'Tambah Area Parkir', 'parking-areas',
                $this->crumbs('Area Parkir', 'area', 'Tambah Area Baru'));
            return;
        }

        try {
            $nama_area = $this->post('nama_area');
            $kapasitas = $this->postInt('kapasitas');
            $terisi    = $this->postInt('terisi');

            if (empty($nama_area) || $kapasitas <= 0) {
                throw new Exception("Nama area dan kapasitas wajib diisi dengan benar!");
            }

            $this->simpanLalu(
                $this->model('m_area')->tambah($nama_area, $kapasitas, $terisi),
                "Menambahkan area parkir baru: $nama_area (Kapasitas: $kapasitas)",
                'area', 'sukses_tambah', "Gagal menambahkan area parkir ke database."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function ubah_area() {
        if (!$this->isPost()) {
            $id_area = $this->getInt('id');
            $area    = $this->ambilAtauTolak('m_area', $id_area, 'Data area parkir tidak ditemukan!', 'area');

            $this->halaman('admin/edit', 'Edit Area Parkir', 'parking-areas',
                $this->crumbs('Area Parkir', 'area', 'Edit Area #' . $id_area),
                compact('area'));
            return;
        }

        try {
            $id_area   = $this->postInt('id_area');
            $nama_area = $this->post('nama_area');
            $kapasitas = $this->postInt('kapasitas');
            $terisi    = $this->postInt('terisi');

            if ($id_area <= 0 || empty($nama_area) || $kapasitas <= 0) {
                throw new Exception("Data area parkir tidak valid!");
            }

            $this->simpanLalu(
                $this->model('m_area')->update($id_area, $nama_area, $kapasitas, $terisi),
                "Mengubah area parkir ID $id_area: $nama_area",
                'area', 'sukses_edit', "Gagal memperbarui data area parkir."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function detail_area() {
        $id_area = $this->getInt('id');
        $area    = $this->ambilAtauTolak('m_area', $id_area, 'Data area parkir tidak ditemukan!', 'area');

        $sisaSlot = max(0, $area['kapasitas'] - $area['terisi']);
        $persen   = $area['kapasitas'] > 0 ? round(($area['terisi'] / $area['kapasitas']) * 100) : 0;

        $this->halaman('admin/view', 'Detail Area Parkir', 'parking-areas',
            $this->crumbs('Area Parkir', 'area', 'Detail #' . $id_area),
            compact('area', 'sisaSlot', 'persen'));
    }

    public function hapus_area() {
        $this->hapusData('m_area', 'area', 'Area', 'Menghapus area parkir ID:',
            "Data area parkir sedang digunakan.");
    }

    // ==================================================================
    // KENDARAAN (member)
    // ==================================================================
    public function kendaraan() {
        $mKendaraan = $this->model('m_kendaraan');
        $search     = $this->get('search');
        $tipe       = 'member'; // Data Kendaraan = daftar member

        $pg   = $this->paginasi($mKendaraan->countAll($search, $tipe));
        $rows = $mKendaraan->getAll($search, $pg['perPage'], $pg['offset'], $tipe);

        $this->halaman('admin/kendaraan/index', 'Data Kendaraan', 'kendaraan',
            $this->crumbs('Kendaraan', 'kendaraan', 'Daftar'),
            array_merge($pg, compact('search', 'rows')));
    }

    public function tambah_kendaraan() {
        if (!$this->isPost()) {
            $this->halaman('admin/kendaraan/create', 'Tambah Kendaraan', 'kendaraan',
                $this->crumbs('Kendaraan', 'kendaraan', 'Tambah Baru'));
            return;
        }

        try {
            $mKendaraan = $this->model('m_kendaraan');

            $plat    = $this->post('plat_nomor');
            $jenis   = $this->post('jenis_kendaraan', 'mobil');
            $warna   = $this->post('warna');
            $pemilik = $this->post('pemilik');

            if (empty($plat) || empty($jenis)) {
                throw new Exception("Nomor plat dan jenis kendaraan wajib diisi!");
            }

            // Semua kendaraan di Data Kendaraan otomatis berstatus member.
            // Jika plat sudah pernah masuk sebagai kendaraan umum (input manual petugas), naikkan jadi member.
            $existing = $mKendaraan->getByPlat($plat);
            if ($existing && !empty($existing['is_member'])) {
                throw new Exception("Kendaraan dengan plat nomor " . strtoupper($plat) . " sudah terdaftar!");
            }

            $adminId = $this->userId();
            if ($existing) {
                $berhasil = $mKendaraan->update($existing['id_kendaraan'], $plat, $jenis, $warna, $pemilik, 1, $adminId);
            } else {
                $berhasil = $mKendaraan->tambah($plat, $jenis, $warna, $pemilik, $adminId);
            }

            $this->simpanLalu($berhasil,
                "Menambahkan kendaraan member: " . strtoupper($plat) . " ($pemilik)",
                'kendaraan', 'sukses_tambah', "Gagal menambahkan kendaraan ke database."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function ubah_kendaraan() {
        if (!$this->isPost()) {
            $id_kendaraan = $this->getInt('id');
            $kendaraan    = $this->ambilAtauTolak('m_kendaraan', $id_kendaraan, 'Data kendaraan tidak ditemukan!', 'kendaraan');

            $this->halaman('admin/kendaraan/edit', 'Edit Kendaraan', 'kendaraan',
                $this->crumbs('Kendaraan', 'kendaraan', 'Edit #' . $id_kendaraan),
                compact('kendaraan'));
            return;
        }

        try {
            $id_kendaraan = $this->postInt('id_kendaraan');
            $plat         = $this->post('plat_nomor');
            $jenis        = $this->post('jenis_kendaraan', 'mobil');
            $warna        = $this->post('warna');
            $pemilik      = $this->post('pemilik');

            if ($id_kendaraan <= 0 || empty($plat)) {
                throw new Exception("Data kendaraan tidak valid!");
            }

            $this->simpanLalu(
                $this->model('m_kendaraan')->update($id_kendaraan, $plat, $jenis, $warna, $pemilik),
                "Mengubah data kendaraan ID $id_kendaraan: " . strtoupper($plat),
                'kendaraan', 'sukses_edit', "Gagal memperbarui kendaraan atau plat nomor sudah digunakan kendaraan lain."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function hapus_kendaraan() {
        $this->hapusData('m_kendaraan', 'kendaraan', 'Kendaraan', 'Menghapus data kendaraan ID:',
            "Data kendaraan sedang digunakan.");
    }

    // ==================================================================
    // TARIF PARKIR
    // ==================================================================
    public function tarif() {
        $rows = $this->model('m_tarif')->getAll();

        $this->halaman('admin/tarif/index', 'Tarif Parkir', 'tarif',
            $this->crumbs('Tarif Parkir', 'tarif', 'Daftar'),
            compact('rows'));
    }

    public function tambah_tarif() {
        if (!$this->isPost()) {
            $this->halaman('admin/tarif/create', 'Tambah Tarif Parkir', 'tarif',
                $this->crumbs('Tarif Parkir', 'tarif', 'Tambah Baru'));
            return;
        }

        try {
            $jenis = $this->post('jenis_kendaraan');
            $tarif = (float)($_POST['tarif_per_jam'] ?? 0);

            if (empty($jenis) || $tarif < 0) {
                throw new Exception("Data tarif tidak lengkap atau tidak valid!");
            }

            $this->simpanLalu(
                $this->model('m_tarif')->tambah($jenis, $tarif),
                "Menambahkan tarif baru: $jenis (Rp " . $this->rupiah($tarif) . "/jam)",
                'tarif', 'sukses_tambah', "Jenis kendaraan ini sudah memiliki tarif atau terjadi kesalahan database."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function ubah_tarif() {
        if (!$this->isPost()) {
            $id_tarif = $this->getInt('id');
            $tarif    = $this->ambilAtauTolak('m_tarif', $id_tarif, 'Data tarif tidak ditemukan!', 'tarif');

            $this->halaman('admin/tarif/edit', 'Edit Tarif Parkir', 'tarif',
                $this->crumbs('Tarif Parkir', 'tarif', 'Edit #' . $id_tarif),
                compact('tarif'));
            return;
        }

        try {
            $id_tarif = $this->postInt('id_tarif');
            $jenis    = $this->post('jenis_kendaraan');
            $tarif    = (float)($_POST['tarif_per_jam'] ?? 0);

            if ($id_tarif <= 0 || empty($jenis) || $tarif < 0) {
                throw new Exception("Data tarif tidak valid!");
            }

            $this->simpanLalu(
                $this->model('m_tarif')->update($id_tarif, $jenis, $tarif),
                "Mengubah tarif ID $id_tarif: $jenis (Rp " . $this->rupiah($tarif) . "/jam)",
                'tarif', 'sukses_edit', "Gagal mengubah tarif parkir."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function hapus_tarif() {
        $this->hapusData('m_tarif', 'tarif', 'Tarif', 'Menghapus tarif parkir ID:',
            "Data tarif sedang digunakan.");
    }

    // ==================================================================
    // PENGGUNA (USER)
    // ==================================================================
    public function pengguna() {
        $mUser      = $this->model('m_user');
        $search     = $this->get('search');
        $roleFilter = $this->get('role');

        $pg   = $this->paginasi($mUser->countAll($search, $roleFilter));
        $rows = $mUser->getAll($search, $roleFilter, $pg['perPage'], $pg['offset']);

        $this->halaman('admin/user/index', 'Data Pengguna', 'users',
            $this->crumbs('Pengguna', 'pengguna', 'Daftar'),
            array_merge($pg, compact('search', 'roleFilter', 'rows')));
    }

    public function tambah_pengguna() {
        if (!$this->isPost()) {
            $this->halaman('admin/user/create', 'Tambah Pengguna', 'users',
                $this->crumbs('Pengguna', 'pengguna', 'Tambah Baru'));
            return;
        }

        try {
            $nama_lengkap = $this->post('nama_lengkap');
            $username     = $this->post('username');
            $password     = $this->post('password');
            $role         = $this->post('role', 'petugas');
            $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

            if (empty($nama_lengkap) || empty($username) || empty($password)) {
                throw new Exception("Semua bidang formulir wajib diisi!");
            }

            $mUser = $this->model('m_user');
            if ($mUser->getByUsername($username)) {
                throw new Exception("Username sudah digunakan, silakan pilih username lain!");
            }

            $this->simpanLalu(
                $mUser->tambah($nama_lengkap, $username, $password, $role, $status_aktif),
                "Menambahkan pengguna baru: $username ($role)",
                'pengguna', 'sukses_tambah', "Gagal menyimpan data pengguna ke database."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function ubah_pengguna() {
        if (!$this->isPost()) {
            $id_user = $this->getInt('id');
            $user    = $this->ambilAtauTolak('m_user', $id_user, 'Data pengguna tidak ditemukan!', 'pengguna');

            $this->halaman('admin/user/edit', 'Edit Pengguna', 'users',
                $this->crumbs('Pengguna', 'pengguna', 'Edit #' . $id_user),
                compact('user'));
            return;
        }

        try {
            $id_user      = $this->postInt('id_user');
            $nama_lengkap = $this->post('nama_lengkap');
            $username     = $this->post('username');
            $role         = $this->post('role', 'petugas');
            $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;
            $password     = $this->post('password') ?: null; // kosong = password tidak diganti

            if ($id_user <= 0 || empty($nama_lengkap) || empty($username)) {
                throw new Exception("Data pengguna tidak valid!");
            }

            $this->simpanLalu(
                $this->model('m_user')->update($id_user, $nama_lengkap, $username, $role, $status_aktif, $password),
                "Mengubah data pengguna: $username (ID: $id_user)",
                'pengguna', 'sukses_edit', "Gagal memperbarui data pengguna atau username sudah dipakai user lain."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function hapus_pengguna() {
        try {
            $id_user = $this->getInt('id');
            if ($id_user <= 0) {
                throw new Exception("ID Pengguna tidak valid!");
            }

            // Admin tidak boleh menghapus akunnya sendiri
            if ($id_user === (int)$this->userId()) {
                throw new Exception("Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!");
            }

            $mUser      = $this->model('m_user');
            $targetUser = $mUser->getById($id_user);

            $this->simpanLalu(
                $mUser->delete($id_user),
                "Menghapus akun pengguna: " . ($targetUser['username'] ?? $id_user),
                'pengguna', 'sukses_hapus', "Data pengguna sedang digunakan."
            );
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function toggle_pengguna() {
        try {
            $id_user = $this->getInt('id');
            if ($id_user <= 0) {
                throw new Exception("ID Pengguna tidak valid!");
            }

            if ($id_user === (int)$this->userId()) {
                throw new Exception("Anda tidak dapat menonaktifkan akun Anda sendiri!");
            }

            $this->model('m_user')->toggleStatus($id_user);
            $this->model('m_log')->catat($this->userId(), "Mengubah status aktifasi pengguna ID: $id_user");
            $this->redirect('admin', 'pengguna', ['msg' => 'sukses_status']);
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    // ==================================================================
    // LOG AKTIVITAS
    // ==================================================================
    public function log() {
        $mLog = $this->model('m_log');

        $pg   = $this->paginasi($mLog->countAll(), [15, 30, 50]);
        $rows = $mLog->getAll($pg['perPage'], $pg['offset']);

        $this->halaman('admin/log/index', 'Log Aktivitas', 'log',
            $this->crumbs('Log Aktivitas', 'log', 'Daftar'),
            array_merge($pg, compact('rows')));
    }
}
