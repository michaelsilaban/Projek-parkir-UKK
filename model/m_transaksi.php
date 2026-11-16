<?php
// model/m_transaksi.php
// Model transaksi parkir: masuk, keluar, riwayat, rekap laporan, dan statistik dashboard (tb_transaksi)

require_once __DIR__ . '/m_base.php';
require_once __DIR__ . '/m_kendaraan.php';
require_once __DIR__ . '/m_tarif.php';
require_once __DIR__ . '/m_area.php';
require_once __DIR__ . '/m_log.php';

class m_transaksi extends m_base {
    // Diskon tarif per jam untuk kendaraan MEMBER (dalam persen)
    const DISKON_MEMBER_PERSEN = 30;

    // ------------------------------------------------------------------
    // Perhitungan (bisa dipanggil tanpa membuat objek: m_transaksi::hitungBiaya(...))
    // ------------------------------------------------------------------

    // Biaya = durasi x tarif per jam setelah dipotong diskon (persen)
    public static function hitungBiaya($durasiJam, $tarifPerJam, $diskonPersen = 0) {
        $tarifSetelahDiskon = (float)$tarifPerJam * (1 - ((float)$diskonPersen / 100));
        return round($durasiJam * $tarifSetelahDiskon);
    }

    // Durasi parkir sampai sekarang: minimal 1 jam, pembulatan ke atas
    public static function hitungDurasiJam($waktuMasuk) {
        $selisihDetik = max(0, time() - strtotime($waktuMasuk));
        return max(1, (int)ceil($selisihDetik / 3600));
    }

    // Tentukan diskon (%) sebuah transaksi tanpa kolom khusus di database:
    // - transaksi selesai : dihitung mundur dari biaya_total vs (durasi x tarif)
    // - transaksi berjalan: member = DISKON_MEMBER_PERSEN, selain itu 0
    private function tambahDiskon(&$row) {
        $diskon = 0;
        if (($row['status'] ?? '') === 'keluar') {
            $kotor = (int)$row['durasi_jam'] * (float)$row['tarif_per_jam'];
            if ($kotor > 0) {
                $diskon = max(0, (int)round((1 - ((float)$row['biaya_total'] / $kotor)) * 100));
            }
        } elseif (!empty($row['is_member'])) {
            $diskon = self::DISKON_MEMBER_PERSEN;
        }
        $row['diskon_persen'] = $diskon;
    }

    // ------------------------------------------------------------------
    // Query bersama
    // ------------------------------------------------------------------

    // Bagian awal query transaksi lengkap (gabungan 5 tabel) yang dipakai semua daftar transaksi
    private function selectTransaksi() {
        return "SELECT t.*,
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik, (k.id_user IS NOT NULL) AS is_member,
                       tf.tarif_per_jam,
                       a.nama_area,
                       u.nama_lengkap AS nama_petugas
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_tarif tf ON t.id_tarif = tf.id_tarif
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                JOIN tb_user u ON t.id_user = u.id_user";
    }

    // Jalankan query daftar transaksi, lalu isi kolom diskon_persen di setiap baris
    private function ambilTransaksi($sql) {
        $data = $this->ambilSemua($sql);
        foreach ($data as &$row) {
            $this->tambahDiskon($row);
        }
        return $data;
    }

    // Kondisi pencarian (plat / pemilik / area) -> teks SQL, atau '' jika kosong
    private function kondisiCari($search) {
        if (empty($search)) {
            return '';
        }
        $cari = $this->esc("%" . $search . "%");
        return "(k.plat_nomor LIKE '$cari' OR k.pemilik LIKE '$cari' OR a.nama_area LIKE '$cari')";
    }

    // Kondisi riwayat (pencarian + status) -> klausa WHERE
    private function whereRiwayat($search, $status) {
        $kondisi = [];
        if (!empty($search)) {
            $kondisi[] = $this->kondisiCari($search);
        }
        if (!empty($status)) {
            $kondisi[] = "t.status = '" . $this->esc($status) . "'";
        }
        return $this->buatWhere($kondisi);
    }

