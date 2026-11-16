-- Jalankan sekali pada database yang sudah ada
-- Cegah kendaraan yang dihapus ikut menghapus transaksinya (sebelumnya CASCADE)
ALTER TABLE tb_transaksi DROP FOREIGN KEY fk_transaksi_kendaraan;
ALTER TABLE tb_transaksi ADD CONSTRAINT fk_transaksi_kendaraan
    FOREIGN KEY (id_kendaraan) REFERENCES tb_kendaraan (id_kendaraan)
    ON DELETE RESTRICT ON UPDATE CASCADE;
