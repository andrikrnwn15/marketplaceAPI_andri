# Dokumen Rancangan REST API - Pasar Online (Marketplace)

**Nama:** Andri Kurniawan  **NPM:** 2355201147

## A. Analisis dan Perancangan API

### a. Fungsi Web Service/API
Web Service/API adalah antarmuka yang memungkinkan aplikasi lain membaca dan mengirim data marketplace melalui HTTP dengan format JSON, tanpa mengakses database atau halaman HTML secara langsung.

Fungsi dalam marketplace:
- Menyediakan data produk, kategori, dan pesanan untuk aplikasi mobile, aplikasi kasir, atau situs mitra.
- Menerima pesanan dari aplikasi lain dan menyimpannya ke database yang sama dengan aplikasi web.

Alasan API diperlukan untuk komunikasi antar-aplikasi:
1. **Interoperabilitas:** Android, iOS, Python, atau Node.js dapat memakai data yang sama lewat HTTP dan JSON.
2. **Keamanan:** aplikasi luar tidak memegang kredensial database; semua akses melewati validasi server.
3. **Satu sumber data:** web, mobile, dan pihak ketiga memakai data yang sama dan selalu konsisten.
4. **Aturan bisnis terpusat:** pengecekan stok dan perhitungan total hanya ada di server.

### b. Rancangan Endpoint

| No | Method | Endpoint | Fungsi | Status sukses |
|---|---|---|---|---|
| 1 | GET | `/api/produk.php` | Menampilkan seluruh produk (filter `q`, `id_kategori`, `id_penjual`, `page`, `limit`) | 200 |
| 2 | GET | `/api/produk.php?id=1` | Menampilkan detail produk | 200 |
| 3 | GET | `/api/kategori.php` | Menampilkan daftar kategori dan jumlah produk | 200 |
| 4 | GET | `/api/kategori.php?id=1` | Menampilkan detail kategori | 200 |
| 5 | POST | `/api/pesanan.php` | Membuat pesanan baru | 201 |
| 6 | GET | `/api/pesanan.php?id=1` | Menampilkan detail pesanan | 200 |
| 7 | GET | `/api/pesanan.php` | Menampilkan daftar pesanan terbaru | 200 |

**Kode status kesalahan**

| Kode | Arti | Contoh |
|---|---|---|
| 400 | Request tidak valid | id bukan angka, body bukan JSON |
| 404 | Data tidak ditemukan | produk id 999 |
| 405 | Method tidak diizinkan | DELETE ke produk.php |
| 422 | Data tidak lengkap atau stok kurang | field `items` kosong |
| 500 | Kesalahan server | koneksi database gagal |

### c. Struktur JSON

**Daftar produk** `GET /api/produk.php`
```json
{
  "status": "success",
  "total": 12,
  "page": 1,
  "limit": 20,
  "data": [
    {
      "id": 1,
      "nama": "Pisang Barangan 1 Sisir",
      "deskripsi": "Pisang segar dari petani lokal.",
      "harga": 20000,
      "stok": 50,
      "gambar": null,
      "gambar_url": null,
      "kategori": { "id": 1, "nama": "Sayur & Buah" },
      "penjual": { "id": 1, "nama_toko": "Toko Pasar Bengkulu", "telepon": "081234567890" }
    }
  ]
}
```

**Detail produk** `GET /api/produk.php?id=1`
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "nama": "Pisang Barangan 1 Sisir",
    "deskripsi": "Pisang segar dari petani lokal.",
    "harga": 20000,
    "stok": 50,
    "gambar": null,
    "gambar_url": null,
    "kategori": { "id": 1, "nama": "Sayur & Buah" },
    "penjual": { "id": 1, "nama_toko": "Toko Pasar Bengkulu", "telepon": "081234567890" }
  }
}
```

**Request pesanan** `POST /api/pesanan.php` (Content-Type: application/json)
```json
{
  "nama": "Budi Santoso",
  "email": "budi@contoh.com",
  "telepon": "081234500000",
  "alamat": "Jl. Kampus No. 1, Bengkulu",
  "items": [
    { "id_produk": 1, "jumlah": 2 },
    { "id_produk": 3, "jumlah": 1 }
  ]
}
```

**Response error**
```json
{ "status": "error", "code": 404, "message": "Produk dengan id 999 tidak ditemukan." }
```

> Contoh di atas adalah struktur rancangan. Untuk laporan pengujian, pakai response asli dari aplikasi Anda.

---

## C. Laporan Hasil Pengujian (isi setelah menguji di XAMPP)

| No | Pengujian | Request | Status diharapkan | Status hasil | Screenshot |
|---|---|---|---|---|---|
| 1 | Semua produk | GET /api/produk.php | 200 | | |
| 2 | Detail produk | GET /api/produk.php?id=1 | 200 | | |
| 3 | Produk tidak ada | GET /api/produk.php?id=999 | 404 | | |
| 4 | Semua kategori | GET /api/kategori.php | 200 | | |
| 5 | Buat pesanan | POST /api/pesanan.php | 201 | | |
| 6 | Stok tidak cukup | POST /api/pesanan.php | 422 | | |
| 7 | Data tidak lengkap | POST /api/pesanan.php | 422 | | |
| 8 | Method salah | DELETE /api/produk.php | 405 | | |
| 9 | Detail pesanan | GET /api/pesanan.php?id=1 | 200 | | |
| 10 | Data tersimpan di MySQL | SELECT pada pembeli, pesanan, detail_pesanan, produk | data ada, stok berkurang | | |

URL repository GitHub: `https://github.com/__________/__________`