    // ------------------------------------------------------------------
    // Check-in
    // ------------------------------------------------------------------
    // - Input manual : isi $plat_nomor, $jenis_kendaraan, $warna, $pemilik
    // - Member       : isi $id_member (id_kendaraan milik member), data kendaraan diambil dari database
    public function masuk($plat_nomor, $jenis_kendaraan, $warna, $pemilik, $id_area, $id_user, $id_member = null) {
        $mArea      = new m_area();
        $mTarif     = new m_tarif();
        $mKendaraan = new m_kendaraan();

        // 1. Area harus masih punya slot kosong
        if (!$mArea->cekKetersediaan($id_area)) {
            return ['status' => false, 'message' => 'Area parkir yang dipilih sudah penuh!'];
        }

        // 2. Tentukan data kendaraan (member dari database / manual dari input petugas)
        if (!empty($id_member)) {
            $kendaraan = $mKendaraan->getById((int)$id_member);
            if (!$kendaraan || empty($kendaraan['is_member'])) {
                return ['status' => false, 'message' => 'Data member tidak ditemukan atau kendaraan bukan member!'];
            }
            $plat_nomor = $kendaraan['plat_nomor'];
        } else {
            $kendaraan = $mKendaraan->getOrCreate($plat_nomor, $jenis_kendaraan, $warna, $pemilik);
            if (!$kendaraan) {
                return ['status' => false, 'message' => 'Gagal memproses data kendaraan!'];
            }
        }
        // Plat yang diketik manual tapi ternyata terdaftar sebagai member tetap mendapat diskon
        $jenis_kendaraan = $kendaraan['jenis_kendaraan'];
        $id_kendaraan    = (int)$kendaraan['id_kendaraan'];
        $isMember        = !empty($kendaraan['is_member']);
        $diskon          = $isMember ? self::DISKON_MEMBER_PERSEN : 0;

        // 3. Tarif sesuai jenis kendaraan (jika belum ada, pakai tarif pertama)
        $tarif = $mTarif->getByJenis($jenis_kendaraan);
        if (!$tarif) {
            $semuaTarif = $mTarif->getAll();
            if (empty($semuaTarif)) {
                return ['status' => false, 'message' => 'Master data tarif parkir belum diatur!'];
            }
            $tarif = $semuaTarif[0];
        }
        $id_tarif = (int)$tarif['id_tarif'];

        // 4. Kendaraan tidak boleh sedang parkir
        $sedangParkir = $this->ambilSatu("SELECT id_parkir FROM tb_transaksi
                                          WHERE id_kendaraan = '$id_kendaraan' AND status = 'masuk' LIMIT 1");
        if ($sedangParkir) {
            return ['status' => false, 'message' => 'Kendaraan dengan plat ini masih tercatat berada di dalam area parkir!'];
        }

        // 5. Simpan transaksi masuk (diskon member dihitung saat check-out)
        $idUser = (int)$id_user;
        $idArea = (int)$id_area;
        $waktu  = date('Y-m-d H:i:s');

        $berhasil = $this->jalankan("INSERT INTO tb_transaksi
                (id_kendaraan, waktu_masuk, waktu_keluar, id_tarif, durasi_jam, biaya_total, status, id_user, id_area)
                VALUES ('$id_kendaraan', '$waktu', NULL, '$id_tarif', 0, 0, 'masuk', '$idUser', '$idArea')");

        if (!$berhasil) {
            return ['status' => false, 'message' => 'Gagal menyimpan data transaksi masuk: ' . mysqli_error($this->conn)];
        }

        $id_parkir = mysqli_insert_id($this->conn);
        $plat      = strtoupper($plat_nomor);

        $mArea->updateTerisi($id_area, 1);

        $labelMember = $isMember ? " [MEMBER diskon {$diskon}%]" : "";
        (new m_log())->catat($id_user, "Check-in parkir masuk plat: " . $plat . $labelMember . " (ID: $id_parkir)");

        return [
            'status'    => true,
            'id_parkir' => $id_parkir,
            'plat'      => $plat,
            'is_member' => $isMember,
            'diskon'    => $diskon,
            'message'   => 'Kendaraan berhasil masuk area parkir.'
        ];
    }

    // ------------------------------------------------------------------
    // Check-out & pembayaran
    // ------------------------------------------------------------------
    public function keluar($id_parkir, $id_user) {
        // 1. Ambil data transaksi saat ini
        $transaksi = $this->getById($id_parkir);
        if (!$transaksi) {
            return ['status' => false, 'message' => 'Data transaksi tidak ditemukan!'];
        }
        if ($transaksi['status'] === 'keluar') {
            return ['status' => false, 'message' => 'Kendaraan ini sudah menyelesaikan pembayaran dan sudah keluar!'];
        }

        // 2. Hitung durasi dan total biaya (member mendapat diskon per jam)
        $durasiJam  = self::hitungDurasiJam($transaksi['waktu_masuk']);
        $diskon     = (int)($transaksi['diskon_persen'] ?? 0);
        $biayaTotal = self::hitungBiaya($durasiJam, (float)$transaksi['tarif_per_jam'], $diskon);

        // 3. Simpan data keluar
        $idParkir    = (int)$id_parkir;
        $idUser      = (int)$id_user;
        $biayaSql    = number_format($biayaTotal, 2, '.', '');
        $waktuKeluar = date('Y-m-d H:i:s');

        $berhasil = $this->jalankan("UPDATE tb_transaksi
                SET waktu_keluar = '$waktuKeluar',
                    durasi_jam = '$durasiJam',
                    biaya_total = '$biayaSql',
                    status = 'keluar',
                    id_user = '$idUser'
                WHERE id_parkir = '$idParkir'");

        if (!$berhasil) {
            return ['status' => false, 'message' => 'Gagal memproses transaksi keluar: ' . mysqli_error($this->conn)];
        }

        (new m_area())->updateTerisi($transaksi['id_area'], -1);

        $infoDiskon = $diskon > 0 ? ", diskon member {$diskon}%" : "";
        (new m_log())->catat($id_user, "Check-out parkir plat: {$transaksi['plat_nomor']} (Total: Rp "
            . number_format($biayaTotal, 0, ',', '.') . "$infoDiskon)");

        return [
            'status'      => true,
            'id_parkir'   => $id_parkir,
            'durasi_jam'  => $durasiJam,
            'diskon'      => $diskon,
            'biaya_total' => $biayaTotal,
            'message'     => 'Pembayaran parkir berhasil diselesaikan.'
        ];
    }

    // ------------------------------------------------------------------
    // Pengambilan data
    // ------------------------------------------------------------------

    public function getById($id_parkir) {
        $id   = (int)$id_parkir;
        $data = $this->ambilTransaksi($this->selectTransaksi() . " WHERE t.id_parkir = '$id' LIMIT 1");
        return $data ? $data[0] : null;
    }

    // Kendaraan yang sedang parkir (status = 'masuk')
    public function getKendaraanAktif($search = '') {
        $kondisi = ["t.status = 'masuk'"];
        if (!empty($search)) {
            $kondisi[] = $this->kondisiCari($search);
        }
        return $this->ambilTransaksi($this->selectTransaksi() . $this->buatWhere($kondisi) . " ORDER BY t.waktu_masuk DESC");
    }

    // Riwayat transaksi dengan filter dan pagination
    public function getAll($search = '', $status = '', $limit = 50, $offset = 0) {
        return $this->ambilTransaksi($this->selectTransaksi() . $this->whereRiwayat($search, $status)
                                   . " ORDER BY t.id_parkir DESC" . $this->buatLimit($limit, $offset));
    }

    // Jumlah transaksi sesuai filter (untuk pagination)
    public function countAll($search = '', $status = '') {
        return $this->hitung("SELECT COUNT(*) AS total
                              FROM tb_transaksi t
                              JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                              JOIN tb_area_parkir a ON t.id_area = a.id_area" . $this->whereRiwayat($search, $status));
    }

    // Rekap laporan untuk Owner
    public function getRekapLaporan($tgl_mulai = '', $tgl_selesai = '', $jenis_kendaraan = '', $status = '') {
        $kondisi = [];
        if (!empty($tgl_mulai)) {
            $kondisi[] = "DATE(t.waktu_masuk) >= '" . $this->esc($tgl_mulai) . "'";
        }
        if (!empty($tgl_selesai)) {
            $kondisi[] = "DATE(t.waktu_masuk) <= '" . $this->esc($tgl_selesai) . "'";
        }
        if (!empty($jenis_kendaraan)) {
            $kondisi[] = "k.jenis_kendaraan = '" . $this->esc($jenis_kendaraan) . "'";
        }
        if (!empty($status)) {
            $kondisi[] = "t.status = '" . $this->esc($status) . "'";
        }

        $data = $this->ambilTransaksi($this->selectTransaksi() . $this->buatWhere($kondisi) . " ORDER BY t.waktu_masuk DESC");

        $totalPendapatan = 0;
        $totalDurasi     = 0;
        $countMasuk      = 0;
        $countKeluar     = 0;

        foreach ($data as $row) {
            if ($row['status'] === 'keluar') {
                $totalPendapatan += (float)$row['biaya_total'];
                $totalDurasi     += (int)$row['durasi_jam'];
                $countKeluar++;
            } else {
                $countMasuk++;
            }
        }

        return [
            'transaksi'        => $data,
            'total_transaksi'  => count($data),
            'total_pendapatan' => $totalPendapatan,
            'total_durasi'     => $totalDurasi,
            'count_masuk'      => $countMasuk,
            'count_keluar'     => $countKeluar
        ];
    }

    // Angka-angka ringkasan untuk dashboard
    public function getStatistikDashboard() {
        $aktif    = $this->ambilSatu("SELECT COUNT(*) AS jumlah FROM tb_transaksi WHERE status = 'masuk'") ?? [];
        $hariIni  = $this->ambilSatu("SELECT COUNT(*) AS jumlah, COALESCE(SUM(biaya_total), 0) AS pendapatan
                                      FROM tb_transaksi
                                      WHERE DATE(waktu_keluar) = CURDATE() AND status = 'keluar'") ?? [];
        // Kendaraan masuk hari ini (berdasarkan tanggal waktu_masuk; otomatis 0 saat ganti hari)
        $masukHariIni = $this->ambilSatu("SELECT COUNT(*) AS jumlah FROM tb_transaksi WHERE DATE(waktu_masuk) = CURDATE()") ?? [];
        $selesai  = $this->ambilSatu("SELECT COALESCE(SUM(biaya_total), 0) AS pendapatan, COUNT(*) AS jumlah
                                      FROM tb_transaksi WHERE status = 'keluar'") ?? [];
        $area     = $this->ambilSatu("SELECT SUM(kapasitas) AS kapasitas, SUM(terisi) AS terisi FROM tb_area_parkir") ?? [];
        $user     = $this->ambilSatu("SELECT COUNT(*) AS jumlah FROM tb_user") ?? [];
        $kendaraan = $this->ambilSatu("SELECT COUNT(*) AS jumlah FROM tb_kendaraan") ?? [];

        $kapasitas = (int)($area['kapasitas'] ?? 0);
        $terisi    = (int)($area['terisi'] ?? 0);

        return [
            'kendaraan_aktif'     => (int)($aktif['jumlah'] ?? 0),
            'masuk_hari_ini'      => (int)($masukHariIni['jumlah'] ?? 0),
            'keluar_hari_ini'     => (int)($hariIni['jumlah'] ?? 0),
            'transaksi_hari_ini'  => (int)($hariIni['jumlah'] ?? 0),
            'pendapatan_hari_ini' => (float)($hariIni['pendapatan'] ?? 0),
            'total_pendapatan'    => (float)($selesai['pendapatan'] ?? 0),
            'total_selesai'       => (int)($selesai['jumlah'] ?? 0),
            'total_kapasitas'     => $kapasitas,
            'total_terisi'        => $terisi,
            'slot_tersedia'       => max(0, $kapasitas - $terisi),
            'total_user'          => (int)($user['jumlah'] ?? 0),
            'total_kendaraan'     => (int)($kendaraan['jumlah'] ?? 0)
        ];
    }
}
