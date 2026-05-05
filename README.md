# Manajemen Kategori Buku — UTS Perpustakaan

> Aplikasi CRUD sederhana untuk mengelola kategori buku perpustakaan, dibangun dengan PHP Native + MySQL + Bootstrap 5.

---

## Identitas Mahasiswa

| | |
|---|---|
| **Nama** | Candra Sya'bana Putra Gunadi |
| **NIM** | 60324042 |
| **Mata Kuliah/Kelas** | Pemrograman Web-II / A |

---

## Deskripsi Aplikasi

Aplikasi ini merupakan sistem manajemen kategori buku untuk perpustakaan sederhana. Fitur yang tersedia:

- **Melihat** daftar seluruh kategori buku
- **Menambah** kategori baru dengan validasi lengkap
- **Mengedit** data kategori yang sudah ada
- **Menghapus** kategori dengan konfirmasi

Setiap kategori memiliki atribut: kode unik (format `KAT-XXX`), nama, deskripsi, dan status (Aktif / Nonaktif).

**Teknologi yang digunakan:**
- PHP 8.x (Native, tanpa framework)
- MySQL / MariaDB
- Bootstrap 5.3 + Bootstrap Icons
- Prepared Statements (MySQLi) untuk keamanan query

---

## Cara Instalasi dan Menjalankan

### Prasyarat

Pastikan sudah terinstal salah satu dari:
- **XAMPP** (Windows/Linux/macOS) — [Download](https://www.apachefriends.org/)
- **Laragon** (Windows) — [Download](https://laragon.org/)
- **MAMP** (macOS) — [Download](https://www.mamp.info/)

### Langkah Instalasi

**1. Clone repository**

```bash
git clone https://github.com/CANDRA2006/uts-pemrograman-web-2-60324042
```
Atau download ZIP lalu ekstrak.

**2. Pindahkan ke folder web server**
```
# XAMPP
C:/xampp/htdocs/uts-perpustakaan/

# Laragon
C:/laragon/www/uts-perpustakaan/

# Linux/macOS (XAMPP)
/opt/lampp/htdocs/uts-perpustakaan/
```

**3. Buat database**

Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), lalu:
- Klik **Import**
- Pilih file `setup.sql` dari foder project
- Klik **Go**

Atau jalankan via terminal:
```bash
mysql -u root -p < setup.sql
```

**4. Sesuaikan konfigurasi database** *(jika diperlukan)*

Buka file `config/database.php`:
```php
define('DB_SERVER',   'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');           // Sesuaikan password MySQL Anda
define('DB_NAME',     'uts_perpustakaan_60324042');
```

**5. Jalankan aplikasi**

Buka browser dan akses:
```
http://localhost/uts-pemrograman-web-2-60324042/index.php
```

---

## Struktur Folder

```
uts-perpustakaan/
│
├── config/
│   └── database.php        # Konfigurasi & koneksi database
│
├── index.php               # Halaman utama — daftar semua kategori
├── create.php              # Form tambah kategori baru
├── edit.php                # Form edit kategori (menerima ?id=)
├── delete.php              # Proses hapus kategori (menerima ?id=)
│
├── setup.sql               # Script SQL: buat database, tabel, dan data awal
│
└── README.md               # Dokumentasi proyek ini
```

---

## Link Repository GitHub

```
https://github.com/CANDRA2006/uts-pemrograman-web-2-60324042
```
