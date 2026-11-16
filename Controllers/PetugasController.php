<?php
// Controllers/PetugasController.php
// Fitur Petugas: Dashboard, Check-In (masuk), Check-Out (keluar), Riwayat, Struk

require_once __DIR__ . '/BaseController.php';

class PetugasController extends BaseController {

    protected $namaController = 'petugas';

    public function __construct() {
        // Petugas dan Admin diizinkan melakukan transaksi parkir
        $this->cekRole(['petugas', 'admin']);
    }

    // Dialog sukses transaksi dengan tombol cetak. $isi = ['text' => ...] atau ['html' => ...]
    private function swalTransaksiSukses($judul, array $isi, $teksCetak, $teksTutup, $warna, $urlCetak) {
        $this->swal(array_merge($isi, [
            'title'              => $judul,
            'icon'               => 'success',
            'showCancelButton'   => true,
            'confirmButtonText'  => $teksCetak,
            'cancelButtonText'   => $teksTutup,
            'confirmButtonColor' => $warna,
            'cancelButtonColor'  => '#6b7280'
        ]), $urlCetak, self::url('petugas', 'dashboard'));
    }

    // Ambil transaksi dari ?id=... untuk dicetak. Jika tidak ada, kembali ke dashboard.
    private function ambilTransaksiStruk($pesanTidakAda) {
        $transaksi = $this->model('m_transaksi')->getById($this->getInt('id'));
        if (!$transaksi) {
            $this->alertTidakDitemukan($pesanTidakAda, 'petugas', 'dashboard');
        }
        return $transaksi;
    }

    // ==================================================================
    // DASHBOARD
    // ==================================================================
    public function dashboard() {
        $kendaraanAktif = $this->model('m_transaksi')->getKendaraanAktif();
        $areas          = $this->model('m_area')->getAll();

        $this->halaman('dashboard_petugas', 'Dashboard Petugas', 'dashboard',
            [['label' => 'Dashboard Petugas']],
            compact('kendaraanAktif', 'areas'));
    }

