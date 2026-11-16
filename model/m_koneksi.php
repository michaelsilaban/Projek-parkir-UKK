<?php

class Koneksi {
    private $host = "localhost"; //localhost
    private $user = "root"; //sesuaiin
    private $db   = "parkir";
    private $pass = ""; //kosongin

    private $koneksi = null; // tetap null jika koneksi gagal

    public function __construct() {
        $sambungan = @mysqli_connect($this->host, $this->user, $this->pass, $this->db);

        if ($sambungan) {
            mysqli_set_charset($sambungan, "utf8mb4");
            mysqli_query($sambungan, "SET time_zone = '+07:00'");
            $this->koneksi = $sambungan;
        }
    }

    public function getKoneksi() {
        return $this->koneksi;
    }
}
