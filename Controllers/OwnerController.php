<?php
// Controllers/OwnerController.php
// Fitur Owner / Pimpinan: Dashboard Eksekutif, Rekap Laporan, Cetak Laporan

require_once __DIR__ . '/BaseController.php';

class OwnerController extends BaseController {

    protected $namaController = 'owner';

    public function __construct() {
        $this->cekRole(['owner', 'admin']);
    }

    // Ambil filter dari URL (bawaan: awal bulan ini s/d hari ini) lalu hitung rekap laporannya.
    // Dipakai bersama oleh halaman laporan dan halaman cetak.
    private function dataLaporan() {
        $tgl_mulai   = $_GET['tgl_mulai'] ?? date('Y-m-01');
        $tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');
        $jenis       = $_GET['jenis_kendaraan'] ?? '';
        $status      = $_GET['status'] ?? '';

        $rekap = $this->model('m_transaksi')->getRekapLaporan($tgl_mulai, $tgl_selesai, $jenis, $status);
        $rows  = $rekap['transaksi'];

        return compact('tgl_mulai', 'tgl_selesai', 'jenis', 'status', 'rekap', 'rows');
    }

    // ==================================================================
    // DASHBOARD
    // ==================================================================
    public function dashboard() {
        $stats     = $this->model('m_transaksi')->getStatistikDashboard();
        $recentTrx = $this->model('m_transaksi')->getAll('', '', 8, 0); // 8 transaksi terakhir

        $this->halaman('dashboard', 'Dashboard Owner', 'dashboard',
            [['label' => 'Dashboard Owner']],
            compact('stats', 'recentTrx'));
    }

    // ==================================================================
    // LAPORAN REKAP TRANSAKSI
    // ==================================================================
    public function laporan() {
        $data           = $this->dataLaporan();
        $data['tarifs'] = $this->model('m_tarif')->getAll();

        $this->halaman('owner/laporan', 'Rekap Transaksi', 'laporan',
            $this->crumbs('Laporan', 'laporan', 'Rekap Transaksi'),
            $data);
    }

    // ==================================================================
    // CETAK LAPORAN (siap print / PDF)
    // ==================================================================
    public function cetak_laporan() {
        $this->render('owner/cetak_laporan', $this->dataLaporan());
    }
}