    // ==================================================================
    // TRANSAKSI MASUK (Check-In)  GET = form, POST = proses
    // ==================================================================
    public function transaksi_masuk() {
        $mTransaksi = $this->model('m_transaksi'); // juga memuat class m_transaksi (dipakai konstanta diskon)

        if (!$this->isPost()) {
            $tarifs  = $this->model('m_tarif')->getAll();
            $areas   = $this->model('m_area')->getAll();
            $members = $this->model('m_kendaraan')->getMembers();
            $diskonMember = m_transaksi::DISKON_MEMBER_PERSEN;

            // Tarif per jenis kendaraan (untuk menampilkan estimasi tarif member)
            $tarifMap = array_map('floatval', array_column($tarifs, 'tarif_per_jam', 'jenis_kendaraan'));

            $this->halaman('petugas/transaksi_masuk', 'Kendaraan Masuk (Check-In)', 'transaksi-masuk',
                $this->crumbs('Transaksi', 'transaksi_masuk', 'Check-In Masuk'),
                compact('areas', 'tarifs', 'members', 'diskonMember', 'tarifMap'));
            return;
        }

        try {
            $userId     = $this->userId(2);
            $id_area    = $this->postInt('id_area');
            $tipe       = $_POST['tipe_masuk'] ?? 'manual';   // 'manual' atau 'member'
            $id_member  = $this->postInt('id_member');

            if ($id_area <= 0) {
                throw new Exception("Area parkir wajib dipilih!");
            }

            if ($tipe === 'member') {
                if ($id_member <= 0) {
                    throw new Exception("Silakan pilih member terlebih dahulu!");
                }
                $res = $mTransaksi->masuk('', '', '', '', $id_area, $userId, $id_member);
            } else {
                $plat = $this->post('plat_nomor');
                if (empty($plat)) {
                    throw new Exception("Plat nomor dan area parkir wajib diisi!");
                }
                $res = $mTransaksi->masuk(
                    $plat,
                    $this->post('jenis_kendaraan', 'motor'),
                    $this->post('warna', '-'),
                    $this->post('pemilik', 'Pengunjung Umum'),
                    $id_area,
                    $userId
                );
            }

            if (!$res['status']) {
                throw new Exception($res['message']);
            }

            $infoMember = !empty($res['is_member']) ? ' (MEMBER - diskon ' . (int)$res['diskon'] . '%)' : '';
            $teks       = 'Kendaraan ' . $res['plat'] . $infoMember . ' berhasil dicatat masuk area parkir.';

            $this->swalTransaksiSukses('Check-In Berhasil!', ['text' => $teks],
                'Cetak Karcis Masuk', 'Selesai', '#d97706',
                self::url('petugas', 'struk_masuk', ['id' => $res['id_parkir']]));
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    // ==================================================================
    // TRANSAKSI KELUAR (Check-Out & Bayar)  GET = form, POST = proses
    // ==================================================================
    public function transaksi_keluar() {
        $mTransaksi = $this->model('m_transaksi');

        if (!$this->isPost()) {
            $selectedId         = $this->getInt('id');
            $selectedTransaksi  = null;
            $durasiEstimasi     = 0;
            $biayaEstimasi      = 0;
            $diskonPersen       = 0;
            $biayaSebelumDiskon = 0;

            // Hitung estimasi biaya jika kendaraan yang dipilih masih parkir
            if ($selectedId > 0) {
                $selectedTransaksi = $mTransaksi->getById($selectedId);
                if ($selectedTransaksi && $selectedTransaksi['status'] === 'masuk') {
                    $tarifPerJam        = (float)$selectedTransaksi['tarif_per_jam'];
                    $durasiEstimasi     = m_transaksi::hitungDurasiJam($selectedTransaksi['waktu_masuk']);
                    $diskonPersen       = (int)($selectedTransaksi['diskon_persen'] ?? 0);
                    $biayaSebelumDiskon = $durasiEstimasi * $tarifPerJam;
                    $biayaEstimasi      = m_transaksi::hitungBiaya($durasiEstimasi, $tarifPerJam, $diskonPersen);
                }
            }

            $kendaraanAktif = $mTransaksi->getKendaraanAktif();

            $this->halaman('petugas/transaksi_keluar', 'Kendaraan Keluar (Check-Out)', 'transaksi-keluar',
                $this->crumbs('Transaksi', 'transaksi_keluar', 'Check-Out & Bayar'),
                compact('selectedId', 'selectedTransaksi', 'durasiEstimasi', 'biayaEstimasi',
                        'diskonPersen', 'biayaSebelumDiskon', 'kendaraanAktif'));
            return;
        }

        try {
            $id_parkir = $this->postInt('id_parkir');
            if ($id_parkir <= 0) {
                throw new Exception("ID Transaksi parkir tidak valid!");
            }

            $res = $mTransaksi->keluar($id_parkir, $this->userId(2));
            if (!$res['status']) {
                throw new Exception($res['message']);
            }

            $diskonInfo = $res['diskon'] > 0 ? '<p>Diskon Member: <strong>' . (int)$res['diskon'] . '%</strong></p>' : '';
            $html = '<p>Durasi: <strong>' . (int)$res['durasi_jam'] . ' Jam</strong></p>' . $diskonInfo
                  . '<p style="font-size:18px; color:#16a34a;">Total Bayar: <strong>Rp ' . $this->rupiah($res['biaya_total']) . '</strong></p>';

            $this->swalTransaksiSukses('Pembayaran Selesai!', ['html' => $html],
                'Cetak Struk Pembayaran', 'Tutup', '#16a34a',
                self::url('petugas', 'struk_keluar', ['id' => $id_parkir]));
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    // ==================================================================
    // RIWAYAT TRANSAKSI
    // ==================================================================
    public function riwayat() {
        $mTransaksi   = $this->model('m_transaksi');
        $search       = $this->get('search');
        $statusFilter = $this->get('status');

        $pg   = $this->paginasi($mTransaksi->countAll($search, $statusFilter));
        $rows = $mTransaksi->getAll($search, $statusFilter, $pg['perPage'], $pg['offset']);

        $this->halaman('petugas/riwayat', 'Riwayat Transaksi', 'riwayat',
            $this->crumbs('Transaksi', 'riwayat', 'Riwayat & Struk'),
            array_merge($pg, compact('search', 'statusFilter', 'rows')));
    }

    // ==================================================================
    // STRUK (cetak karcis masuk & struk pembayaran)
    // ==================================================================
    public function struk_masuk() {
        $transaksi  = $this->ambilTransaksiStruk('Data tiket parkir tidak ditemukan!');
        $nomorTiket = 'TIKET-' . str_pad($transaksi['id_parkir'], 5, '0', STR_PAD_LEFT);

        $this->render('petugas/struk_masuk', compact('transaksi', 'nomorTiket'));
    }

    public function struk_keluar() {
        $transaksi = $this->ambilTransaksiStruk('Data transaksi parkir tidak ditemukan!');
        $nomorTrx  = 'TRX-' . str_pad($transaksi['id_parkir'], 5, '0', STR_PAD_LEFT);

        $this->render('petugas/struk_keluar', compact('transaksi', 'nomorTrx'));
    }
}
