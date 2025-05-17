# 📘 Personal Finance Tracker API

API sederhana untuk mencatat pemasukan dan pengeluaran harian dengan Laravel, PostgreSQL, Redis, Swagger, Docker, dan Repository Pattern.

---

## 🔐 1. Autentikasi & User Management

- **Registrasi**: `POST /register`
  - Input: `name`, `email`, `password`
- **Login**: `POST /login`
  - Output: API Token (Laravel Sanctum)
- **Logout**: `POST /logout`
- **Profil**: `GET /me`
  - Mendapatkan data pengguna saat ini
- **Proteksi Autentikasi**: Middleware digunakan untuk semua endpoint pribadi

---

## 💸 2. Manajemen Transaksi

- **CRUD Transaksi**: `GET|POST|PUT|DELETE /transactions`
  - Atribut:
    - `amount` (decimal)
    - `type` (`income` atau `expense`)
    - `category_id`
    - `description`
    - `date` (format: `YYYY-MM-DD`)
- **Filter Transaksi**:
  - Berdasarkan `type`
  - Berdasarkan rentang tanggal (`start_date` & `end_date`)
  - Berdasarkan `category_id`
- **Pagination**: Mendukung paginasi default Laravel

---

## 🗂️ 3. Manajemen Kategori

- **CRUD Kategori**: `GET|POST|PUT|DELETE /categories`
  - Atribut:
    - `name`
    - `type`: `income` atau `expense`
- **Relasi**: Setiap transaksi terkait dengan satu kategori

---

## 📊 4. Ringkasan & Statistik

- **Ringkasan Bulanan**: `GET /summary/monthly`
  - Input: `month` & `year` (opsional, default: bulan ini)
  - Output:
    - Total pemasukan
    - Total pengeluaran
    - Saldo akhir
    - Jumlah transaksi per kategori
- **Caching dengan Redis**:
  - Key: `summary:user:{id}:{year-month}`
  - TTL: 10 menit atau invalidasi saat data berubah

---

## 📄 5. Swagger / OpenAPI Documentation

- Otomatis generate dokumentasi menggunakan Swagger
- Menampilkan:
  - Semua endpoint
  - Parameter & request body
  - Response format
  - Status code

---

## 🧪 6. Unit Test & Feature Test

- **Unit Test**:
  - Repository transaksi
  - Summary service
- **Feature Test**:
  - Autentikasi
  - Transaksi (store, update, delete)
  - Ringkasan bulanan

---

## ⚙️ 7. Repository Pattern

- `TransactionRepositoryInterface` + implementasi
- `CategoryRepository`
- `SummaryService` untuk perhitungan ringkasan

---

## 🐳 8. Docker & Environment

- **Docker Compose** terdiri dari:
  - Laravel App (`php-fpm`)
  - PostgreSQL
  - Redis
- **File `.env`** untuk konfigurasi environment

---

## 📎 Teknologi

- Laravel 9
- PostgreSQL
- Redis
- Docker
- Swagger (OpenAPI)
- Laravel Sanctum
- PHPUnit

---

## 🛠️ Instalasi & Menjalankan

```bash
git clone <repo-url>
cd <folder>
cp .env.example .env
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
