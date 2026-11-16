<?php
// model/m_area.php
// Model master area parkir (tb_area_parkir)

require_once __DIR__ . '/m_base.php';

class m_area extends m_base {

    // Kondisi pencarian (dipakai getAll dan countAll)
    private function whereCari($search) {
        if (empty($search)) {
            return '';
        }
        $cari = $this->esc("%" . $search . "%");
        return $this->buatWhere(["(nama_area LIKE '$cari' OR id_area LIKE '$cari')"]);
    }

    public function getAll($search = '', $limit = 50, $offset = 0) {
        return $this->ambilSemua("SELECT * FROM tb_area_parkir" . $this->whereCari($search)
                               . " ORDER BY id_area ASC" . $this->buatLimit($limit, $offset));
    }

    public function countAll($search = '') {
        return $this->hitung("SELECT COUNT(*) AS total FROM tb_area_parkir" . $this->whereCari($search));
    }

    public function getById($id_area) {
        $id = (int)$id_area;
        return $this->ambilSatu("SELECT * FROM tb_area_parkir WHERE id_area = '$id' LIMIT 1");
    }

    public function tambah($nama_area, $kapasitas, $terisi = 0) {
        $nama = $this->esc($nama_area);
        $kap  = (int)$kapasitas;
        $isi  = (int)$terisi;

        return $this->jalankan("INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) VALUES ('$nama', '$kap', '$isi')");
    }

    public function update($id_area, $nama_area, $kapasitas, $terisi = 0) {
        $id   = (int)$id_area;
        $nama = $this->esc($nama_area);
        $kap  = (int)$kapasitas;
        $isi  = (int)$terisi;

        return $this->jalankan("UPDATE tb_area_parkir SET nama_area = '$nama', kapasitas = '$kap', terisi = '$isi' WHERE id_area = '$id'");
    }

    public function delete($id_area) {
        $id = (int)$id_area;
        // Area yang punya transaksi (berjalan maupun riwayat) tidak boleh dihapus
        if ($this->hitung("SELECT COUNT(*) AS total FROM tb_transaksi WHERE id_area = '$id'") > 0) {
            return false;
        }
        return $this->jalankan("DELETE FROM tb_area_parkir WHERE id_area = '$id'");
    }

    // Ubah jumlah terisi (+1 saat masuk, -1 saat keluar)
    public function updateTerisi($id_area, $delta) {
        $id    = (int)$id_area;
        $delta = (int)$delta;

        if ($delta > 0) {
            // Tidak boleh melebihi kapasitas
            $sql = "UPDATE tb_area_parkir SET terisi = terisi + $delta WHERE id_area = '$id' AND terisi < kapasitas";
        } else {
            // Tidak boleh kurang dari 0
            $kurang = abs($delta);
            $sql = "UPDATE tb_area_parkir SET terisi = GREATEST(0, terisi - $kurang) WHERE id_area = '$id'";
        }
        return $this->jalankan($sql);
    }

    // Cek apakah slot masih tersedia
    public function cekKetersediaan($id_area) {
        $area = $this->getById($id_area);
        return $area && ($area['kapasitas'] - $area['terisi']) > 0;
    }
}
