# 🍽️ POS PKK - Sistem Kasir & Manajemen Kantin Modern

**POS PKK** adalah aplikasi Point of Sale (POS) berbasis web yang dirancang khusus untuk modernisasi operasional kantin sekolah atau bazaar kelas. Aplikasi ini mengintegrasikan pemesanan mandiri (self-service), manajemen dapur (kitchen display), dan pengelolaan stok inventaris dalam satu ekosistem yang efisien.

---

## 📋 Daftar Isi
1. [Latar Belakang & Tujuan](#-latar-belakang--tujuan)
2. [Fitur Unggulan](#-fitur-unggulan)
3. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
4. [Struktur Database](#-struktur-database)
5. [Instalasi & Konfigurasi](#-instalasi--konfigurasi)
6. [Dokumentasi Penggunaan](#-dokumentasi-penggunaan)

---

## 🎯 Latar Belakang & Tujuan
Project ini dibuat untuk menyelesaikan masalah antrian panjang dan kesalahan pencatatan pesanan yang sering terjadi di kantin sekolah atau event bazaar.

**Solusi yang ditawarkan:**
*   **Self-Service Ordering:** Pelanggan memesan sendiri melalui tablet/kiosk yang terintegrasi langsung di Landing Page.
*   **Real-Time Kitchen Display:** Pesanan langsung muncul di layar dapur, menghilangkan penggunaan kertas bon.
*   **Inventory Tracking (Stok):** Stok berkurang otomatis saat pesanan masuk, mencegah penjualan produk yang sudah habis.

---

## ✨ Fitur Unggulan

### 1. 📱 Landing Page & Kasir (Pelanggan)
Aplikasi ini menggunakan konsep **Single Page Application** untuk sisi pelanggan.
*   **Hero Section:** Sambutan menarik dengan tombol "Mulai Belanja" yang otomatis menggulir ke bagian menu.
*   **Menu & Pemesanan:** Terletak di halaman yang sama (scroll ke bawah).
    *   **Kategori & Pencarian:** Filter mudah untuk Makanan dan Minuman.
    *   **Manajemen Stok Cerdas:** Badge stok visual & auto-disable jika habis.
    *   **Custom Topping:** Modal popup untuk memilih varian rasa/topping.
    *   **Keranjang Belanja:** Sidebar yang selalu terlihat (sticky) di desktop untuk review pesanan cepat.

### 2. 👨‍🍳 Kitchen Display System (Dapur)
*   **Pemantauan Pesanan:** Tampilan kartu pesanan yang masuk secara real-time (polling otomatis).
*   **Status Tracking:** Alur kerja jelas: `Baru` → `Diproses` → `Selesai`.
*   **Prioritas Visual:** Pesanan baru ditandai dengan warna mencolok dan notifikasi suara.

### 3. 📦 Manajemen Inventori (Admin)
*   **Dashboard CRUD:** Tambah, edit, dan hapus menu serta topping.
*   **Kontrol Stok:**
    *   Input jumlah stok saat tambah/edit produk.
    *   **Peringatan Stok Menipis:** Angka stok berubah warna merah jika sisa ≤ 5 porsi.
*   **Manajemen Aset:** Upload foto produk dan pengaturan harga fleksibel.

---

## 🛠️ Teknologi yang Digunakan

### Backend
*   **Framework:** Laravel 10.x
*   **Bahasa:** PHP 8.2+
*   **Database:** MySQL 8.0

### Frontend
*   **CSS Framework:** Tailwind CSS (v3.x via CDN & Config)
*   **Icons:** Bootstrap Icons
*   **Scripting:** jQuery (DOM Manipulation), AJAX (Data Fetching)
*   **Animation:** GSAP (GreenSock Animation Platform)

---

## 🗃️ Struktur Database

### Tabel Utama:
1.  **`products`**: `id`, `name`, `category`, `price`, `stock`, `image`, `description`, `is_available`
2.  **`toppings`**: `id`, `name`, `category`, `price`, `is_available`
3.  **`orders`**: `id`, `order_number`, `customer_name`, `total_price`, `status`, `created_at`
4.  **`order_items`**: Detail item per transaksi.
5.  **`order_item_toppings`**: Pivot table untuk topping per item.

---

## 🚀 Instalasi & Konfigurasi

### Prasyarat
*   PHP >= 8.1
*   Composer
*   MySQL

### Langkah Instalasi
1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/pos-pkk.git
    cd pos-pkk
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Setup Environment**
    *   Copy file `.env.example` menjadi `.env`
    *   Sesuaikan konfigurasi database (DB_DATABASE, DB_USERNAME, dll).

4.  **Generate Key**
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi & Seeder Database**
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Link Storage (untuk gambar produk)**
    ```bash
    php artisan storage:link
    ```

7.  **Jalankan Server**
    ```bash
    php artisan serve
    ```
    Akses di: `http://localhost:8000`

---

## 📚 Dokumentasi Penggunaan
Panduan lengkap cara menggunakan aplikasi untuk Pelanggan, Staff Dapur, dan Admin dapat dilihat di file:

👉 **[BUKU_PANDUAN.md](BUKU_PANDUAN.md)**

---

*Dibuat dengan ❤️ oleh Tim Pengembang POS PKK*
