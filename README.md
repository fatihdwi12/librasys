# LibraSys

LibraSys adalah aplikasi manajemen perpustakaan yang dibuat untuk technical test Fullstack Developer. Aplikasi menggunakan Laravel REST API, MySQL, JWT Authentication, serta frontend Blade, Bootstrap, dan Vanilla JavaScript.

## Fitur

- Registrasi dan login menggunakan JWT.
- Hak akses admin dan member.
- Pengelolaan kategori, buku, dan anggota.
- Pencatatan peminjaman buku oleh admin.
- Pengembalian buku dan pembaruan stok.
- Pembuatan akun login untuk anggota yang sudah terdaftar.
- Perubahan kata sandi dan reset kata sandi anggota oleh admin.
- Daftar peminjaman pribadi melalui akun member.
- Pencarian, filter kategori, dan pagination pada API buku.

Peminjaman dan pengembalian menggunakan database transaction. Setiap peminjaman yang berhasil mengurangi stok satu eksemplar, sedangkan pengembalian menambah stok satu eksemplar. Pengembalian berulang pada loan yang sama ditolak.

## Teknologi

- Laravel 12
- PHP 8.2 — versi lingkungan pengembangan: 8.2.12
- MySQL
- Composer
- JWT: `php-open-source-saver/jwt-auth`
- Blade
- Bootstrap 5
- Vanilla JavaScript dan Fetch API

Dependency yang digunakan proyek tercantum pada `composer.json` dan `composer.lock`.

## Kebutuhan

- PHP dan ekstensi sesuai kebutuhan `composer.json`.
- Composer.
- MySQL yang sudah berjalan.
- Git.
- Browser modern.

Proyek dikembangkan pada Windows menggunakan Laragon dan Git Bash.

Frontend menggunakan Bootstrap, ikon, dan font melalui CDN. Akses internet diperlukan untuk memuat aset CDN tersebut.

## Menjalankan Aplikasi

### 1. Clone repository

```bash
git clone <https://github.com/fatihdwi12/librasys.git>
cd librasys
```

Jika nama folder hasil clone berbeda, sesuaikan perintah `cd`.

### 2. Install dependency

```bash
composer install
```

Gunakan `composer install`, bukan `composer update`, untuk mengikuti versi dependency pada lock file.

### 3. Buat file environment lokal

```bash
cp .env.example .env
```

Perintah tersebut dapat digunakan melalui Git Bash. Alternatifnya, salin `.env.example` menjadi `.env` secara manual.

### 4. Buat database MySQL

Buat database kosong melalui HeidiSQL, phpMyAdmin, atau MySQL client:

```sql
CREATE DATABASE librasys
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Sesuaikan konfigurasi `.env` dengan MySQL lokal:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=librasys
DB_USERNAME=root
DB_PASSWORD=
```

Username dan password di atas adalah contoh lingkungan Laragon lokal.

### 5. Generate application key dan JWT secret

```bash
php artisan key:generate
php artisan jwt:secret
php artisan config:clear
```

Application key dan JWT secret dibuat pada lingkungan masing-masing. Jangan menggunakan secret milik lingkungan lain.

### 6. Jalankan migration dan seeder

Pada database kosong:

```bash
php artisan migrate --seed
```

Seeder menyediakan data demo sesuai implementasi pada `database/seeders`.

### 7. Jalankan server

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

Jika port berbeda, gunakan alamat yang ditampilkan oleh Artisan.

## Akun Demo Admin

```text
Email: admin@librasys.test
Password: password
```

Akun ini hanya untuk demonstrasi lokal. Pastikan kredensial sesuai dengan seeder yang disertakan.

Admin dapat membuat akun untuk anggota yang sudah ada melalui fitur Buat Akun Login. Proses tersebut menghubungkan User ke record Member yang sama, bukan membuat anggota kedua.

## Alternatif Setup Database: SQL Dump

SQL dump demo disertakan pada:

```text
database/sql/librasys_demo.sql
```

Gunakan salah satu metode setup database:

1. Migration dan Seeder — direkomendasikan.
2. Import SQL dump.

Jangan menjalankan seeder lagi setelah import dump lengkap, kecuali memang diperlukan dan seeder sudah dibuat idempotent.

Untuk import:

