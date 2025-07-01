
# Backend DagangBareng

**DagangBareng** adalah backend API untuk platform e-commerce sederhana yang membantu pelaku UMKM membuka toko online mereka sendiri. Backend ini dibangun menggunakan **Laravel 12** dan **PostgreSQL**, serta dilengkapi dengan dokumentasi API menggunakan **Swagger (L5-Swagger)**.

## 🔗 Repository
Repository ini berada di:
[https://github.com/Ganiramadhan/backend-dagangbareng.git](https://github.com/Ganiramadhan/backend-dagangbareng.git)

## ⚙️ Persyaratan Sistem

- PHP >= 8.2
- Composer
- PostgreSQL
- Laravel CLI (opsional tapi disarankan)

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan backend secara lokal:

### 1. Clone Repository

```bash
git clone https://github.com/Ganiramadhan/backend-dagangbareng.git
cd backend-dagangbareng
```

### 2. Install Dependency

```bash
composer install
```

### 3. Konfigurasi .env

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan bagian database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_database_anda
```

> ⚠️ Pastikan database PostgreSQL dengan nama yang sesuai sudah dibuat.

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migrasi

```bash
php artisan migrate
```

Jika ada seeder:

```bash
php artisan db:seed
```

### 6. Jalankan Server

```bash
php artisan serve
```

Akses API melalui: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 📘 Dokumentasi API (Swagger)

### Instalasi Swagger

```bash
composer require "darkaonline/l5-swagger"
```

Publikasi konfigurasinya:

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Generate Dokumentasi

```bash
php artisan l5-swagger:generate
```

### Akses Swagger UI

```
http://127.0.0.1:8000/api/documentation
```

## 📁 Struktur Penting

- `app/Http/Controllers/` — Controller untuk API (dengan anotasi Swagger)
- `routes/api.php` — Routing untuk API
- `database/migrations/` — Struktur database
- `app/Models/` — Model Eloquent Laravel

## ❓ Troubleshooting

- **Koneksi database gagal**: Pastikan PostgreSQL berjalan dan konfigurasi `.env` benar.
- **Swagger tidak muncul**: Jalankan `php artisan l5-swagger:generate` dan cek anotasi Swagger di controller.

## 📄 Lisensi

Proyek ini open-source dan dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
