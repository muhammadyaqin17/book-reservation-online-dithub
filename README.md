# 📚 Book Reservation System

Sistem reservasi buku berbasis web menggunakan Laravel 11, PostgreSQL, Redis, dan Docker. Aplikasi ini memungkinkan pengguna untuk melihat katalog buku, melakukan reservasi, dan mengelola antrian peminjaman.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-blue?logo=postgresql)
![Redis](https://img.shields.io/badge/Redis-7-red?logo=redis)
![Docker](https://img.shields.io/badge/Docker-Ready-blue?logo=docker)

## ✨ Fitur Utama

### Untuk Pengguna (Member)
- 📖 Lihat katalog buku dengan filter dan pencarian
- 🔍 Cek ketersediaan buku secara real-time
- 📝 Buat reservasi buku
- 📊 Sistem antrian otomatis jika buku tidak tersedia
- 📧 Notifikasi email untuk update status reservasi
- 📋 Lihat riwayat dan status reservasi

### Untuk Admin
- 📊 Dashboard dengan statistik dan peringatan
- 📚 Kelola buku (tambah, edit, hapus)
- 📁 Kelola kategori buku
- 👥 Kelola pengguna
- ✅ Proses reservasi (setujui, pinjam, kembalikan, batalkan)
- 🔔 Notifikasi untuk buku yang terlambat dikembalikan

## 🛠️ Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Database:** PostgreSQL 15
- **Cache & Queue:** Redis 7
- **Frontend:** Bootstrap 5, Bootstrap Icons
- **Container:** Docker & Docker Compose
- **Email:** Mailtrap (development)

## 📋 Prasyarat

Pastikan Anda sudah menginstall:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) atau Docker + Docker Compose (Linux)
- [Git](https://git-scm.com/downloads)

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/book-reservation.git
cd book-reservation
```

### 2. Copy Environment File

```bash
# Windows (Command Prompt)
copy .env.example .env

# Windows (PowerShell) / Linux / Mac
cp .env.example .env
```

### 3. Konfigurasi Environment (Opsional)

Buka file `.env` dan sesuaikan jika diperlukan:

```env
# Untuk email notification (Mailtrap)
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

> 💡 **Tips:** Daftar gratis di [Mailtrap.io](https://mailtrap.io) untuk testing email.

### 4. Jalankan Docker

```bash
docker-compose up -d --build
```

Tunggu beberapa menit sampai semua container berjalan. Anda bisa cek status dengan:

```bash
docker-compose ps
```

### 5. Install Dependencies & Setup Database

```bash
# Install Composer dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Jalankan migrasi database
docker-compose exec app php artisan migrate

# Isi data contoh (opsional tapi disarankan)
docker-compose exec app php artisan db:seed
```

### 6. Buat Storage Link

```bash
docker-compose exec app php artisan storage:link
```

### 7. Akses Aplikasi

🎉 Aplikasi sekarang bisa diakses di: **http://localhost:8000**

## 🔐 Akun Default

Setelah menjalankan `db:seed`, Anda bisa login dengan akun berikut:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bookreservation.com | password |
| Member | member@bookreservation.com | password |

## 📁 Struktur Folder

```
book-reservation/
├── app/
│   ├── Http/Controllers/     # Controller aplikasi
│   ├── Models/               # Eloquent models
│   ├── Jobs/                 # Queue jobs
│   └── Mail/                 # Mailable classes
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── docker/                   # Docker configuration
│   ├── nginx/                # Nginx config
│   └── php/                  # PHP config
├── resources/views/          # Blade templates
│   ├── admin/                # Admin panel views
│   ├── books/                # Book catalog views
│   ├── emails/               # Email templates
│   └── layouts/              # Layout templates
├── routes/
│   └── web.php               # Web routes
├── tests/                    # PHPUnit tests
│   ├── Feature/              # Feature tests
│   └── Unit/                 # Unit tests
├── docker-compose.yml        # Docker compose config
├── Dockerfile                # Docker image config
└── phpunit.xml               # PHPUnit config
```

## 🧪 Menjalankan Tests

```bash
# Jalankan semua tests
docker-compose exec app php artisan test

# Jalankan test tertentu
docker-compose exec app php artisan test --filter=AuthenticationTest

# Dengan coverage report
docker-compose exec app php artisan test --coverage
```

## 📧 Email Queue Worker

Untuk mengirim email notifikasi secara asynchronous:

```bash
docker-compose exec app php artisan queue:work
```

> **Catatan:** Queue worker sudah berjalan otomatis sebagai service terpisah di Docker.

## 🔧 Perintah Berguna

```bash
# Melihat log aplikasi
docker-compose logs -f app

# Masuk ke container app
docker-compose exec app bash

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Reset database (HATI-HATI: menghapus semua data!)
docker-compose exec app php artisan migrate:fresh --seed

# Stop semua container
docker-compose down

# Stop dan hapus volumes (reset database)
docker-compose down -v
```

## 🌐 URL Endpoints

| URL | Deskripsi |
|-----|-----------|
| `/` | Homepage |
| `/books` | Katalog Buku |
| `/books/{slug}` | Detail Buku |
| `/login` | Halaman Login |
| `/register` | Halaman Registrasi |
| `/reservations/my` | Reservasi Saya (perlu login) |
| `/admin` | Admin Dashboard (perlu login admin) |

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan buat Pull Request atau buka Issue jika menemukan bug.

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 📞 Kontak

Jika ada pertanyaan, silakan buka Issue di repository ini.

---

Made with ❤️ using Laravel 11
