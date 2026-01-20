@extends('layouts.app')

@section('title', 'Kasir Self-Service - Kantin Pintar')

@section('content')
    <div class="container-fluid pb-5 mb-5">

        <!-- 1. Category Tabs (Sticky) -->
        <div class="row mb-4 sticky-top bg-light py-2 shadow-sm rounded-bottom" style="z-index: 1020; top: 70px;">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill gap-2" id="categoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-lg fw-bold py-3" id="tab-makanan" data-bs-toggle="pill"
                            data-category="makanan" type="button">
                            🍽️ Makanan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-lg fw-bold py-3" id="tab-minuman" data-bs-toggle="pill"
                            data-category="minuman" type="button">
                            🥤 Minuman
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 2. Products Grid -->
        <div id="products-container" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <!-- Loading Skeleton (Placeholder) -->
            @for ($i = 0; $i < 4; $i++)
                <div class="col skeleton-loader">
                    <div class="card h-100 border-0 shadow-sm" aria-hidden="true">
                        <div class="card-img-top bg-secondary bg-opacity-10 placeholder-glow" style="height: 200px;"></div>
                        <div class="card-body">
                            <h5 class="card-title placeholder-glow">
                                <span class="placeholder col-6"></span>
                            </h5>
                            <p class="card-text placeholder-glow">
                                <span class="placeholder col-4"></span>
                            </p>
                            <a href="#" class="btn btn-primary disabled placeholder col-12"></a>
                        </div>
                    </div>
                </div>
            @endfor

            <!-- Products will be injected here via JS -->
        </div>

    </div>

    <!-- 3. Floating Cart Button -->
    <div class="fixed-bottom p-3 d-flex justify-content-end pointer-events-none">
        <button
            class="btn btn-primary btn-lg rounded-pill shadow-lg d-flex align-items-center gap-2 px-4 py-3 pointer-events-auto position-relative"
            data-bs-toggle="modal" data-bs-target="#cartModal" style="transform: scale(1.1);">
            <i class="bi bi-cart-fill fs-4"></i>
            <span class="fw-bold fs-5">Keranjang</span>
            <span id="cart-count"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light p-2 fs-6">
                0
            </span>
        </button>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- 4. Modal: Add To Cart & Toppings -->
    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold fs-4" id="modalProductName">Nama Produk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <img id="modalProductImage" src="" class="img-fluid rounded-3 shadow-sm w-100"
                                style="object-fit: cover; height: 300px;">
                            <h3 class="text-primary fw-bold mt-3" id="modalProductPrice">Rp 0</h3>
                            <p class="text-muted" id="modalProductDesc"></p>
                        </div>

                        <div class="col-md-7">
                            <!-- Toppings Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-stars text-warning me-2"></i>Tambah Topping?
                                    </h5>
                                    <div id="toppingsList" class="d-flex flex-column gap-2">
                                        <!-- Topping Items injected by JS -->
                                        <div class="text-center text-muted py-3">Memuat topping...</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity & Subtotal -->
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded border">
                                <div class="d-flex align-items-center gap-3">
                                    <button class="btn btn-outline-secondary btn-lg rounded-circle" id="qty-minus"
                                        style="width: 50px; height: 50px;">-</button>
                                    <span id="modalQty" class="fs-3 fw-bold mx-2">1</span>
                                    <button class="btn btn-outline-primary btn-lg rounded-circle" id="qty-plus"
                                        style="width: 50px; height: 50px;">+</button>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Subtotal</small>
                                    <span class="fs-3 fw-bold text-primary" id="modalSubtotal">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light">
                    <button type="button" class="btn btn-lg btn-secondary px-4 rounded-pill"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-add-to-cart"
                        class="btn btn-lg btn-success px-5 rounded-pill fw-bold flex-grow-1">
                        <i class="bi bi-cart-plus me-2"></i> Tambah Pesanan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Modal: Shopping Cart -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold fs-4"><i class="bi bi-basket me-2"></i>Keranjang Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="cartItemsList" class="list-group list-group-flush">
                        <!-- Cart Items injected by JS -->
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                            <p class="mt-3 text-muted fs-5">Keranjang masih kosong</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex flex-column align-items-stretch">
                    <!-- Customer Name Input -->
                    <div class="w-100 px-2 mb-3">
                        <label for="customerName" class="form-label fw-bold small text-muted text-uppercase">Nama
                            Pemesan</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="customerName"
                            placeholder="Contoh: Budi, Meja 5" autocomplete="off">
                    </div>

                    <div class="d-flex justify-content-between align-items-center w-100 mb-3 px-2">
                        <span class="fs-5">Total Pembayaran:</span>
                        <span id="cart-grand-total" class="fs-2 fw-bold text-primary">Rp 0</span>
                    </div>
                    <div class="d-flex w-100 gap-2">
                        <button type="button" class="btn btn-lg btn-outline-danger w-50" data-bs-dismiss="modal">Tambah Menu
                            Lain</button>
                        <button type="button" id="btn-checkout" class="btn btn-lg btn-primary w-50 fw-bold">
                            Pesan Sekarang <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Modal: Konfirmasi & Sukses -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 text-center p-4">
                <div class="modal-body">
                    <div class="mb-4 text-success">
                        <i class="bi bi-check-circle-fill display-1"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Pesanan Berhasil!</h2>
                    <p class="text-muted fs-5">Nomor Antrian Anda:</p>
                    <div class="display-3 fw-bold text-primary mb-4 bg-light py-3 rounded-3 border border-2 border-primary border-opacity-25"
                        id="success-order-number">
                        #---
                    </div>
                    <p class="text-muted mb-4">Mohon tunggu, pesanan Anda sedang diproses oleh dapur.</p>
                    <div class="progress mb-3" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" id="redirect-progress">
                        </div>
                    </div>
                    <small class="text-muted">Halaman akan refresh dalam <span id="countdown">5</span> detik...</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Toast -->
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 2000">
        <div id="liveToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fs-6 fw-medium">
                    <i class="bi bi-check-circle me-2"></i> <span id="toast-message">Item ditambahkan!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Large Touch Targets for Tablet */
            .btn-lg {
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
            }

            .form-check-input {
                width: 1.8em;
                height: 1.8em;
            }

            .form-check-label {
                font-size: 1.1rem;
                padding-left: 0.5rem;
                padding-top: 0.3rem;
            }

            /* Product Card */
            .product-card-hover {
                transition: transform 0.2s;
            }

            .product-card-hover:active {
                transform: scale(0.98);
            }

            /* Hide scrollbar but keep functionality */
            .modal-body::-webkit-scrollbar {
                width: 8px;
            }

            .modal-body::-webkit-scrollbar-thumb {
                background-color: rgba(0, 0, 0, 0.2);
                border-radius: 4px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('js/kasir.js') }}"></script>
        {{-- JS Logic will be implemented in the next step --}}
        <script>
            // Placeholder to prevent errors
            console.log('Kasir view loaded.');
        </script>
    @endpush

@endsection