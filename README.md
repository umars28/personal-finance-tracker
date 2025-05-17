🔐 1. Autentikasi & User Management
 Registrasi pengguna (/register)

Input: nama, email, password

 Login pengguna (/login)

Output: API token (Sanctum)

 Logout pengguna (/logout)

 Autentikasi middleware untuk endpoint yang butuh login

 Endpoint profil (/me) untuk cek data diri

💸 2. Manajemen Transaksi
 CRUD transaksi (/transactions)

Atribut:

amount (decimal)

type: income atau expense

category_id (relasi)

description

date (tanggal transaksi)

 Filter transaksi:

Berdasarkan type (income/expense)

Berdasarkan rentang tanggal (start_date & end_date)

Berdasarkan kategori

 Pagination list transaksi

🗂️ 3. Manajemen Kategori
 CRUD kategori (/categories)

Atribut:

name

type: income / expense (kategori income/pengeluaran)

 Relasi: transaksi → kategori

📊 4. Ringkasan dan Statistik
 Ringkasan bulanan (/summary/monthly)

Input: bulan & tahun (default bulan ini)

Output:

Total pemasukan

Total pengeluaran

Saldo akhir

Jumlah transaksi per kategori

 Caching hasil ringkasan dengan Redis

Key cache per user + bulan (contoh: summary:user:5:2025-05)

TTL cache misal 10 menit atau sampai transaksi berubah

📄 5. Swagger / OpenAPI Documentation
 Auto-generate dokumentasi endpoint dengan OpenAPI/Swagger

 Include semua parameter, response, dan status code

🧪 6. Unit Test & Feature Test
 Unit test untuk:

Repository transaksi

Summary service

 Feature test untuk:

Autentikasi

Transaksi (store, update, delete)

Ringkasan bulanan

⚙️ 7. Repository Pattern
 TransactionRepositoryInterface dan implementasinya

 CategoryRepository

 SummaryService untuk logic summary bulanan

🐳 8. Docker & Environment
 Docker Compose dengan 3 service:

Laravel app (php-fpm)

PostgreSQL

Redis

 .env file untuk konfigurasi

