@extends('layouts.app')

@section('title', 'Bazar XII RPL 2 - POS System')

@section('content')

<!-- SECTION 1: HERO / WELCOME -->
<section id="hero" class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-mono-white via-mono-off-white to-mono-light">
    
    <!-- Floating Images (3 Items) -->
    <!-- Image 1: Top Left (Zig-zag/Geometric) -->
    <div class="absolute top-10 left-[-5%] md:left-10 w-48 h-48 md:w-64 md:h-64 z-0 hero-img-1 hidden md:block">
        <div class="w-full h-full bg-white p-2 shadow-lg" style="clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%);">
            <img src="{{ asset('images/Basreng.jpg') }}" class="w-full h-full object-cover" alt="Food 1">
        </div>
    </div>

    <!-- Image 2: Bottom Left (Wavy/Organic) -->
    <div class="absolute bottom-20 left-[-10%] md:left-20 w-56 h-56 md:w-72 md:h-72 z-0 hero-img-2 hidden md:block">
        <div class="w-full h-full bg-white p-2 shadow-lg" style="border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;">
            <img src="{{ asset('images/Lemon Tea.jpg') }}" class="w-full h-full object-cover rounded-[inherit]" alt="Food 2">
        </div>
    </div>

    <!-- Image 3: Right (Star/Flower) -->
    <div class="absolute top-1/2 right-[-15%] md:right-16 transform -translate-y-1/2 w-48 h-48 md:w-72 md:h-72 z-0 hero-img-3 hidden md:block">
        <div class="w-full h-full bg-white p-2 shadow-lg" style="clip-path: polygon(50% 0%, 83% 12%, 100% 43%, 94% 78%, 68% 100%, 32% 100%, 6% 78%, 0% 43%, 17% 12%);">
            <img src="{{ asset('images/Mie Ayam.jpg') }}" class="w-full h-full object-cover" alt="Food 3">
        </div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 py-16 relative z-10 text-center">
        <div class="hero-content max-w-4xl mx-auto">
            
            <!-- Doodle/Scribble -->
            <div class="mb-4 flex justify-center">
                <svg width="60" height="60" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-mono-black animate-spin-slow">
                    <path d="M50 10C30 10 10 30 10 50C10 70 30 90 50 90C70 90 90 70 90 50C90 30 70 10 50 10Z" stroke="currentColor" stroke-width="4" stroke-dasharray="10 10"/>
                    <path d="M50 30C40 30 30 40 30 50C30 60 40 70 50 70C60 70 70 60 70 50C70 40 60 30 50 30Z" stroke="currentColor" stroke-width="4"/>
                </svg>
            </div>

            <!-- Main Heading -->
            <h1 class="font-display text-5xl md:text-7xl lg:text-8xl font-bold text-mono-black mb-6 leading-tight tracking-tight uppercase">
                Bazar<br>
                XII <span class="italic">RPL 2</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-lg md:text-xl text-mono-black font-medium max-w-xl mx-auto mb-10 leading-relaxed">
                Nikmati hidangan lezat dengan harga bersahabat. <br class="hidden md:block">
                Pesan sekarang dan rasakan bedanya.
            </p>

            <!-- CTA Button -->
            <div class="flex justify-center">
                <a href="#" id="btn-start-shopping" class="inline-flex items-center bg-primary text-mono-black px-8 py-4 rounded-full font-bold tracking-wider hover:bg-primary-hover transition-transform transform hover:scale-105 shadow-xl group">
                    <i class="bi bi-bag-fill mr-3 group-hover:animate-bounce"></i>
                    MULAI BELANJA
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2: ABOUT -->
<section id="about" class="min-h-screen flex items-center py-20 bg-mono-off-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center gap-12 max-w-6xl mx-auto">
            <div class="w-full md:w-1/2">
                <img src="{{ asset('images/Tentang.jpeg') }}" alt="Suasana Kantin" class="rounded-2xl shadow-mono-xl w-full h-[400px] object-cover">
            </div>
            <div class="w-full md:w-1/2">
                <span class="text-primary font-bold tracking-wider uppercase mb-2 block">Tentang Kami</span>
                <h2 class="font-display text-4xl md:text-5xl font-bold text-mono-black mb-6">Definisi Murah<br>Tapi Gak Murahan.</h2>
                <p class="text-mono-gray text-lg leading-relaxed mb-6">
                    Selamat datang di Bazar Kelas XII RPL 2 Kami menyajikan berbagai hidangan lezat yang dimasak dengan penuh cinta. 
                    Menggabungkan cita rasa otentik dengan kemudahan teknologi modern untuk pengalaman bersantap yang tak terlupakan.
                </p>
                <div class="flex gap-8">
                    <div>
                        <h4 class="font-display text-3xl font-bold text-primary">100%</h4>
                        <p class="text-sm text-mono-gray">Halal</p>
                    </div>
                    <div>
                        <h4 class="font-display text-3xl font-bold text-primary">3</h4>
                        <p class="text-sm text-mono-gray">Menu Pilihan</p>
                    </div>
                    <div>
                        <h4 class="font-display text-3xl font-bold text-primary">Fresh</h4>
                        <p class="text-sm text-mono-gray">Ingredients</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 3: HOW IT WORKS -->