1. Install dependency dan buat `.env`.
2. Siapkan database kosong.
3. Sesuaikan konfigurasi database.
4. Import dump.
5. Generate application key dan JWT secret.
6. Jalankan aplikasi.

Contoh import melalui Git Bash, untuk MySQL lokal dengan user root tanpa password:

```bash
mysql.exe -h 127.0.0.1 -P 3306 -u root librasys < database/sql/librasys_demo.sql
```

Jika MySQL menggunakan password, tambahkan `-p` dan masukkan password saat diminta.

Setelah import:

```bash
php artisan key:generate
php artisan jwt:secret
php artisan config:clear
php artisan migrate:status
php artisan serve
```

Dump lengkap mencakup tabel `migrations`. Periksa status migration sebelum menjalankan perubahan skema berikutnya.

## REST API

Base URL:

```text
http://127.0.0.1:8000/api
```

Header request:

```http
Accept: application/json
Content-Type: application/json
```

Untuk endpoint yang membutuhkan autentikasi:

```http
Authorization: Bearer <JWT_TOKEN>
```

### Authentication

| Method | Endpoint           | Akses  | Keterangan                    |
| ------ | ------------------ | ------ | ----------------------------- |
| POST   | `/register`        | Publik | Registrasi akun member baru   |
| POST   | `/login`           | Publik | Login dan mendapatkan JWT     |
| GET    | `/me`              | Login  | Profil akun dan relasi member |
| POST   | `/logout`          | Login  | Logout                        |
| PUT    | `/change-password` | Login  | Mengubah kata sandi akun      |

JWT login tersedia pada:

```text
data.token.access_token
```

Registrasi dengan email anggota yang sudah ada ditolak. Anggota existing yang belum memiliki akun diaktivasi oleh admin.

### Categories

| Method    | Endpoint                 | Akses        |
| --------- | ------------------------ | ------------ |
| GET       | `/categories`            | Admin/member |
| GET       | `/categories/{category}` | Admin/member |
| POST      | `/categories`            | Admin        |
| PUT/PATCH | `/categories/{category}` | Admin        |
| DELETE    | `/categories/{category}` | Admin        |

### Books

| Method    | Endpoint        | Akses        |
| --------- | --------------- | ------------ |
| GET       | `/books`        | Admin/member |
| GET       | `/books/{book}` | Admin/member |
| POST      | `/books`        | Admin        |
| PUT/PATCH | `/books/{book}` | Admin        |
| DELETE    | `/books/{book}` | Admin        |

Contoh query daftar buku:

```text
GET /api/books?search=laravel
GET /api/books?category_id=1
GET /api/books?per_page=10&page=1
```

### Members

| Method    | Endpoint                     | Akses | Keterangan                       |
| --------- | ---------------------------- | ----- | -------------------------------- |
| GET       | `/members`                   | Admin | Daftar anggota                   |
| GET       | `/members/{member}`          | Admin | Detail anggota                   |
| POST      | `/members`                   | Admin | Tambah anggota                   |
| PUT/PATCH | `/members/{member}`          | Admin | Ubah anggota                     |
| DELETE    | `/members/{member}`          | Admin | Hapus anggota                    |
| POST      | `/members/{member}/account`  | Admin | Buat akun untuk anggota existing |
| PUT       | `/members/{member}/password` | Admin | Reset password anggota           |

Anggota yang memiliki riwayat loan tidak dapat dihapus.

Contoh body pembuatan akun:

```json
{
    "admin_password": "<KATA_SANDI_ADMIN>",
    "password": "<KATA_SANDI_ANGGOTA_15_64_KARAKTER>",
    "password_confirmation": "<KATA_SANDI_ANGGOTA_15_64_KARAKTER>"
}
```

### Loans

| Method    | Endpoint               | Akses        | Keterangan                                     |
| --------- | ---------------------- | ------------ | ---------------------------------------------- |
| GET       | `/loans`               | Admin        | Daftar peminjaman                              |
| GET       | `/loans/{loan}`        | Admin        | Detail peminjaman                              |
| POST      | `/loans`               | Admin        | Catat peminjaman                               |
| PUT/PATCH | `/loans/{loan}`        | Admin        | Ubah sesuai aturan UpdateLoanRequest           |
| DELETE    | `/loans/{loan}`        | Admin        | Hapus loan yang sudah dikembalikan             |
| PUT       | `/loans/{loan}/return` | Admin        | Catat pengembalian                             |
| GET       | `/my-loans`            | Admin/member | Peminjaman milik member yang terhubung ke akun |

