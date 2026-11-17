CREATE TABLE penjual (
  id_penjual INT AUTO_INCREMENT PRIMARY KEY,
  nama_toko VARCHAR(100) NOT NULL,
  telepon VARCHAR(20),
  alamat TEXT
) ENGINE=InnoDB;

CREATE TABLE kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(60) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
  id_produk INT AUTO_INCREMENT PRIMARY KEY,
  id_penjual INT NOT NULL,
  id_kategori INT NOT NULL,
  nama_produk VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  harga DECIMAL(12,2) NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  gambar VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_penjual) REFERENCES penjual(id_penjual),
  FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
) ENGINE=InnoDB;

CREATE TABLE pembeli (
  id_pembeli INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  telepon VARCHAR(20) NOT NULL,
  alamat TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE pesanan (
  id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
  id_pembeli INT NOT NULL,
  tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(12,2) NOT NULL,
  status ENUM('baru','diproses','dikirim','selesai','batal') DEFAULT 'baru',
  FOREIGN KEY (id_pembeli) REFERENCES pembeli(id_pembeli)
) ENGINE=InnoDB;

CREATE TABLE detail_pesanan (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan INT NOT NULL,
  id_produk INT NOT NULL,
  jumlah INT NOT NULL,
  harga_satuan DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
  FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
) ENGINE=InnoDB;

INSERT INTO penjual (nama_toko, telepon, alamat) VALUES ('Toko Pasar Bengkulu','081234567890','Jl. Pasar Minggu, Bengkulu');
INSERT INTO kategori (nama_kategori) VALUES ('Sayur & Buah'),('Kerajinan'),('Makanan Olahan');
INSERT INTO produk (id_penjual,id_kategori,nama_produk,deskripsi,harga,stok) VALUES
(1,1,'Pisang Barangan 1 Sisir','Pisang segar dari petani lokal.',20000,50),
(1,2,'Anyaman Bambu','Tempat buah anyaman bambu tangan.',45000,15),
(1,3,'Kerupuk Kemplang 500g','Kerupuk khas Sumatera.',25000,30);

INSERT INTO kategori (nama_kategori) VALUES ('Rempah & Bumbu'),('Batik & Kain');
INSERT INTO penjual (nama_toko, telepon, alamat) VALUES ('Kios Bu Sari','082211223344','Pasar Panorama, Bengkulu'),('Batik Besurek Pak Rudi','085266778899','Jl. Fatmawati, Bengkulu');
INSERT INTO produk (id_penjual,id_kategori,nama_produk,deskripsi,harga,stok) VALUES
(2,1,'Sayur Bayam Segar 1 Ikat','Bayam hijau segar dipetik pagi hari.',5000,80),
(2,1,'Jeruk Kalamansi 1 Kg','Jeruk kecil segar, cocok untuk sambal dan minuman.',18000,40),
(2,4,'Cabai Rawit Merah 250g','Cabai rawit pedas segar langsung dari petani.',15000,25),
(2,4,'Kunyit Bubuk Asli 100g','Kunyit giling murni tanpa campuran.',12000,8),
(1,3,'Pendap Bengkulu 5 Pcs','Makanan khas Bengkulu berbahan ikan dan daun talas.',35000,20),
(1,3,'Lempuk Durian 250g','Dodol durian khas Bengkulu.',28000,12),
(1,2,'Tas Anyaman Pandan','Tas anyaman pandan buatan pengrajin lokal.',65000,9),
(3,5,'Kain Batik Besurek 2 Meter','Batik khas Bengkulu motif kaligrafi Besurek.',185000,10),
(3,5,'Selendang Batik Besurek','Selendang batik tulis motif Rafflesia.',95000,15);