<section id="how-it-works" class="min-h-screen flex items-center py-20 bg-mono-white">
    <div class="container mx-auto px-4 w-full">
        <div class="text-center mb-16">
            <span class="text-primary font-bold tracking-wider uppercase mb-2 block">Panduan</span>
            <h2 class="font-display text-4xl md:text-5xl font-bold text-mono-black">Cara Pemesanan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-8 max-w-6xl mx-auto relative">
            <!-- Connecting Line (Desktop) -->
            <div class="hidden md:block absolute top-12 left-0 w-full h-1 bg-mono-light -z-10"></div>

            <!-- Step 1 -->
            <div class="text-center relative bg-mono-white p-4">
                <div class="w-24 h-24 bg-primary text-mono-black rounded-full flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg border-4 border-mono-white relative z-10">
                    <i class="bi bi-phone-vibrate"></i>
                </div>
                <h4 class="font-bold text-xl mb-2">1. Pesan</h4>
                <p class="text-sm text-mono-gray">Pilih menu favorit Anda melalui layar sentuh.</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center relative bg-mono-white p-4">
                <div class="w-24 h-24 bg-secondary text-mono-black rounded-full flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg border-4 border-mono-white relative z-10">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <h4 class="font-bold text-xl mb-2">2. Antri</h4>
                <p class="text-sm text-mono-gray">Pesanan masuk antrian dapur secara otomatis.</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center relative bg-mono-white p-4">
                <div class="w-24 h-24 bg-primary-light text-primary rounded-full flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg border-4 border-mono-white relative z-10">
                    <i class="bi bi-fire"></i>
                </div>
                <h4 class="font-bold text-xl mb-2">3. Dimasak</h4>
                <p class="text-sm text-mono-gray">Chef kami menyiapkan hidangan Anda.</p>
            </div>

            <!-- Step 4 -->
            <div class="text-center relative bg-mono-white p-4">
                <div class="w-24 h-24 bg-mono-gray text-white rounded-full flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg border-4 border-mono-white relative z-10">
                    <i class="bi bi-megaphone"></i>
                </div>
                <h4 class="font-bold text-xl mb-2">4. Dipanggil</h4>
                <p class="text-sm text-mono-gray">Nama Anda akan dipanggil saat pesanan siap.</p>
            </div>

            <!-- Step 5 -->
            <div class="text-center relative bg-mono-white p-4">
                <div class="w-24 h-24 bg-primary text-mono-black rounded-full flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg border-4 border-mono-white relative z-10">
                    <i class="bi bi-bag-check"></i>
                </div>
                <h4 class="font-bold text-xl mb-2">5. Ambil & Bayar</h4>
                <p class="text-sm text-mono-gray">Ambil pesanan di counter dan lakukan pembayaran.</p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 4: KASIR / POS INTERFACE -->
