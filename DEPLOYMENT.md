# 🚀 Deployment Guide - Laravel POS System

## 📋 Table of Contents
1. [Persiapan](#1-persiapan)
2. [Deployment ke Shared Hosting](#2-deployment-ke-shared-hosting)
3. [Deployment ke VPS](#3-deployment-ke-vps-optional)
4. [Post-Deployment Configuration](#4-post-deployment-configuration)
5. [Testing](#5-testing)
6. [Troubleshooting](#6-troubleshooting)
7. [Maintenance](#7-maintenance)
8. [Security Checklist](#8-security-checklist)
9. [Rollback Strategy](#9-rollback-strategy)

---

## 1. Persiapan

### 1.1 System Requirements

**Minimum Requirements (Shared Hosting):**
- PHP >= 8.1
- MySQL >= 5.7 atau MariaDB >= 10.3
- Apache Nginx web server
- SSL Certificate (HTTPS)
- cPanel access (recommended)

**PHP Extensions Required:**
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

### 1.2 Checklist Sebelum Deploy

- [ ] Project berjalan sempurna di local
- [ ] Semua tests passed
- [ ] Database seeder sudah ditest
- [ ] `.env.example` sudah lengkap
- [ ] `README.md` sudah update
- [ ] Backup code & database local
- [ ] Domain/subdomain sudah ready
- [ ] Hosting account sudah active

### 1.3 Files yang Perlu Disiapkan

1. **Project Files** - Seluruh project Laravel (zip)
2. **Database Backup** - Export `.sql` dari local
3. **.env Configuration** - Konfigurasi production
4. **Documentation** - README, API docs

---

## 2. Deployment ke Shared Hosting

### Step 1: Akses cPanel

1. Login ke cPanel hosting Anda
2. URL biasanya: `https://yourdomain.com/cpanel` atau `https://yourdomain.com:2083`
3. Masukkan username & password
   *(Screenshot placeholder: cPanel login page)*

### Step 2: Buat Database MySQL

**Via cPanel → MySQL Database Wizard:**

1. **Create Database**
   - Database Name: `username_laravel_pos`
   - Click "Next Step"
   *(Screenshot placeholder: Create database)*

2. **Create Database User**
   - Username: `username_posuser`
   - Password: Generate strong password (simpan!)
   - Password Strength: Very Strong
   - Click "Create User"
   *(Screenshot placeholder: Create user)*

3. **Add User to Database**
   - Select user yang baru dibuat
   - Privileges: **ALL PRIVILEGES**
   - Click "Next Step"
   *(Screenshot placeholder: Add privileges)*

4. **Catat Informasi Database:**
   - Database Name: `username_laravel_pos`
   - Database User: `username_posuser`
   - Database Password: `[your strong password]`
   - Database Host: `localhost` (usually)

### Step 3: Upload Project Files

**Option A: Via cPanel File Manager (Recommended untuk pemula)**

1. **Akses File Manager**
   - cPanel → File Manager
   - Navigate ke folder domain Anda:
     * Main domain: `public_html/`
     * Subdomain: `public_html/subdomain/`

2. **Upload ZIP File**
   - Click "Upload" button
   - Select project ZIP file
   - Wait until upload complete (cek progress bar)
   *(Screenshot placeholder: File upload progress)*

3. **Extract Files**
   - Right-click ZIP file → Extract
   - Extract to current directory
   - Delete ZIP file setelah extract
   *(Screenshot placeholder: Extract files)*

**Option B: Via FTP (FileZilla)**

1. **Install FileZilla Client**
   - Download: https://filezilla-project.org/

2. **Connect to Server**
   - Host: `ftp.yourdomain.com` atau IP server
   - Username: cPanel username
   - Password: cPanel password
   - Port: 21 (FTP) atau 22 (SFTP)
   - Click "Quickconnect"
   *(Screenshot placeholder: FileZilla connection)*

3. **Upload Files**
   - Left panel: Local files (project Anda)
   - Right panel: Remote server
   - Navigate remote ke `public_html/`
   - Drag & drop semua files dari local
   - Wait until transfer complete

### Step 4: Configure Public Directory

Laravel membutuhkan document root mengarah ke folder `public/`.

**Option A: Subdomain (Recommended)**

1. **Create Subdomain**
   - cPanel → Subdomains
   - Subdomain: `pos`
   - Domain: `yourdomain.com`
   - Document Root: `/public_html/laravel-pos/public`
   - Click "Create"
   *(Screenshot placeholder: Create subdomain)*

2. **Result:** `https://pos.yourdomain.com` akan point ke `/public`

**Option B: Main Domain (Advanced)**

1. **Move Public Contents**
   - Copy semua isi folder `public/` ke `public_html/`
   - Sisakan folder Laravel di `public_html/laravel-pos/`

2. **Edit index.php**
   - Open `public_html/index.php`
   - Update paths:
   ```php
   // Change from:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // Change to (adjust path as needed):
   require __DIR__.'/laravel-pos/vendor/autoload.php';
   $app = require_once __DIR__.'/laravel-pos/bootstrap/app.php';
   ```

### Step 5: Set File Permissions

**Via cPanel File Manager:**

1. **Set Permissions untuk Storage**
   - Navigate ke `storage/` folder
   - Select folder → Right-click → Change Permissions
   - Set to **755** (Recursive)
   - Check "Recurse into subdirectories"
   - Apply
   *(Screenshot placeholder: Change permissions dialog)*

2. **Set Permissions untuk Bootstrap/Cache**
   - Navigate ke `bootstrap/cache/`
   - Set to **755** (Recursive)

**Via SSH (jika tersedia):**
```bash
cd /home/username/public_html/laravel-pos
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

> **Permission Reference:**
> - 755 - Owner: Read/Write/Execute, Group & Others: Read/Execute
> - 644 - Owner: Read/Write, Group & Others: Read only

### Step 6: Configure Environment (.env)

1. **Rename .env.example**
   - File Manager → locate `.env.example`
   - Rename to `.env`

2. **Edit .env File**
   - Right-click `.env` → Edit
   - Update konfigurasi:

   ```env
   # Application Settings
   APP_NAME="Laravel POS - Kantin SMK"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://pos.yourdomain.com

   # Database Settings (gunakan info dari Step 2)
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=username_laravel_pos
   DB_USERNAME=username_posuser
   DB_PASSWORD=your_strong_password_here

   # Cache & Session (production)
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync

   # Logging
   LOG_CHANNEL=daily
   LOG_LEVEL=error

   # Custom POS Settings
   POS_POLLING_INTERVAL=3000
   POS_IDLE_TIMEOUT=30000
   POS_AUTO_REMOVE_DELAY=120000
   ```

3. **Save Changes**

> [!IMPORTANT]
> - `APP_DEBUG=false` untuk production (JANGAN true!)
> - `APP_ENV=production`
> - Password database harus match dengan Step 2
> - `APP_URL` harus match dengan domain Anda

### Step 7: Generate Application Key

**Via SSH:**
```bash
cd /home/username/public_html/laravel-pos
php artisan key:generate
```

**Tanpa SSH (Manual):**
1. **Generate Key Online**
   - Buka: https://generate-random.org/laravel-key-generator
   - Copy generated key (contoh: `base64:xxxxxxxxxxxxx`)
2. **Update .env**
   - Edit `.env`
   - Paste di `APP_KEY=base64:xxxxxxxxxxxxx`

### Step 8: Import Database

**Option A: Via phpMyAdmin (Recommended)**

1. **Access phpMyAdmin**
   - cPanel → phpMyAdmin
   - Select database `username_laravel_pos`
   *(Screenshot placeholder: phpMyAdmin interface)*

2. **Import SQL File**
   - Click "Import" tab
   - Click "Choose File"
   - Select your `.sql` backup file
   - Format: SQL
   - Click "Go"
   *(Screenshot placeholder: Import interface)*

3. **Wait for Success Message**
   - "Import has been successfully finished"

**Option B: Run Migrations (Fresh Database)**
Via SSH:
```bash
cd /home/username/public_html/laravel-pos
php artisan migrate --force
php artisan db:seed --force
```

> [!WARNING]
> `migrate:fresh` will DROP all tables! Use with caution.

### Step 9: Storage Link (Optional)

Jika menggunakan file uploads:

**Via SSH:**
```bash
php artisan storage:link
```

**Manual (Tanpa SSH):**
- Create symlink folder manually
- File Manager → `public/` folder
- Create folder `storage`
- Set as link to `../storage/app/public`

### Step 10: Optimize for Production

**Via SSH (Recommended):**
```bash
cd /home/username/public_html/laravel-pos

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

## 3. Deployment ke VPS (Optional)

**Prerequisites VPS:**
- Ubuntu 20.04/22.04 LTS (recommended)
- Root or sudo access
- Public IP address
- Domain pointing to IP

### 3.1 Server Setup

**Update System:**
```bash
sudo apt update && sudo apt upgrade -y
```

**Install LEMP Stack:**
```bash
# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install PHP 8.1+ & Extensions
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3.2 Deploy Application

**Clone atau Upload Project:**
```bash
cd /var/www
sudo git clone https://github.com/username/laravel-pos.git
# OR upload via SFTP
cd laravel-pos
```

**Install Dependencies:**
```bash
composer install --optimize-autoloader --no-dev
```

**Setup Environment:**
```bash
cp .env.example .env
nano .env  # Edit konfigurasi
php artisan key:generate
```

**Database Setup:**
```bash
# Create database
sudo mysql -u root -p
CREATE DATABASE laravel_pos;
CREATE USER 'posuser'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON laravel_pos.* TO 'posuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Migrate
php artisan migrate --seed --force
```

**Permissions:**
```bash
sudo chown -R www-data:www-data /var/www/laravel-pos
sudo chmod -R 755 /var/www/laravel-pos/storage
sudo chmod -R 755 /var/www/laravel-pos/bootstrap/cache
```

### 3.3 Nginx Configuration

**Create config file:**
```bash
sudo nano /etc/nginx/sites-available/laravel-pos
```

**Config content:**
```nginx
server {
    listen 80;
    server_name pos.yourdomain.com;
    root /var/www/laravel-pos/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Enable site:**
```bash
sudo ln -s /etc/nginx/sites-available/laravel-pos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 3.4 SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d pos.yourdomain.com
# Follow prompts, pilih redirect HTTP to HTTPS
```

---

## 4. Post-Deployment Configuration

### 4.1 Verify Installation

**Check Application:**
- Visit: `https://pos.yourdomain.com`
- Should see landing page
- No errors configuration warnings

**Check Routes (Via SSH):**
```bash
php artisan route:list
```

### 4.2 Test Functionality

1. **Test Kasir Page:**
   - Visit: `https://pos.yourdomain.com/kasir`
   - Check products load
   - Submit order

2. **Test Dapur Page:**
   - Visit: `https://pos.yourdomain.com/dapur`
   - Check orders appear
   - Try real-time status updates

3. **Test API Endpoints:**
   ```bash
   curl https://pos.yourdomain.com/api/products
   ```

---

## 6. Troubleshooting

### Issue 1: Error 500 - Internal Server Error
**Symptoms:** White page or "500 Internal Server Error"
**Solutions:**
1. Check credentials in `.env`
2. Fix permissions: `chmod -R 755 storage`
3. Check logs: `storage/logs/laravel.log`

### Issue 2: Error 404 - Page Not Found
**Symptoms:** Routes return 404 (except home)
**Solutions:**
1. Ensure `.htaccess` exists in `public/`
2. Enable `mod_rewrite` if on Apache
3. Verify document root points to `public/`

### Issue 3: Assets Not Loading (CSS/JS)
**Symptoms:** Broken layout, styles missing
**Solutions:**
1. Check `APP_URL` in `.env`
2. Ensure `asset()` helper is used in Blade files
3. Clear browser cache

### Issue 4: Database Connection Error
**Symptoms:** "Access denied for user"
**Solutions:**
1. Shared hosting `DB_HOST` usually `localhost`, not `127.0.0.1`
2. Verify username has prefix (e.g. `cpaneluser_dbuser`)
3. Reset DB password

### Issue 5: Pesanan Tidak Muncul di Dapur
**Symptoms:** Order created but not showing on Kitchen Display
**Solutions:**
1. Check browser console (F12) for JS errors
2. Check if polling (AJAX) is working in Network tab
3. Verify server time matches local time zone

---

## 7. Maintenance

### 7.1 Regular Backup
**Database:**
- Daily automated backup via cPanel
- OR Cron job script: `mysqldump ...`

**Files:**
- Weekly full account backup

### 7.2 Update Laravel
```bash
composer update
php artisan migrate
php artisan config:cache
```

### 7.3 Monitor Logs
```bash
tail -100 storage/logs/laravel.log
```

---

## 8. Security Checklist

> [!CAUTION]
> Failure to follow these steps can compromise your application.

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production` set
- [ ] Strong database password (16+ chars)
- [ ] Unique `APP_KEY` generated
- [ ] File permissions correct (755 directories, 644 files)
- [ ] `.env` NOT accessible via browser
- [ ] HTTPS (SSL) enabled and forced
- [ ] Database user has minimal privileges (NO root)
- [ ] Disable directory listing (`Options -Indexes`)

---

## 9. Rollback Strategy

**Quick Rollback:**
1. **Restore Database:** `mysql -u user -p db_name < backup.sql`
2. **Restore Files:** Upload previous version backup
3. **Clear Cache:** `php artisan cache:clear`

---

*Last updated: January 2026*