Filter loan aktif:

```text
GET /api/loans?status=borrowed
```

Contoh body peminjaman:

```json
{
    "member_id": 1,
    "book_id": 3,
    "due_at": "YYYY-MM-DD"
}
```

Ganti ID dengan data yang tersedia dan gunakan tanggal jatuh tempo yang valid.

### Aturan Peminjaman

- Member harus aktif.
- Buku harus memiliki stok lebih dari nol.
- Satu loan mewakili satu eksemplar buku.
- Status awal loan adalah `borrowed`.
- Pembuatan loan dan pengurangan stok berada dalam satu transaksi.
- Buku dikunci saat proses pemeriksaan dan pengurangan stok.

### Aturan Pengembalian

- Loan harus berstatus `borrowed`.
- Pengembalian mengisi `returned_at`.
- Status berubah menjadi `returned`.
- Stok bertambah satu.
- Pengembalian kedua terhadap loan yang sama ditolak.

### Status HTTP

| Status | Penggunaan                                         |
| ------ | -------------------------------------------------- |
| 200    | Pengambilan, pembaruan, atau pengembalian berhasil |
| 201    | Data atau akun berhasil dibuat                     |
| 401    | Token tidak valid atau autentikasi gagal           |
| 403    | Tidak memiliki hak akses                           |
| 404    | Data tidak ditemukan                               |
| 409    | Konflik kondisi data                               |
| 422    | Validasi atau aturan peminjaman gagal              |
| 429    | Batas request terlampaui                           |

Respons collection buku menggunakan metadata pagination. Jangan menghitung total katalog hanya dari panjang array pada satu halaman.

## Struktur Utama

```text
app/Http/Controllers/Api   Controller REST API
app/Http/Requests          Validasi request
app/Http/Resources         Penyajian data API
app/Models                Model dan relasi Eloquent
app/Services/LoanService   Proses peminjaman dan pengembalian
database/migrations       Skema database
database/seeders          Data demo
resources/views           Halaman Blade
public/js/api.js          JWT dan helper Fetch API
routes/api.php            Route API
routes/web.php            Route halaman
```

## Relasi Data

```text
User     1 ─── 0..1 Member
Category 1 ─── *    Book
Member   1 ─── *    Loan
Book     1 ─── *    Loan
```

Foreign key utama:

- `members.user_id` → `users.id`
- `books.category_id` → `categories.id`
- `loans.member_id` → `members.id`
- `loans.book_id` → `books.id`

`members.user_id` unik agar satu akun tidak terhubung ke beberapa record anggota.

## Pengujian

Jalankan automated tests yang tersedia:

```bash
php artisan test
```

Cakupan aktual mengikuti file pada folder `tests`. Jangan menganggap semua skenario berikut sudah memiliki automated test tanpa memeriksa implementasinya.

Checklist pengujian manual:

- Login admin.
- Request protected endpoint tanpa JWT ditolak.
- CRUD kategori, buku, dan anggota.
- Registrasi duplikat ditolak.
- Buat akun anggota existing tanpa membuat Member baru.
- Buat loan dan pastikan stok berkurang satu.
- Login member dan periksa `/api/my-loans`.
- Kembalikan buku dan pastikan stok bertambah satu.
- Pengembalian berulang ditolak.
- Search, filter, dan pagination buku.

## Keamanan Repository

Tidak disertakan:

- `.env`
- `vendor/`
- JWT secret atau application key asli.
- Password database pribadi.
- Token aktif.
- Data pribadi nyata pada SQL dump.

`.env.example` hanya berisi referensi konfigurasi tanpa secret.

## Catatan

- Akun demo hanya digunakan pada lingkungan lokal.
- Dashboard mengambil ulang data saat halaman dimuat atau tombol muat ulang digunakan; bukan push real-time.
- Total judul buku berbeda dari jumlah eksemplar tersedia.
- Jangan menggunakan `migrate:fresh` pada database yang datanya ingin dipertahankan.

## Entity Relationship Diagram

![ERD LibraSys](docs/diagrams/erd.png)

## Relasi Tabel

![Relasi Tabel LibraSys](docs/diagrams/table-relations.png)

## Diagram Arsitektur

![Arsitektur LibraSys](docs/diagrams/architecture.png)