<section id="kasir" class="min-h-screen bg-mono-off-white py-8">
    <div class="container mx-auto px-4 h-full">
        <div class="flex flex-col lg:flex-row gap-6 h-full">
            
            <!-- LEFT COLUMN: MENU (Scrollable) -->
            <div class="w-full lg:w-3/4 flex flex-col lg:h-[calc(100vh-4rem)] lg:sticky lg:top-4">
                <!-- Header & Tabs -->
                <div class="flex-none bg-mono-white rounded-2xl p-6 shadow-sm mb-6 z-20">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h2 class="font-display text-3xl font-bold text-mono-black">Menu</h2>
                            <p class="text-mono-gray text-sm">Silakan pilih menu yang tersedia</p>
                        </div>
                        
                        <!-- Category Tabs -->
                        <div class="inline-flex bg-mono-off-white rounded-full p-1 border border-primary-light">
                            <button class="category-btn active px-6 py-2 rounded-full font-medium text-sm uppercase tracking-wide transition-all flex items-center gap-2" data-category="makanan">
                                <i class="bi bi-egg-fried"></i> Makanan
                            </button>
                            <button class="category-btn px-6 py-2 rounded-full font-medium text-sm uppercase tracking-wide transition-all flex items-center gap-2" data-category="minuman">
                                <i class="bi bi-cup-straw"></i> Minuman
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid Wrapper (Scrollable) -->
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar pb-24 lg:pb-0" style="overscroll-behavior: contain;">
                    <div id="products-container" class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <!-- Loading Skeleton -->
                        @for ($i = 0; $i < 6; $i++)
                            <div class="product-skeleton bg-mono-white rounded-2xl p-4 shadow-sm">
                                <div class="bg-mono-light h-40 rounded-xl mb-4 animate-pulse"></div>
                                <div class="h-4 bg-mono-light rounded w-3/4 mb-2 animate-pulse"></div>
                                <div class="h-4 bg-mono-light rounded w-1/2 mb-4 animate-pulse"></div>
                                <div class="h-10 bg-mono-light rounded-full w-full animate-pulse"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: CART (Fixed/Sticky) -->
            <div class="w-full lg:w-1/4">
                <div class="bg-mono-white rounded-2xl shadow-mono-lg h-[calc(100vh-4rem)] sticky top-4 flex flex-col border border-primary-light">
                    <!-- Cart Header -->
                    <div class="p-6 border-b border-primary-light bg-primary-light bg-opacity-30 rounded-t-2xl">
                        <h3 class="font-display text-xl font-bold text-mono-black flex items-center gap-2">
                            <i class="bi bi-receipt"></i> Detail Pesanan
                        </h3>
                    </div>

                    <!-- Customer Info -->
                    <div class="p-4 border-b border-mono-light">
                        <label class="block text-xs font-bold text-mono-gray uppercase tracking-wide mb-2">Nama Pelanggan</label>
                        <input type="text" id="sidebarCustomerName" class="w-full bg-mono-off-white border border-primary-light rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" placeholder="Ketik nama Anda...">
                    </div>

                    <!-- Cart Items (Scrollable) -->
                    <div id="sidebarCartItems" class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Empty State -->
                        <div class="text-center py-10 empty-cart">
                            <i class="bi bi-basket text-4xl text-mono-gray-light mb-3 block"></i>
                            <p class="text-sm text-mono-gray">Belum ada menu yang dipilih</p>
                        </div>
                        <!-- Items will be injected here -->
                    </div>

                    <!-- Footer Summary -->
                    <div class="p-6 bg-mono-off-white border-t border-primary-light rounded-b-2xl">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-mono-gray text-sm">Subtotal</span>
                            <span id="sidebar-subtotal" class="font-bold text-mono-black">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-mono-gray text-sm">Pajak (0%)</span> <!-- Assuming no tax for now -->
                            <span class="font-bold text-mono-black">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center mb-6 pt-4 border-t border-mono-gray-light border-dashed">
                            <span class="text-lg font-bold text-mono-black">Total</span>
                            <span id="sidebar-total" class="font-display text-2xl font-bold text-primary">Rp 0</span>
                        </div>
                        
                        <button id="btn-process-order" class="w-full bg-primary text-mono-black py-4 rounded-xl font-bold uppercase tracking-wide shadow-lg hover:bg-primary-hover hover:shadow-xl hover:-translate-y-1 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                            <span>Proses Pesanan</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= MODALS ================= -->

<!-- Modal: Add To Cart & Toppings -->
<div id="addToCartModal" class="modal-overlay hidden fixed inset-0 bg-mono-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-mono-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-mono-xl">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-mono-white border-b-2 border-mono-light p-6 flex justify-between items-center z-10">
            <h3 class="font-display text-3xl font-bold text-mono-black" id="modalProductName">Nama Produk</h3>
            <button class="modal-close text-mono-gray hover:text-mono-black text-3xl">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Product Image & Info -->
                <div>
                    <img id="modalProductImage" src="" class="w-full h-80 object-cover rounded-lg mb-4 grayscale hover:grayscale-0 transition-all duration-500" alt="">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-mono-gray text-sm uppercase tracking-wide">Harga</span>
                        <h3 class="font-display text-4xl font-bold text-mono-black" id="modalProductPrice">Rp 0</h3>
                    </div>
                    <p class="text-mono-gray leading-relaxed" id="modalProductDesc"></p>
                </div>

                <!-- Toppings & Quantity -->
                <div>
                    <!-- Toppings Section -->
                    <div class="mb-6">
                        <h4 class="font-bold text-mono-black mb-4 text-lg uppercase tracking-wide flex items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Tambah Topping
                        </h4>
                        <div id="toppingsList" class="space-y-2 max-h-48 overflow-y-auto pr-2">
                            <div class="text-center text-mono-gray py-4 text-sm">Memuat topping...</div>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="border-2 border-primary-light rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-mono-gray text-sm uppercase tracking-wide">Jumlah</span>
                            <div class="flex items-center gap-4">
                                <button id="qty-minus" class="w-10 h-10 border-2 border-primary hover:bg-primary hover:text-mono-black transition-all rounded-full flex items-center justify-center font-bold">-</button>
                                <span id="modalQty" class="text-3xl font-bold text-mono-black w-12 text-center">1</span>
                                <button id="qty-plus" class="w-10 h-10 border-2 border-primary hover:bg-primary hover:text-mono-black transition-all rounded-full flex items-center justify-center font-bold">+</button>
                            </div>
                        </div>
                        <div class="border-t-2 border-primary-light pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-mono-gray text-sm uppercase tracking-wide">Subtotal</span>
                                <span id="modalSubtotal" class="font-display text-3xl font-bold text-mono-black">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <button id="btn-add-to-cart" class="w-full bg-primary text-mono-black py-4 rounded-full font-bold text-lg uppercase tracking-wide hover:bg-primary-hover transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Success -->
