# Panduan Deploy POS Kasir ke InfinityFree

Panduan ini untuk membuat aplikasi POS Kasir PHP Native + MySQL bisa diakses publik lewat InfinityFree.

## A. Yang Perlu Disiapkan

- Akun InfinityFree sudah aktif.
- Repo GitHub sudah ada: `https://github.com/berdignas/pos_kasir`
- File project lokal ada di `C:\laragon\www\pos-kasir-tailadmin`
- File database ada di `database.sql`

InfinityFree mendukung PHP dan MySQL, jadi aplikasi ini bisa jalan di sana. Tetapi InfinityFree tidak otomatis deploy dari GitHub seperti Netlify. Cara paling aman adalah upload file ke `htdocs` melalui File Manager atau FTP.

## B. Buat Hosting Account di InfinityFree

1. Login ke InfinityFree.
2. Masuk ke halaman `Client Area`.
3. Klik `Create Account`.
4. Pilih subdomain gratis, misalnya:

```text
poskasir.infinityfreeapp.com
```

5. Tunggu sampai hosting account aktif.
6. Klik `Manage` pada hosting account tersebut.
7. Klik `Control Panel`.

## C. Buat Database MySQL

1. Di Control Panel InfinityFree, cari menu `MySQL Databases`.
2. Buat database baru, misalnya:

```text
poskasir
```

3. Setelah dibuat, catat data berikut:

```text
MySQL Hostname
MySQL Database Name
MySQL Username
MySQL Password
```

Biasanya nama database dan username di InfinityFree punya prefix tertentu, misalnya:

```text
if0_12345678_poskasir
if0_12345678
sqlXXX.infinityfree.com
```

Jangan menebak data ini. Ambil langsung dari halaman `MySQL Databases`.

## D. Import Database

1. Masih di Control Panel, buka `phpMyAdmin`.
2. Cari database yang baru dibuat.
3. Klik `Connect Now` atau buka database tersebut.
4. Klik tab `Import`.
5. Pilih file:

```text
database.sql
```

6. Klik `Go`.
7. Pastikan tabel berikut muncul:

```text
categories
products
sales
sale_items
stock_movements
```

## E. Edit Koneksi Database

Sebelum upload, edit file:

```text
config/database.php
```

Ubah bagian berikut sesuai data dari InfinityFree:

```php
$dbConfig = [
    'host' => 'MYSQL_HOSTNAME_DARI_INFINITYFREE',
    'port' => '3306',
    'database' => 'MYSQL_DATABASE_NAME_DARI_INFINITYFREE',
    'username' => 'MYSQL_USERNAME_DARI_INFINITYFREE',
    'password' => 'MYSQL_PASSWORD_DARI_INFINITYFREE',
    'charset' => 'utf8mb4',
];
```

Contoh bentuknya:

```php
$dbConfig = [
    'host' => 'sqlXXX.infinityfree.com',
    'port' => '3306',
    'database' => 'if0_12345678_poskasir',
    'username' => 'if0_12345678',
    'password' => 'password_database',
    'charset' => 'utf8mb4',
];
```

## F. Siapkan File Yang Akan Diupload

Jangan upload folder:

```text
.git
node_modules
```

Upload file/folder ini:

```text
app
build
config
migrations
pages
uploads
.browserslistrc
.gitignore
.prettierrc
database.sql
index.php
LICENSE
package.json
package-lock.json
PANDUAN_DEPLOY_INFINITYFREE.md
PANDUAN_INSTALL_DOSEN.md
postcss.config.js
README.md
SETUP_POS.md
webpack.config.js
```

Yang paling penting untuk aplikasi berjalan:

```text
app
build
config
pages
uploads
index.php
database.sql
```

## G. Upload File ke InfinityFree

### Opsi 1: File Manager

1. Di Control Panel InfinityFree, buka `Online File Manager`.
2. Masuk ke folder:

```text
htdocs
```

3. Hapus file default seperti:

```text
index2.html
```

4. Upload semua isi project ke dalam `htdocs`.

Struktur akhirnya harus seperti ini:

```text
htdocs/index.php
htdocs/app
htdocs/build
htdocs/config
htdocs/pages
htdocs/uploads
htdocs/database.sql
```

Jangan upload folder project sebagai subfolder seperti:

```text
htdocs/pos-kasir-tailadmin/index.php
```

Kecuali kamu memang ingin URL menjadi:

```text
https://domainmu/pos-kasir-tailadmin/
```

### Opsi 2: FTP/FileZilla

1. Di InfinityFree, buka detail FTP account.
2. Catat:

```text
FTP Hostname
FTP Username
FTP Password
FTP Port
```

3. Buka FileZilla.
4. Login memakai data FTP.
5. Buka folder server:

```text
htdocs
```

6. Upload semua isi project ke folder `htdocs`.

## H. Cek Folder Upload Foto Produk

Pastikan folder ini ada di hosting:

```text
htdocs/uploads/products
```

Jika upload foto produk gagal, cek permission folder tersebut. Normalnya di InfinityFree folder upload bisa dipakai langsung.

## I. Buka Website

Setelah file dan database selesai:

```text
https://subdomainmu.infinityfreeapp.com
```

Contoh:

```text
https://poskasir.infinityfreeapp.com
```

Halaman utama akan langsung masuk ke halaman kasir.

## J. Kalau Error Database

Jika muncul error koneksi database:

1. Cek ulang `config/database.php`.
2. Pastikan `host` memakai hostname dari InfinityFree, bukan `localhost`.
3. Pastikan database sudah dibuat di menu `MySQL Databases`.
4. Pastikan `database.sql` sudah diimport lewat phpMyAdmin.
5. Pastikan username/password database benar.

## K. Kalau Tampilan Berantakan

Pastikan folder ini ikut terupload:

```text
build
```

Folder `build` berisi CSS dan JavaScript TailAdmin yang dipakai aplikasi.

## L. Kalau Halaman 403 atau Index Tidak Muncul

Cek struktur file di hosting. File `index.php` harus berada langsung di:

```text
htdocs/index.php
```

Jika masih ada file default dari InfinityFree, hapus file default tersebut.

## M. Update Aplikasi Setelah Ada Perubahan

Setelah perubahan di laptop:

```powershell
git add .
git commit -m "Update aplikasi POS"
git push
```

Lalu upload ulang file yang berubah ke InfinityFree melalui File Manager atau FTP.

InfinityFree free hosting umumnya tidak menyediakan auto deploy langsung dari GitHub. Untuk otomatisasi, kamu bisa memakai GitHub Actions + FTP, tetapi untuk tugas kuliah cara manual lebih sederhana dan lebih aman.

