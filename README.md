<p align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</a>
</p>

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12-red">
<img src="https://img.shields.io/badge/PHP-8.2-blue">
<img src="https://img.shields.io/badge/License-MIT-green">
</p>

# Harmoni BPS Tuban

Aplikasi **Harmoni BPS Tuban** adalah sistem manajemen kegiatan rapat dan dinas luar pegawai yang dibangun menggunakan **Laravel Framework**.

Fitur utama:

* Manajemen data pegawai
* Manajemen kegiatan rapat
* Manajemen dinas luar
* Export data ke Excel
* Export laporan ke PDF
* Sistem login multi role (Admin, Katim, Pegawai)

---

# ⚙️ Tech Stack

* Laravel 12
* PHP 8.2+
* MySQL
* Laravel Excel
* DomPDF

---

# 📥 Cara Clone dan Menjalankan Project

Ikuti langkah berikut untuk menjalankan project di komputer lokal.

---

## 1️⃣ Clone Repository

```bash
git clone https://github.com/MohammadIsma11/Harmoni_BPS_Tuban.git
cd Harmoni_BPS_Tuban
```

---

## 2️⃣ Install Dependency Laravel

Pastikan sudah menginstall:

* PHP 8.2+
* Composer
* MySQL
* Node.js (opsional)

Install dependency Laravel:

```bash
composer install
```

---

## 3️⃣ Copy File Environment

Linux / Mac:

```bash
cp .env.example .env
```

Windows:

```bash
copy .env.example .env
```

---

## 4️⃣ Generate Application Key

```bash
php artisan key:generate
```

---

## 5️⃣ Konfigurasi Database

Buka file `.env` lalu ubah konfigurasi database:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=harmoni
DB_USERNAME=postgres
DB_PASSWORD=(password postgres mu)
```

Buat database baru di MySQL dengan nama:

```
harmoni_bps
```

---

## 6️⃣ Jalankan Migration Database

```bash
php artisan migrate
```

Jika ada seeder:

```bash
php artisan db:seed
```

---

## 7️⃣ Jalankan Server Laravel

```bash
php artisan serve
```

Aplikasi akan berjalan di:

```
http://127.0.0.1:8000
```

---

## 8️⃣ Install Frontend Dependencies (Jika diperlukan)

```bash
npm install
npm run dev
```

---

# 🔑 Akun Login

### Admin

```
username : admin
password : password123
```

### kepala

```
username : kepala.bps
password : password123
```

### Katim

```
username : dua nama depan masing masing katim
password : password123
```

### Pegawai

```
username : dua nama depan masing masing pegawai
password : password123
```

---

# 📁 Struktur Folder Penting

```
app/                → Controller & Model
routes/web.php      → Routing aplikasi
resources/views     → Blade template
database/migrations → Struktur tabel database
storage/            → Cache, session, file upload
```

---

# 📦 Package yang Digunakan

* laravel/framework
* barryvdh/laravel-dompdf
* maatwebsite/excel

---

# 👨‍💻 Author

Muhammad Ismail Putra
Universitas Islam Sunan Ampel Surabaya

---

# 📜 License

The Laravel framework is open-sourced software licensed under the MIT license.
