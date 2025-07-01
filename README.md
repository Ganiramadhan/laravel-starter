
# Laravel REST API Starter Kit

Starter kit ini adalah backend REST API yang dibangun menggunakan **Laravel 12** dan **PostgreSQL**, dilengkapi dengan autentikasi **JWT**, proteksi menggunakan **middleware**, dan dokumentasi API otomatis menggunakan **Swagger (L5-Swagger)**. Cocok digunakan sebagai pondasi awal untuk membangun sistem e-commerce, manajemen data, dan aplikasi modern lainnya.

## ✨ Fitur Utama

- ✅ Autentikasi dengan **JWT** (`tymon/jwt-auth`)
- 🔐 Middleware Laravel untuk proteksi route
- 📘 Dokumentasi API otomatis dengan **Swagger (L5-Swagger)**
- 📦 Struktur folder RESTful yang bersih
- 🧪 Siap untuk integrasi testing dan pengembangan lanjutan

---

## 📦 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/Ganiramadhan/backend-dagangbareng.git
cd backend-dagangbareng
```

### 2. Install Dependency

```bash
composer install
```

### 3. Salin File .env

```bash
cp .env.example .env
```

### 4. Konfigurasi Database di `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ganipedia
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Generate Key dan JWT Secret

```bash
php artisan key:generate
php artisan jwt:secret
```

### 6. Jalankan Migrasi

```bash
php artisan migrate
```

(Optional)
```bash
php artisan db:seed
```

### 7. Jalankan Server Lokal

```bash
php artisan serve
```

---

## 🔐 Autentikasi JWT

Gunakan endpoint berikut:

- `POST /api/register` — Register user
- `POST /api/login` — Login dan dapatkan token
- Header: `Authorization: Bearer {token}` untuk akses endpoint yang dilindungi

---

## 📘 Dokumentasi API (Swagger)

### Instalasi (Jika belum)

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Generate dokumentasi

```bash
php artisan l5-swagger:generate
```

### Akses Swagger UI

```
http://127.0.0.1:8000/api/documentation
```

---

## 📁 Struktur Proyek

- `app/Http/Controllers/` — Controller untuk API
- `routes/api.php` — Routing untuk REST API
- `app/Models/` — Model Eloquent
- `app/Http/Middleware/` — Middleware kustom dan auth
- `database/migrations/` — Skema database

---

## ❓ Troubleshooting

- Pastikan `.env` sudah sesuai
- Jalankan `php artisan config:clear` & `php artisan cache:clear` jika ada error konfigurasi
- Swagger kosong? Pastikan sudah `php artisan l5-swagger:generate`

---

## 📄 Lisensi

Proyek ini open-source dan dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
