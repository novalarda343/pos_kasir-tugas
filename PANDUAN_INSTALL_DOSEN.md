# Panduan Install Aplikasi POS Kasir

Panduan ini dibuat agar aplikasi POS Kasir bisa dibuka di laptop dosen dengan tampilan yang sama seperti di laptop pembuat.

## Kebutuhan

- Laragon atau XAMPP
- PHP 8.0 atau lebih baru
- MySQL
- Browser Chrome/Edge/Firefox

Disarankan memakai Laragon karena proyek ini dibuat dan dites di Laragon.

## Cara Install Dengan Laragon

1. Extract file ZIP `pos-kasir-tailadmin-siap-kirim-dosen.zip`.
2. Copy folder hasil extract bernama `pos-kasir-tailadmin` ke:

```text
C:\laragon\www\
```

3. Buka Laragon.
4. Klik `Start All` agar Apache/Nginx dan MySQL berjalan.
5. Buka phpMyAdmin dari Laragon, atau buka:

```text
http://localhost/phpmyadmin
```

6. Buat database baru dengan nama:

```text
pos_kasir_tailadmin
```

7. Import file:

```text
C:\laragon\www\pos-kasir-tailadmin\database.sql
```

8. Buka aplikasi di browser:

```text
http://localhost/pos-kasir-tailadmin
```

## Cara Import Database Lewat Terminal Laragon

Jika ingin import database lewat terminal Laragon, jalankan:

```powershell
Get-Content C:\laragon\www\pos-kasir-tailadmin\database.sql | C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root
```

Catatan: path `mysql-8.0.30-winx64` bisa berbeda sesuai versi MySQL Laragon di laptop dosen.

## Cara Install Dengan XAMPP

1. Extract file ZIP.
2. Copy folder `pos-kasir-tailadmin` ke:

```text
C:\xampp\htdocs\
```

3. Jalankan Apache dan MySQL dari XAMPP Control Panel.
4. Buka phpMyAdmin:

```text
http://localhost/phpmyadmin
```

5. Buat database:

```text
pos_kasir_tailadmin
```

6. Import file:

```text
C:\xampp\htdocs\pos-kasir-tailadmin\database.sql
```

7. Buka aplikasi:

```text
http://localhost/pos-kasir-tailadmin
```

## Konfigurasi Database

Jika aplikasi tidak bisa konek database, edit file:

```text
config/database.php
```

Default konfigurasi:

```php
'host' => '127.0.0.1',
'port' => '3306',
'database' => 'pos_kasir_tailadmin',
'username' => 'root',
'password' => '',
```

Jika MySQL dosen memakai password, isi bagian:

```php
'password' => 'password_mysql_dosen',
```

## Halaman Utama

Setelah aplikasi terbuka, halaman utama langsung masuk ke:

```text
Kasir
```

Fitur yang tersedia:

- Kasir POS dengan tampilan kartu produk.
- Filter kategori dan pencarian produk.
- Keranjang belanja di sisi kanan.
- Popup pembayaran.
- Diskon, PPN, metode pembayaran.
- Cetak struk.
- Manajemen produk.
- Tambah, edit, dan hapus foto produk.
- Manajemen kategori.
- Manajemen stok.
- Riwayat transaksi.
- Laporan ringkas.

## Jika Foto Produk Tidak Muncul

Pastikan folder berikut ada:

```text
uploads/products
```

Pastikan folder tersebut bisa ditulis oleh server lokal. Di Laragon dan XAMPP biasanya otomatis bisa.

## Jika Tampilan Berantakan

Pastikan folder berikut ikut tercopy:

```text
build
```

Folder `build` berisi file CSS dan JavaScript TailAdmin yang sudah siap pakai. Tidak perlu menjalankan `npm install` lagi untuk membuka aplikasi.

## Akun Login

Aplikasi versi ini belum memakai login. Dosen bisa langsung membuka halaman kasir lewat browser.

## Catatan Untuk Penilaian

Aplikasi ini dibuat menggunakan:

- PHP Native
- MySQL
- TailAdmin Tailwind Dashboard Template
- Alpine.js untuk interaksi kasir di browser

