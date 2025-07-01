
# Laravel REST API Starter Kit

This starter kit is a backend REST API built with **Laravel 12** and **PostgreSQL**, featuring **JWT authentication**, route protection using **middleware**, and automated API documentation with **Swagger (L5-Swagger)**. It is ideal as a foundation for building e-commerce systems, data management platforms, or other modern backend applications.

## ✨ Features

- ✅ **JWT** authentication (`tymon/jwt-auth`)
- 🔐 Route protection with Laravel **middleware**
- 📘 Auto-generated API documentation using **Swagger (L5-Swagger)**
- 📦 Clean and modular **RESTful API** structure
- 🧪 Ready for testing and further development

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Ganiramadhan/backend-dagangbareng.git
cd backend-dagangbareng
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Copy the .env File

```bash
cp .env.example .env
```

### 4. Configure Database in `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ganipedia
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Generate App Key and JWT Secret

```bash
php artisan key:generate
php artisan jwt:secret
```

### 6. Run Migrations

```bash
php artisan migrate
```

(Optional)
```bash
php artisan db:seed
```

### 7. Start the Local Server

```bash
php artisan serve
```

---

## 🔐 JWT Authentication

Use the following endpoints:

- `POST /api/register` — Register a new user
- `POST /api/login` — Authenticate and receive a token
- Use the header: `Authorization: Bearer {token}` to access protected routes

---

## 📘 API Documentation (Swagger)

### Install Swagger (if not yet installed)

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Generate API Docs

```bash
php artisan l5-swagger:generate
```

### Access Swagger UI

```
http://127.0.0.1:8000/api/documentation
```

---

## 📁 Project Structure

- `app/Http/Controllers/` — API controllers
- `routes/api.php` — RESTful routes
- `app/Models/` — Eloquent models
- `app/Http/Middleware/` — Custom and auth middleware
- `database/migrations/` — Database schema

---

## ❓ Troubleshooting

- Make sure `.env` is correctly configured
- Run `php artisan config:clear` and `php artisan cache:clear` if you face config issues
- Blank Swagger? Ensure you've run `php artisan l5-swagger:generate`

---

## 📄 License

This project is open-source and licensed under the [MIT License](https://opensource.org/licenses/MIT).
