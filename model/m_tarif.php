<?php
// model/m_tarif.php
// Model master tarif parkir (tb_tarif)

require_once __DIR__ . '/m_base.php';

class m_tarif extends m_base {

    public function getAll() {
        return $this->ambilSemua("SELECT * FROM tb_tarif ORDER BY id_tarif ASC");
    }

    public function getById($id_tarif) {
        $id = (int)$id_tarif;
        return $this->ambilSatu("SELECT * FROM tb_tarif WHERE id_tarif = '$id' LIMIT 1");
    }

    public function getByJenis($jenis_kendaraan) {
        $jenis = $this->esc($jenis_kendaraan);
        return $this->ambilSatu("SELECT * FROM tb_tarif WHERE jenis_kendaraan = '$jenis' LIMIT 1");
    }

    public function tambah($jenis_kendaraan, $tarif_per_jam) {
        // Satu jenis kendaraan hanya boleh punya satu tarif
        if ($this->getByJenis($jenis_kendaraan)) {
            return false;
        }
        $jenis = $this->esc($jenis_kendaraan);
        $tarif = (float)$tarif_per_jam;

        return $this->jalankan("INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis', '$tarif')");
    }

    public function update($id_tarif, $jenis_kendaraan, $tarif_per_jam) {
        $id    = (int)$id_tarif;
        $jenis = $this->esc($jenis_kendaraan);
        $tarif = (float)$tarif_per_jam;

        return $this->jalankan("UPDATE tb_tarif SET jenis_kendaraan = '$jenis', tarif_per_jam = '$tarif' WHERE id_tarif = '$id'");
    }

    public function delete($id_tarif) {
        $id = (int)$id_tarif;
        // Tarif yang sudah dipakai transaksi (berjalan maupun riwayat) tidak boleh dihapus
        if ($this->hitung("SELECT COUNT(*) AS total FROM tb_transaksi WHERE id_tarif = '$id'") > 0) {
            return false;
        }
        return $this->jalankan("DELETE FROM tb_tarif WHERE id_tarif = '$id'");
    }
}
