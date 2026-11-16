<?php
// model/m_user.php
// Model pengelolaan data pengguna (tb_user)

require_once __DIR__ . '/m_base.php';

class m_user extends m_base {

    // Cari akun aktif berdasarkan username atau nama lengkap (untuk login)
    public function login($username) {
        $user = $this->esc($username);
        return $this->ambilSatu("SELECT * FROM tb_user
                                 WHERE (username = '$user' OR nama_lengkap = '$user') AND status_aktif = 1 LIMIT 1");
    }

    public function getByUsername($username) {
        $user = $this->esc($username);
        return $this->ambilSatu("SELECT * FROM tb_user WHERE username = '$user' LIMIT 1");
    }

    public function getById($id_user) {
        $id = (int)$id_user;
        return $this->ambilSatu("SELECT * FROM tb_user WHERE id_user = '$id' LIMIT 1");
    }

    // Kondisi pencarian + filter role (dipakai getAll dan countAll)
    private function whereFilter($search, $role) {
        $kondisi = [];
        if (!empty($search)) {
            $cari = $this->esc("%" . $search . "%");
            $kondisi[] = "(nama_lengkap LIKE '$cari' OR username LIKE '$cari')";
        }
        if (!empty($role)) {
            $kondisi[] = "role = '" . $this->esc($role) . "'";
        }
        return $this->buatWhere($kondisi);
    }

    public function getAll($search = '', $role = '', $limit = 50, $offset = 0) {
        return $this->ambilSemua("SELECT * FROM tb_user" . $this->whereFilter($search, $role)
                               . " ORDER BY id_user DESC" . $this->buatLimit($limit, $offset));
    }

    public function countAll($search = '', $role = '') {
        return $this->hitung("SELECT COUNT(*) AS total FROM tb_user" . $this->whereFilter($search, $role));
    }

    // Simpan user baru. $passwordHash harus sudah di-hash. false jika username sudah dipakai.
    private function simpan($nama_lengkap, $username, $passwordHash, $role, $status_aktif) {
        if ($this->getByUsername($username)) {
            return false;
        }
        $nama   = $this->esc($nama_lengkap);
        $user   = $this->esc($username);
        $hash   = $this->esc($passwordHash);
        $role   = $this->esc($role);
        $status = (int)$status_aktif;

        return $this->jalankan("INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif)
                                VALUES ('$nama', '$user', '$hash', '$role', '$status')");
    }

    // Tambah user oleh admin (password di-hash di sini)
    public function tambah($nama_lengkap, $username, $password, $role, $status_aktif = 1) {
        return $this->simpan($nama_lengkap, $username, password_hash($password, PASSWORD_DEFAULT), $role, $status_aktif);
    }

    // Ubah user oleh admin. Password hanya diganti jika diisi.
    public function update($id_user, $nama_lengkap, $username, $role, $status_aktif, $password = null) {
        // Username tidak boleh dipakai user lain
        $existing = $this->getByUsername($username);
        if ($existing && $existing['id_user'] != $id_user) {
            return false;
        }

        $id     = (int)$id_user;
        $nama   = $this->esc($nama_lengkap);
        $user   = $this->esc($username);
        $role   = $this->esc($role);
        $status = (int)$status_aktif;

        $kolomPassword = '';
        if (!empty($password)) {
            $kolomPassword = ", password = '" . $this->esc(password_hash($password, PASSWORD_DEFAULT)) . "'";
        }

        return $this->jalankan("UPDATE tb_user
                                SET nama_lengkap = '$nama', username = '$user'$kolomPassword, role = '$role', status_aktif = '$status'
                                WHERE id_user = '$id'");
    }

    public function delete($id_user) {
        $id = (int)$id_user;
        return $this->jalankan("DELETE FROM tb_user WHERE id_user = '$id'");
    }

    // Balik status aktif <-> nonaktif
    public function toggleStatus($id_user) {
        $user = $this->getById($id_user);
        if (!$user) {
            return false;
        }

        $id          = (int)$id_user;
        $statusBaru  = $user['status_aktif'] == 1 ? 0 : 1;

        return $this->jalankan("UPDATE tb_user SET status_aktif = '$statusBaru' WHERE id_user = '$id'");
    }
}
