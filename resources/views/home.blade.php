@extends('layouts.app')

@section('title', 'Beranda - Kantin Pintar SMK Nusantara')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden mb-5">
        <div class="container h-100">
            <div class="row align-items-center h-100 py-5">
                <div class="col-lg-6 text-white z-2">
                    <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill fw-semibold">
                        👋 Selamat Datang Siswa & Guru!
                    </span>
                    <h1 class="display-3 fw-bold mb-3 lh-sm">
                        Kantin Pintar <br>
                        <span class="text-warning">SMK Nusantara</span>
                    </h1>
                    <p class="lead mb-4 text-white-50">
                        Pesan makanan dan minuman favoritmu lebih cepat, tanpa perlu antri panjang.
                        Nikmati pengalaman jajan digital yang modern! ⚡
                    </p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('kasir.index') }}"
                            class="btn btn-light btn-lg text-primary fw-bold px-4 shadow-sm">
                            <i class="bi bi-cart-plus me-2"></i> Mulai Pesan
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4">
                            Pelajari Cara Pesan
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block z-1">
                    <!-- Hero Image / Illustration Placeholder -->
                    <img src="https://source.unsplash.com/random/600x600/?lunch,food" alt="Delicious Food"
                        class="img-fluid rounded-4 shadow-lg hero-image"
                        onerror="this.src='https://placehold.co/600x600/FF6B35/FFFFFF?text=Delicious+Food'">
                </div>
            </div>
        </div>

        <!-- Decorative Blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 mb-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-2">Kenapa Pesan di Sini?</h2>
                <p class="text-muted">Teknologi modern untuk kenyamanan istirahatmu</p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                            <i class="bi bi-tablet fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Pesan Sendiri</h4>
                        <p class="text-muted mb-0">
                            Pilih menu favoritmu langsung dari tablet kasir self-service.
                            Tinggal klik-klik, bayar, dan tunggu pesananmu.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center p-4">
                        <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3">
                            <i class="bi bi-fire fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Dapur Real-time</h4>
                        <p class="text-muted mb-0">
                            Pesananmu langsung masuk ke layar dapur.
                            Chefs kami langsung siapkan makananmu selagi hangat.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center p-4">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                            <i class="bi bi-stars fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Topping Suka-suka</h4>
                        <p class="text-muted mb-0">
                            Mau extra keju? Atau tambah telur?
                            Sesuaikan makananmu dengan berbagai pilihan topping lezat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Menu Preview -->
    <section class="py-5 bg-white mb-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Menu Favorit 🔥</h2>
                    <p class="text-muted mb-0">Paling banyak dipesan teman-temanmu</p>
                </div>
                <a href="{{ route('kasir.index') }}" class="btn btn-outline-primary rounded-pill">
                    Lihat Semua Menu <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                <!-- Dummy Product 1 -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <div class="position-relative">
                            <span class="badge bg-warning position-absolute top-0 end-0 m-3">Best Seller</span>
                            <img src="https://source.unsplash.com/random/300x200/?friedrice" class="card-img-top"
                                alt="Nasi Goreng" onerror="this.src='https://placehold.co/300x200?text=Product'">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">Nasi Goreng Spesial</h5>
                            <p class="card-text text-muted small mb-3">Dengan telur mata sapi dan kerupuk udang.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">Rp 15.000</span>
                                <span class="badge bg-light text-dark border">Makanan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dummy Product 2 -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <div class="position-relative">
                            <img src="https://source.unsplash.com/random/300x200/?noodle" class="card-img-top"
                                alt="Mie Ayam" onerror="this.src='https://placehold.co/300x200?text=Product'">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">Mie Ayam Bakso</h5>
                            <p class="card-text text-muted small mb-3">Mie kenyal dengan topping ayam dan bakso sapi.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">Rp 12.000</span>
                                <span class="badge bg-light text-dark border">Makanan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dummy Product 3 -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <div class="position-relative">
                            <span class="badge bg-success position-absolute top-0 end-0 m-3">Baru</span>
                            <img src="https://source.unsplash.com/random/300x200/?icedtea" class="card-img-top"
                                alt="Es Teh Manis" onerror="this.src='https://placehold.co/300x200?text=Product'">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">Es Teh Jumbo</h5>
                            <p class="card-text text-muted small mb-3">Teh manis dingin ukuran jumbo segar.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">Rp 5.000</span>
                                <span class="badge bg-light text-dark border">Minuman</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dummy Product 4 -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <div class="position-relative">
                            <img src="https://source.unsplash.com/random/300x200/?coffee" class="card-img-top"
                                alt="Kopi Susu" onerror="this.src='https://placehold.co/300x200?text=Product'">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">Kopi Susu Gula Aren</h5>
                            <p class="card-text text-muted small mb-3">Kopi susu kekinian dengan gula aren asli.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">Rp 10.000</span>
                                <span class="badge bg-light text-dark border">Minuman</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3 display-6">Lapar? Jangan Tunggu Lama!</h2>
                    <p class="lead mb-4 text-white-50">
                        Rasakan kemudahan memesan makanan di kantin sekolah.
                        Tinggal klik, bayar, dan ambil pesananmu.
                    </p>
                    <a href="{{ route('kasir.index') }}"
                        class="btn btn-light btn-lg text-primary fw-bold px-5 shadow-lg rounded-pill transform-hover">
                        Pesan Sekarang 🚀
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            /* Hero Styling */
            .hero-section {
                background: linear-gradient(135deg, var(--primary-color) 0%, #FF9F1C 100%);
                min-height: 600px;
                display: flex;
                align-items: center;
            }

            /* Interactive Blobs */
            .blob {
                position: absolute;
                filter: blur(50px);
                z-index: 0;
                opacity: 0.4;
                animation: blob-bounce 10s infinite ease;
            }

            .blob-1 {
                top: -10%;
                left: -10%;
                width: 400px;
                height: 400px;
                background: #7bdcb5;
                border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            }

            .blob-2 {
                bottom: -10%;
                right: -10%;
                width: 500px;
                height: 500px;
                background: #2D9CDB;
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
                animation-delay: 2s;
            }

            @keyframes blob-bounce {

                0%,
                100% {
                    transform: translate(0, 0) rotate(0deg);
                }

                33% {
                    transform: translate(30px, -50px) rotate(10deg);
                }

                66% {
                    transform: translate(-20px, 20px) rotate(-5deg);
                }
            }

            /* Feature Cards */
            .feature-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
            }

            .icon-box {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Product Cards */
            .product-card {
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
            }

            .product-card img {
                height: 200px;
                object-fit: cover;
                transition: transform 0.5s ease;
            }

            .product-card:hover img {
                transform: scale(1.05);
            }

            /* CTA Button Hover */
            .transform-hover {
                transition: transform 0.2s ease;
            }

            .transform-hover:hover {
                transform: scale(1.05);
            }

            /* Hero Image */
            .hero-image {
                transform: rotate(-3deg);
                border: 10px solid rgba(255, 255, 255, 0.2);
                transition: transform 0.5s ease;
            }

            .hero-image:hover {
                transform: rotate(0deg) scale(1.02);
            }
        </style>
    @endpush
@endsection