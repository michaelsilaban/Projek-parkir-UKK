<?php
// model/m_base.php
// Class induk semua model: menyiapkan koneksi database dan fungsi bantu query
// yang dipakai bersama, supaya tiap model tidak menulis ulang kode yang sama.

require_once __DIR__ . '/m_koneksi.php';

abstract class m_base {
    protected $conn;

    public function __construct() {
        $this->conn = (new Koneksi())->getKoneksi();
    }

    // Amankan teks sebelum dimasukkan ke query
    protected function esc($teks) {
        return mysqli_real_escape_string($this->conn, (string)$teks);
    }

    // Jalankan query tulis (INSERT / UPDATE / DELETE) -> true / false
    protected function jalankan($sql) {
        return mysqli_query($this->conn, $sql);
    }

    // Ambil banyak baris sebagai array
    protected function ambilSemua($sql) {
        $hasil = mysqli_query($this->conn, $sql);
        $data  = [];
        while ($hasil && $baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    // Ambil satu baris, atau null jika tidak ada
    protected function ambilSatu($sql) {
        $hasil = mysqli_query($this->conn, $sql);
        return $hasil && mysqli_num_rows($hasil) > 0 ? mysqli_fetch_assoc($hasil) : null;
    }

    // Ambil angka dari kolom "total" (untuk SELECT COUNT(*) AS total ...)
    protected function hitung($sql) {
        $baris = $this->ambilSatu($sql);
        return (int)($baris['total'] ?? 0);
    }

    // Gabungkan daftar kondisi menjadi klausa WHERE (kosong jika tidak ada kondisi)
    protected function buatWhere(array $kondisi) {
        return empty($kondisi) ? '' : ' WHERE ' . implode(' AND ', $kondisi);
    }

    // Klausa LIMIT/OFFSET yang aman (dipaksa jadi angka)
    protected function buatLimit($limit, $offset) {
        return ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
    }
}
