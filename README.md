# Pasar Online - Web Marketplace (PHP Native + MySQLi + Bootstrap)

1. Buat database MySQL, impor `database.sql`.
2. Salin `config/koneksi.example.php` menjadi `config/koneksi.php`, lalu isi kredensial dan hash password admin.
3. Upload semua file ke `htdocs`. Admin: `/admin/login.php`.

`config/koneksi.php` masuk `.gitignore` agar kredensial tidak ikut ke GitHub.

---
## REST API (Soal 12)
Endpoint di folder `api/`: `produk.php`, `kategori.php`, `pesanan.php`.
Dokumentasi: `docs/API.md`. Koleksi Postman: `docs/postman_collection.json`.

Jalankan di XAMPP:
1. Salin folder ke `C:\xampp\htdocs\marketplace`, jalankan Apache dan MySQL.
2. Buat database `marketplace` di phpMyAdmin, import `database_lengkap.sql`.
3. Salin `config/api_config.example.php` menjadi `config/api_config.php`.
4. Buka `http://localhost/marketplace/api/produk.php`.