<div id="successModal" class="modal-overlay hidden fixed inset-0 bg-mono-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-mono-white rounded-2xl max-w-lg w-full p-8 text-center shadow-mono-xl">
        <div class="mb-6">
            <i class="bi bi-check-circle-fill text-8xl text-primary"></i>
        </div>
        <h2 class="font-display text-4xl font-bold text-mono-black mb-3">Pesanan Berhasil!</h2>
        <p class="text-mono-gray mb-4">Nomor Antrian Anda:</p>
        <div class="border-4 border-primary rounded-xl py-6 mb-6">
            <div class="font-display text-7xl font-bold text-mono-black" id="success-order-number">#---</div>
        </div>
        <p class="text-mono-gray mb-6">Mohon tunggu, pesanan Anda sedang diproses oleh dapur.</p>
        <div class="h-2 bg-mono-light rounded-full overflow-hidden mb-2">
            <div class="h-full bg-primary transition-all duration-1000" id="redirect-progress" style="width: 0%"></div>
        </div>
        <small class="text-mono-gray text-sm">Halaman akan refresh dalam <span id="countdown">5</span> detik...</small>
    </div>
</div>

@push('styles')
<style>
    /* Custom Scrollbar - Warm Theme */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 224, 178, 0.3); /* Transparent Orange */
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #D7CCC8; /* Mono Gray Light */
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #A1887F; /* Mono Gray */
    }

    /* Fade In Animation for Products */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }

    /* Category Tabs */
    .category-btn {
        color: #7A7A7A;
        background-color: transparent;
        border-color: #FFA726; /* Orange Border */
    }
    .category-btn.active {
        background-color: #FFA726; /* Orange */
        color: #3E2723; /* Dark Brown Text */
        border-color: #FFA726;
    }
    .category-btn:not(.active):hover {
        background-color: #FFE0B2; /* Light Orange Hover */
        color: #3E2723;
    }

    /* Product Cards */
    .product-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-4px);
    }

    /* Topping Checkbox */
    .topping-item input[type="checkbox"] {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: #FFA726; /* Orange Accent */
    }

    /* Modal Animations */
    .modal-overlay {
        animation: fadeIn 0.2s ease;
    }
    .modal-content {
        animation: slideUp 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scrollbar for modal */
    .modal-content::-webkit-scrollbar {
        width: 6px;
    }
    .modal-content::-webkit-scrollbar-thumb {
        background-color: #DADADA;
        border-radius: 3px;
    }

    /* Hide elements initially for GSAP - REMOVED to prevent blank page issues */
    /* Elements will be animated via JS .from() methods instead */
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/kasir.js') }}"></script>
<script>
    // GSAP Animations (Hero Only)
    document.addEventListener('DOMContentLoaded', function() {
        // Hero Section Animations
        gsap.to('.hero-title', {
            opacity: 1,
            y: 0,
            duration: 1,
            ease: 'power3.out',
            delay: 0.2
        });
        
        gsap.to('.hero-subtitle', {
            opacity: 1,
            y: 0,
            duration: 1,
            ease: 'power3.out',
            delay: 0.4
        });

        gsap.to('.scroll-indicator', {
            opacity: 1,
            y: 0,
            duration: 1,
            ease: 'power3.out',
            delay: 0.6
        });

        // Modal Controls
        const modals = document.querySelectorAll('.modal-overlay');
        const modalCloseBtns = document.querySelectorAll('.modal-close');

        modalCloseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.modal-overlay').classList.add('hidden');
            });
        });

        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    });
</script>
@endpush

@endsection