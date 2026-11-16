<?php
// model/m_log.php
// Model pencatatan log aktivitas pengguna (tb_log_aktivitas)

require_once __DIR__ . '/m_base.php';

class m_log extends m_base {

    public function catat($id_user, $aktivitas) {
        $id        = (int)$id_user;
        $aktivitas = $this->esc($aktivitas);
        $waktu     = date('Y-m-d H:i:s');

        return $this->jalankan("INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas)
                                VALUES ('$id', '$aktivitas', '$waktu')");
    }

    public function getAll($limit = 50, $offset = 0) {
        return $this->ambilSemua("SELECT l.*, u.nama_lengkap, u.username, u.role
                                  FROM tb_log_aktivitas l
                                  LEFT JOIN tb_user u ON l.id_user = u.id_user
                                  ORDER BY l.id_log DESC" . $this->buatLimit($limit, $offset));
    }

    public function countAll() {
        return $this->hitung("SELECT COUNT(*) AS total FROM tb_log_aktivitas");
    }

    public function getRecent($limit = 8) {
        return $this->getAll($limit, 0);
    }
}
