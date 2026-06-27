# Panduan Push GitHub dan Deploy

## 1. Push Ke GitHub

Proyek ini awalnya diambil dari template TailAdmin. Remote bawaan TailAdmin sudah diamankan menjadi `tailadmin-upstream`, jadi sekarang tinggal menambahkan remote GitHub milik sendiri sebagai `origin`.

Langkah aman:

```powershell
cd C:\laragon\www\pos-kasir-tailadmin
git remote add origin https://github.com/USERNAME/NAMA-REPO.git
git push -u origin main
```

Ganti:

```text
USERNAME
NAMA-REPO
```

dengan akun dan nama repo GitHub sendiri.

Jika repo GitHub masih kosong, jangan centang `Add README`, karena proyek ini sudah punya file sendiri.

Commit aplikasi sudah dibuat secara lokal dengan pesan:

```text
Build POS cashier app with PHP and MySQL
```

## 2. Catatan Penting Tentang Netlify

Aplikasi ini memakai:

- PHP Native
- MySQL
- Upload file produk

Netlify tidak cocok untuk menjalankan aplikasi ini secara penuh, karena Netlify tidak menjalankan runtime PHP Native dan tidak menyediakan server MySQL seperti Laragon/XAMPP.

Jika dipaksa deploy ke Netlify, halaman PHP tidak akan berjalan sebagai aplikasi kasir. Yang bisa tampil hanya file statis seperti HTML, CSS, dan JavaScript.

## 3. Hosting Yang Cocok Untuk Aplikasi Ini

Pilih hosting yang mendukung:

- PHP
- MySQL
- File upload
- phpMyAdmin atau akses import SQL

Contoh pilihan:

- Shared hosting cPanel
- InfinityFree
- 000webhost/alternatif hosting PHP gratis
- VPS dengan Apache/Nginx + PHP + MySQL
- Laragon/XAMPP untuk demo lokal di laptop dosen

## 4. Deploy Ke Shared Hosting / cPanel

1. Upload semua file proyek ke folder hosting, biasanya:

```text
public_html
```

2. Buat database MySQL di cPanel.
3. Import file:

```text
database.sql
```

4. Edit file:

```text
config/database.php
```

Sesuaikan:

```php
'host' => 'localhost',
'database' => 'nama_database_hosting',
'username' => 'username_database_hosting',
'password' => 'password_database_hosting',
```

5. Pastikan folder berikut bisa ditulis:

```text
uploads/products
```

6. Buka domain hosting.

## 5. Deploy Lokal Di Laptop Dosen

Untuk penilaian offline/lokal, gunakan panduan:

```text
PANDUAN_INSTALL_DOSEN.md
```

Itu adalah cara paling aman agar tampilan dan fitur berjalan sama seperti di laptop pembuat.

## 6. Kalau Tetap Ingin Masuk Netlify

Gunakan Netlify hanya untuk:

- Menyimpan preview statis.
- Menampilkan dokumentasi project.
- Hosting frontend setelah backend diubah ke API/serverless lain.

Untuk membuat aplikasi ini benar-benar berjalan di Netlify, backend perlu diubah dari PHP Native + MySQL menjadi arsitektur lain, misalnya:

- Frontend static/SPA
- Netlify Functions
- Database cloud
- Object storage untuk foto produk

Itu berarti perlu rewrite backend, bukan sekadar deploy.
