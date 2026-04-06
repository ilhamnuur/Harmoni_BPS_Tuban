# Cara Menjalankan Aplikasi Harmoni BPS Tuban di Ubuntu

## Persyaratan Sistem

- Ubuntu 20.04/22.04/24.04
- PHP 8.1+
- Composer
- Node.js & npm
- MySQL/MariaDB
- Web Server (Apache/Nginx) atau Laravel Artisan

---

## Langkah 1: Install Dependencies

Buka terminal dan jalankan:

```bash
sudo apt update
sudo apt install -y php-cli php-curl php-gd php-mbstring php-xml php-xmlrpc php-soap php-intl php-zip php-mysql composer nodejs npm
```

## Langkah 2: Clone/Extract Project

Jika menggunakan Git:
```bash
git clone <repository-url> Harmoni_BPS_Tuban
cd Harmoni_BPS_Tuban
```

## Langkah 3: Konfigurasi Environment

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME="Harmoni BPS Tuban"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=harmoni_bps_tuban
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

Generate application key:
```bash
php artisan key:generate
```

## Langkah 4: Setup Database

Login ke MySQL:
```bash
mysql -u root -p
```

Buat database:
```sql
CREATE DATABASE harmoni_bps_tuban;
EXIT;
```

## Langkah 5: Install PHP Dependencies

```bash
composer install
```

## Langkah 6: Install Node.js Dependencies & Build Assets

```bash
npm install
npm run dev
```

## Langkah 7: Run Database Migrations

```bash
php artisan migrate
```

Jika ingin seeding data awal:
```bash
php artisan db:seed
```

## Langkah 8: Jalankan Aplikasi

### Opsi A: Menggunakan Laravel Artisan (Development)
```bash
php artisan serve
```

Akses di browser: http://localhost:8000

### Opsi B: Menggunakan Apache/Nginx (Production)

#### Apache:
```bash
sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/harmoni.conf
sudo nano /etc/apache2/sites-available/harmoni.conf
```

Konfigurasi VirtualHost:
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/Harmoni_BPS_Tuban/public
    
    <Directory /var/www/html/Harmoni_BPS_Tuban/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Aktifkan site dan rewrite module:
```bash
sudo a2enmod rewrite
sudo a2ensite harmoni.conf
sudo systemctl restart apache2
```

#### Nginx:
```bash
sudo nano /etc/nginx/sites-available/harmoni
```

Konfigurasi:
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/Harmoni_BPS_Tuban/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

Aktifkan site:
```bash
sudo ln -s /etc/nginx/sites-available/harmoni /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

---

## Troubleshooting

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/html/Harmoni_BPS_Tuban
sudo chmod -R 755 /var/www/html/Harmoni_BPS_Tuban/storage
sudo chmod -R 755 /var/www/html/Harmoni_BPS_Tuban/bootstrap/cache
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Jika Menggunakan Shared Hosting

Upload semua file ke public_html, pindahkan isi folder `public` ke root:
- Upload semua file KE folder `public_html`
- Pindahkan `.env` ke `public_html`
- Edit `public/index.php` untuk menyesuaikan path

---

## Akun Default (Setelah Seeding)

Cek file `database/seeders/DatabaseSeeder.php` untuk detail akun default.