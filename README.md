# Sistem CRUD Laravel 13

Sistem ini merupakan aplikasi CRUD sederhana yang dibangun menggunakan **Laravel 13**, **Livewire 4**, **Laravel Fortify**, dan **Tailwind CSS**.

Aplikasi ini dibuat untuk mengelola data melalui fitur dasar seperti tambah data, lihat data, edit data, dan hapus data. Sistem juga dilengkapi autentikasi pengguna menggunakan Laravel Fortify.

## Teknologi

* Laravel 13
* Livewire 4
* Laravel Fortify
* Tailwind CSS
* Vite
* MySQL / SQLite

## Fitur

* Login dan logout pengguna
* Proteksi halaman dengan autentikasi
* Tambah data
* Tampil data
* Edit data
* Hapus data
* Validasi form
* Tampilan responsif

## Instalasi

Clone project:

```bash
git clone https://github.com/username/nama-project.git
cd nama-project
```

Install dependency:

```bash
composer install
npm install
```

Salin file environment:

```bash
cp .env.example .env
```

Generate key:

```bash
php artisan key:generate
```

Atur database pada file `.env`, lalu jalankan migrasi:

```bash
php artisan migrate
```

Jalankan aplikasi:

```bash
php artisan serve
npm run dev
```

Akses aplikasi melalui browser:

```text
http://127.0.0.1:8000
```

## Keterangan

Project ini masih sederhana dan dapat dikembangkan lagi sesuai kebutuhan, seperti menambahkan role pengguna, pencarian data, upload file, export data, atau dashboard.
