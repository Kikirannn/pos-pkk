# 📖 Buku Panduan Penggunaan POS PKK

Selamat datang di panduan penggunaan aplikasi POS PKK. Dokumen ini menjelaskan cara menggunakan setiap fitur aplikasi untuk berbagai peran pengguna.

---

## 👥 Peran Pengguna
Pilih panduan sesuai peran Anda:
1.  [👨‍💼 Pelanggan (Landing Page & Kasir)](#1-panduan-pelanggan-landing-page--kasir)
2.  [👨‍🍳 Staff Dapur (Kitchen Display)](#2-panduan-staff-dapur-kitchen-display-system)
3.  [📦 Admin (Manajemen Inventori)](#3-panduan-admin-manajemen-inventori)

---

## 1. Panduan Pelanggan (Landing Page & Kasir)
**Akses Halaman:** `http://localhost:8000/`

Halaman depan (Landing Page) sudah terintegrasi langsung dengan mesin kasir. Anda tidak perlu berpindah halaman.

### Alur Pemesanan:
1.  **Layar Sambutan (Hero):**
    *   Klik tombol **"Mulai Belanja"**.
    *   Layar akan otomatis menggulir ke bawah menuju bagian Menu.

2.  **Pilih Menu:**
    *   Gunakan tombol kategori **Makanan** atau **Minuman** untuk memfilter.
    *   **Cek Stok:**
        *   🟢 **Stok Tersedia:** Muncul badge hijau "Stok: 10".
        *   🔴 **Stok Habis:** Kartu produk berwarna abu-abu, ada badge "Habis", dan tombol tidak bisa diklik.

3.  **Tambah ke Keranjang:**
    *   Klik tombol **"Tambah"**.
    *   **Untuk Menu dengan Topping (misal: Mie Ayam):**
        *   Akan muncul jendela (modal).
        *   Pilih Topping (Ceker, Bakso, dll).
        *   Tentukan jumlah (Maksimal sesuai sisa stok).
        *   Klik "Simpan ke Keranjang".
    *   **Untuk Menu Biasa:** Langsung masuk ke keranjang.

4.  **Kelola Keranjang (Sidebar Kanan):**
    *   Lihat panel di sebelah kanan layar (atau di bawah pada mode mobile).
    *   Klik tombol **Sampah (Merah)** untuk menghapus item.
    *   Total harga akan terhitung otomatis.

5.  **Checkout:**
    *   Masukkan **Nama Pelanggan** pada kolom yang tersedia di sidebar keranjang.
    *   Klik tombol **"Proses Pesanan"**.
    *   Tunggu notifikasi sukses dan catat **Nomor Antrian** Anda.
    *   Sistem akan otomatis me-refresh halaman ke tampilan awal dalam 5 detik.

---

## 2. Panduan Staff Dapur (Kitchen Display System)
**Akses Halaman:** `http://localhost:8000/dapur`

### Cara Kerja:
Halaman ini dirancang untuk **Tanpa Sentuh (Hands-free)** yang sering di-refresh. Data akan masuk otomatis (Real-time polling).

### Status Pesanan:
1.  **⬜ New (Baru Masuk):**
    *   Kartu berwarna putih.
    *   Tanda pesanan baru saja dibuat pelanggan.
    *   **Tindakan:** Segera siapkan bahan. Klik tombol **"Proses" (Kuning)** jika mulai memasak.

2.  **🟨 Processing (Sedang Dimasak):**
    *   Kartu berwarna kuning.
    *   Menandakan pesanan sedang dibuat.
    *   **Tindakan:** Jika makanan sudah matang dan siap diambil, klik tombol **"Selesai" (Hijau)**.

3.  **🟩 Done (Selesai):**
    *   Kartu berwarna hijau.
    *   Pesanan sudah siap.
    *   Kartu akan otomatis menghilang dari layar setelah beberapa saat untuk membersihkan tampilan.

---

## 3. Panduan Admin (Manajemen Inventori)
**Akses Halaman:** `http://localhost:8000/inventory`

Halaman ini digunakan untuk mengontrol apa yang tampil di Landing Page / Kasir.

### Mengelola Produk (Menu):
1.  **Menambah Produk:**
    *   Klik tombol **"+ Tambah Menu"**.
    *   Isi Nama, Kategori, Harga.
    *   **PENTING: Isi Stok.** Masukkan jumlah porsi yang tersedia (misal: 50).
    *   Upload gambar menarik.
    *   Klik Simpan.

2.  **Mengupdate Stok:**
    *   Jika stok fisik bertambah atau berkurang (rusak/dimakan sendiri), edit produk tersebut.
    *   Ubah angka di kolom **Stok**.
    *   **Indikator:** Jika angka stok berwarna merah, berarti stok sudah menipis (di bawah 5).

3.  **Menghapus Produk:**
    *   Klik tombol sampah untuk menghapus menu selamanya.

### Mengelola Topping:
*   Pilih tab **"Topping"** di bagian atas.
*   Topping tidak memiliki stok (selalu dianggap tersedia kecuali dinonaktifkan).
*   Pastikan kategori topping benar (Topping Makanan vs Minuman).

---

## ❓ FAQ & Masalah Umum

### Q: Kenapa saya tidak bisa klik tombol "Tambah"?
**A:** Cek badge di gambar produk. Jika tertulis "Habis" atau "Stok: 0", sistem otomatis mengunci tombol tersebut agar Anda tidak memesan barang kosong.

### Q: Bagaimana jika pelanggan memesan lebih banyak dari stok?
**A:** Sistem akan menolak. Misal stok tinggal 2, dan di keranjang sudah ada 2, Anda tidak bisa menambah lagi. Akan muncul peringatan modal error.

### Q: Apakah halaman dapur perlu di-refresh manual?
**A:** Tidak perlu. Halaman dapur akan mengecek pesanan baru setiap 10 detik secara otomatis.

---

*Dokumen ini diperbarui terakhir pada: Januari 2026*
