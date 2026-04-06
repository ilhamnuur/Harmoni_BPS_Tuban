# Panduan Menjalankan Docker Harmoni BPS Tuban

Aplikasi ini telah dikonfigurasi untuk berjalan di Docker menggunakan `docker-compose`.

## Struktur Container
1. **app**: Menjalankan Laravel PHP 8.2-FPM.
2. **web**: Server Nginx (Port host: 8080).
3. **db**: Basis data MySQL 8.0 (Port host: 33060).
4. **redis**: Cache dan Queue untuk Laravel.
5. **worker**: Queue worker untuk menjalankan job di background secara otomatis.

## Cara Menjalankan

1.  **Persiapan file .env**
    Salin `.env.example` menjadi `.env` jika belum ada:
    ```bash
    cp .env.example .env
    ```

2.  **Sesuaikan Konfigurasi Database di .env**
    Pastikan `.env` Anda menggunakan nama host sesuai service di Docker:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=harmoni_bps_tuban
    DB_USERNAME=root
    DB_PASSWORD=secret

    REDIS_HOST=redis
    ```

3.  **Build dan Jalankan Docker Compose**
    ```bash
    docker-compose up -d --build
    ```

4.  **Akses Aplikasi**
    Buka di browser: `http://localhost:8080`

## Perintah Penting

- **Cek Status Container:**
  ```bash
  docker-compose ps
  ```

- **Melihat Log:**
  ```bash
  docker-compose logs -f app
  ```

- **Menjalankan Artisan di dalam Docker:**
  ```bash
  docker-compose exec app php artisan <printah>
  ```

- **Reset / Hentikan:**
  ```bash
  docker-compose down
  ```

## Catatan Tambahan
- Script `docker/entrypoint.sh` akan otomatis menjalankan `php artisan key:generate` (jika key kosong) dan `php artisan migrate --force` setiap kali container dijalankan.
- Data database tersimpan secara persisten di volume docker bernama `dbdata`.
