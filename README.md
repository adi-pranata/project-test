# Aplikasi Pengaduan Masyarakat - Kabupaten Badung

Sistem layanan pengaduan masyarakat berbasis Laravel.

## ✅ Fitur Utama

-   Registrasi & Login User
-   Pengajuan layanan (izin, laporan, dsb.)
-   Upload dokumen pendukung
-   Tracking status pengaduan
-   Verifikasi & Tindak lanjut oleh Admin
-   Notifikasi via email

---

## 🔧 Persyaratan

-   PHP >= 8.4
-   Composer
-   MySQL / MariaDB

---

## 🚀 Cara Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/adi-pranata/project-test.git
cd project-test

2. Install Dependency Laravel
composer install

3. Copy File Environment
cp .env.example .env

4. Generate Key Aplikasi
php artisan key:generate

5. Atur Koneksi Database di .env
env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengaduan
DB_USERNAME=root
DB_PASSWORD=

6. Jalankan Migrasi & Seeder
php artisan migrate --seed

7. Jalankan Laravel Server
php artisan serve

Buka di browser:
http://127.0.0.1:8000
```
