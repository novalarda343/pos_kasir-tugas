# POS Kasir TailAdmin

Aplikasi POS kasir dan manajemen stok sederhana berbasis PHP Native, MySQL, dan template TailAdmin.

Untuk pengiriman ke dosen, lihat panduan lengkap di `PANDUAN_INSTALL_DOSEN.md`.

## Lokasi proyek

`C:\laragon\www\pos-kasir-tailadmin`

## Cara menjalankan

1. Buka Laragon.
2. Start Apache/Nginx dan MySQL.
3. Import database:

```powershell
Get-Content C:\laragon\www\pos-kasir-tailadmin\database.sql | C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root
```

4. Buka:

```text
http://localhost/pos-kasir-tailadmin
```

## Konfigurasi database

Edit file `config/database.php` jika username, password, host, atau port MySQL berbeda.

Default:

```text
database: pos_kasir_tailadmin
username: root
password: kosong
host: 127.0.0.1
port: 3306
```

## Fitur

- Dashboard ringkasan penjualan, produk, transaksi, dan stok menipis.
- Kasir POS dengan pilihan produk, qty, subtotal, bayar, dan kembalian.
- Simpan transaksi serta otomatis mengurangi stok.
- CRUD kategori.
- CRUD produk.
- Tambah, edit, dan hapus foto produk.
- Mutasi stok masuk dan keluar.
- Riwayat transaksi dan struk cetak.

## Build TailAdmin

Jika mengubah class Tailwind di file PHP, jalankan ulang:

```powershell
npm install
npm run build
```
