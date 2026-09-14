# Sistem Peminjaman Alat

Aplikasi web untuk mengelola proses peminjaman dan pengembalian alat.

## Teknologi

- Laravel
- PHP
- MySQL
- Laravel Sanctum
- Docker
- Bootstrap

## Role Pengguna

Sistem memiliki beberapa role pengguna:

- **Admin** — mengelola data pengguna, kategori, dan alat.
- **Petugas** — mengelola proses peminjaman dan pengembalian serta laporan.
- **Peminjam** — melakukan peminjaman dan melihat data peminjaman.

## Fitur

- Login dan registrasi
- Autentikasi menggunakan Laravel Sanctum
- Manajemen pengguna
- Manajemen kategori alat
- Manajemen alat
- Peminjaman alat
- Pengembalian alat
- Laporan peminjaman
- Log aktivitas
- Manajemen profil pengguna

## Menjalankan Project

Pastikan Docker sudah berjalan.

Clone repository:

```bash
git clone https://github.com/Azura0728/peminjaman-alat.git

Masuk ke folder project

Jalankan container:

docker compose up -d

Masuk ke container Laravel:

docker compose exec app bash

Install dependency:

composer install

Salin file environment:

cp .env.example .env

Generate application key:

php artisan key:generate

Jalankan migration:

php artisan migrate

Setelah itu aplikasi dapat dijalankan sesuai konfigurasi Docker pada project.

Struktur Project

peminjaman-alat/
├── backend/            # Source code Laravel
├── public/             # File publik project
└── docker-compose.yml  # Konfigurasi Docker

Author

Azura Fatih Al Ansyori