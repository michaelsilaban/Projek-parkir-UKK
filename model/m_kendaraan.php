<?php
// model/m_kendaraan.php
// Model master data kendaraan (tb_kendaraan)
//
// ATURAN MEMBER (tanpa mengubah struktur database):
// - Kendaraan di menu Data Kendaraan (tb_kendaraan.id_user terisi) = MEMBER.
// - Kendaraan yang hanya diinput manual petugas disimpan dengan id_user NULL = UMUM.
//   Barisnya hanya dibutuhkan agar transaksi punya referensi kendaraan.

require_once __DIR__ . '/m_base.php';

class m_kendaraan extends m_base {

    // Bagian awal query yang sama untuk semua pengambilan data kendaraan
    private function selectDasar() {
        return "SELECT k.*, u.nama_lengkap AS nama_petugas, (k.id_user IS NOT NULL) AS is_member
                FROM tb_kendaraan k
                LEFT JOIN tb_user u ON k.id_user = u.id_user";
    }

    // Plat nomor selalu disimpan huruf besar tanpa spasi di tepi
    private function rapikanPlat($plat_nomor) {
        return strtoupper(trim($plat_nomor));
    }

    // Kondisi pencarian & filter tipe ('member' | 'umum' | '')
    private function whereFilter($search, $tipe) {
        $kondisi = [];
        if (!empty($search)) {
            $cari = $this->esc("%" . $search . "%");
            $kondisi[] = "(k.plat_nomor LIKE '$cari' OR k.pemilik LIKE '$cari' OR k.jenis_kendaraan LIKE '$cari')";
        }
        if ($tipe === 'member') {
            $kondisi[] = "k.id_user IS NOT NULL";
        } elseif ($tipe === 'umum') {
            $kondisi[] = "k.id_user IS NULL";
        }
        return $this->buatWhere($kondisi);
    }

    public function getAll($search = '', $limit = 50, $offset = 0, $tipe = '') {
        return $this->ambilSemua($this->selectDasar() . $this->whereFilter($search, $tipe)
                               . " ORDER BY k.id_kendaraan DESC" . $this->buatLimit($limit, $offset));
    }

    public function countAll($search = '', $tipe = '') {
        return $this->hitung("SELECT COUNT(*) AS total FROM tb_kendaraan k
                              LEFT JOIN tb_user u ON k.id_user = u.id_user" . $this->whereFilter($search, $tipe));
    }

    // Daftar member (untuk dipilih petugas saat check-in)
    public function getMembers() {
        return $this->ambilSemua("SELECT k.*,
                                         1 AS is_member,
                                         (SELECT COUNT(*) FROM tb_transaksi t
                                           WHERE t.id_kendaraan = k.id_kendaraan AND t.status = 'masuk') AS sedang_parkir
                                  FROM tb_kendaraan k
                                  WHERE k.id_user IS NOT NULL
                                  ORDER BY k.plat_nomor ASC");
    }

    public function getById($id_kendaraan) {
        $id = (int)$id_kendaraan;
        return $this->ambilSatu($this->selectDasar() . " WHERE k.id_kendaraan = '$id' LIMIT 1");
    }

    public function getByPlat($plat_nomor) {
        $plat = $this->esc($this->rapikanPlat($plat_nomor));
        return $this->ambilSatu($this->selectDasar() . " WHERE k.plat_nomor = '$plat' LIMIT 1");
    }

    // $id_user boleh NULL (kendaraan umum). false jika plat sudah terdaftar.
    public function tambah($plat_nomor, $jenis_kendaraan, $warna, $pemilik, $id_user = null) {
        $platBersih = $this->rapikanPlat($plat_nomor);
        if ($this->getByPlat($platBersih)) {
            return false;
        }

        $plat    = $this->esc($platBersih);
        $jenis   = $this->esc($jenis_kendaraan);
        $warna   = $this->esc($warna);
        $pemilik = $this->esc($pemilik);
        $idUser  = ($id_user === null) ? "NULL" : "'" . (int)$id_user . "'";

        return $this->jalankan("INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
                                VALUES ('$plat', '$jenis', '$warna', '$pemilik', $idUser)");
    }

    // $jadikan_member = true + $id_admin -> kendaraan umum dinaikkan jadi member
    public function update($id_kendaraan, $plat_nomor, $jenis_kendaraan, $warna, $pemilik, $jadikan_member = null, $id_admin = null) {
        $platBersih = $this->rapikanPlat($plat_nomor);

        // Plat tidak boleh dipakai kendaraan lain
        $existing = $this->getByPlat($platBersih);
        if ($existing && $existing['id_kendaraan'] != $id_kendaraan) {
            return false;
        }

        $id      = (int)$id_kendaraan;
        $plat    = $this->esc($platBersih);
        $jenis   = $this->esc($jenis_kendaraan);
        $warna   = $this->esc($warna);
        $pemilik = $this->esc($pemilik);

        $kolomMember = ($jadikan_member && $id_admin !== null) ? ", id_user = '" . (int)$id_admin . "'" : '';

        return $this->jalankan("UPDATE tb_kendaraan
                                SET plat_nomor = '$plat', jenis_kendaraan = '$jenis', warna = '$warna', pemilik = '$pemilik'$kolomMember
                                WHERE id_kendaraan = '$id'");
    }

    public function delete($id_kendaraan) {
        $id = (int)$id_kendaraan;
        // Kendaraan yang punya transaksi (berjalan maupun riwayat) tidak boleh dihapus
        if ($this->hitung("SELECT COUNT(*) AS total FROM tb_transaksi WHERE id_kendaraan = '$id'") > 0) {
            return false;
        }
        return $this->jalankan("DELETE FROM tb_kendaraan WHERE id_kendaraan = '$id'");
    }

    // Ambil kendaraan yang sudah ada, atau buat baru (sebagai kendaraan umum) jika belum terdaftar
    public function getOrCreate($plat_nomor, $jenis_kendaraan, $warna, $pemilik) {
        $platBersih = $this->rapikanPlat($plat_nomor);
        $kendaraan  = $this->getByPlat($platBersih);

        if ($kendaraan) {
            // Data member dikelola admin -> jangan ditimpa oleh input manual petugas
            if (!empty($kendaraan['is_member'])) {
                return $kendaraan;
            }
            // Kendaraan umum: perbarui warna/pemilik jika ada info baru
            if (!empty($warna) || !empty($pemilik)) {
                $warnaBaru   = $warna ?: $kendaraan['warna'];
                $pemilikBaru = $pemilik ?: $kendaraan['pemilik'];
                $this->update($kendaraan['id_kendaraan'], $platBersih, $jenis_kendaraan, $warnaBaru, $pemilikBaru);
                $kendaraan['warna']           = $warnaBaru;
                $kendaraan['pemilik']         = $pemilikBaru;
                $kendaraan['jenis_kendaraan'] = $jenis_kendaraan;
            }
            return $kendaraan;
        }

        // Belum terdaftar -> buat baru dengan nilai bawaan
        $this->tambah($platBersih, $jenis_kendaraan, $warna ?: '-', $pemilik ?: 'Pengunjung Umum');
        return $this->getByPlat($platBersih);
    }
}
