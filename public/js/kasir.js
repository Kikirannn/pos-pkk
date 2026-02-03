/**
 * KASIR JS - Integrated with Landing Page
 * Handles: Auto Scroll Flow, Product Rendering, Cart Management, Order Processing
 */

$(document).ready(function () {
    
    // ==========================================
    // 1. STATE MANAGEMENT
    // ==========================================
    const app = {
        products: [],
        toppings: [],
        cart: [],
        currentCategory: 'makanan',
        currentProduct: null, // For modal
        isShoppingMode: false
    };

    // ==========================================
    // 2. INITIALIZATION
    // ==========================================
    function init() {
        console.log('Initializing Kasir System...');
        
        // Initial Data Fetch
        fetchProducts();
        fetchToppings();

        // Setup Event Listeners
        setupEventListeners();

        // Check if we should restore cart (optional, maybe clear for kiosk mode)
        // For Kiosk mode, we usually start fresh.
        
        // GSAP Intro Animation for Hero
        animateHero();
    }

    // ==========================================
    // 3. AUTO SCROLL & LOCK FLOW
    // ==========================================
    
    function startShoppingFlow() {
        if (app.isShoppingMode) return;
        app.isShoppingMode = true;

        console.log('Starting Shopping Flow...');

        // Disable button to prevent double clicks
        $('#btn-start-shopping').addClass('pointer-events-none opacity-50');

        // Sequence using Promises for better control
        const sequence = async () => {
            // 1. Scroll to About
            await scrollToSection('#about', 2000);
            
            // 2. Scroll to How It Works
            await scrollToSection('#how-it-works', 3000); // Give time to read

            // 3. Scroll to Kasir
            await scrollToSection('#kasir', 1000);

            // 4. Lock Interface - REMOVED per user request
            // lockToKasir();
            
            // Re-enable button
            $('#btn-start-shopping').removeClass('pointer-events-none opacity-50');
            app.isShoppingMode = false;
        };

        sequence();
    }

    function scrollToSection(selector, duration) {
        return new Promise(resolve => {
            const el = document.querySelector(selector);
            if (el) {
                // Smooth scroll
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Wait for the duration before resolving
                setTimeout(resolve, duration);
            } else {
                resolve(); // Skip if not found
            }
        });
    }

    /* 
    // Lock Function Removed per request
    function lockToKasir() {
        ...
    }
    */

    function animateHero() {
        // Optimized GSAP entry for Hero
        const tl = gsap.timeline();
        
        // Use autoAlpha for better performance (handles opacity + visibility)
        // Add force3D and willChange to optimize GPU usage
        tl.from('.hero-content > *', {
            opacity: 0,
            y: 30,
            duration: 0.6, // Reduced from 0.8s for snappier feel
            stagger: 0.1,  // Reduced from 0.2s to minimize delay
            ease: 'power3.out', // Snappier ease than power2
            force3D: true, // Force hardware acceleration
            willChange: 'transform, opacity', // Hint browser to optimize layering
            clearProps: 'willChange' // Clean up after animation to free memory
        });

        // Animate Floating Images (Pop in)
        gsap.from('.hero-img-1, .hero-img-2, .hero-img-3', {
            scale: 0,
            opacity: 0,
            rotation: -15,
            duration: 0.8,
            stagger: 0.2,
            ease: 'back.out(1.7)',
            delay: 0.3
        });

        // Continuous Floating Animation (Yoyo)
        gsap.to('.hero-img-1', {
            y: -15,
            rotation: 5,
            duration: 3,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });

        gsap.to('.hero-img-2', {
            y: 15,
            rotation: -5,
            duration: 4,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 0.5
        });

        gsap.to('.hero-img-3', {
            x: 10,
            rotation: 3,
            duration: 5,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            delay: 1
        });
    }

    // ==========================================
    // 4. DATA FETCHING
    // ==========================================

    function fetchProducts() {
        $.ajax({
            url: '/api/products',
            method: 'GET',
            success: function (response) {
                // Handle response if it's direct array or wrapped object
                app.products = Array.isArray(response) ? response : (response.products || []);
                renderProducts();
            },
            error: function () {
                $('#products-container').html('<div class="col-span-3 text-center text-red-500">Gagal memuat produk.</div>');
            }
        });
    }

    function fetchToppings() {
        $.ajax({
            url: '/api/toppings',
            method: 'GET',
            success: function (response) {
                // Handle grouped response from controller {makanan: [], minuman: []}
                let all = [];
                if (response.makanan && Array.isArray(response.makanan)) {
                    all = all.concat(response.makanan);
                }
                if (response.minuman && Array.isArray(response.minuman)) {
                    all = all.concat(response.minuman);
                }
                
                // Fallback if response is direct array
                if (Array.isArray(response)) {
                    all = response;
                } else if (response.toppings && Array.isArray(response.toppings)) {
                    all = response.toppings;
                }

                app.toppings = all;
            }
        });
    }

    // ==========================================
    // 5. RENDERING
    // ==========================================

    function renderProducts() {
        const container = $('#products-container');
        container.empty();

        // Safe filtering (case-insensitive and handling nulls)
        const filtered = app.products.filter(p => 
            p.category && p.category.toLowerCase() === app.currentCategory.toLowerCase()
        );

        if (filtered.length === 0) {
            container.html(`
                <div class="col-span-full text-center py-12">
                    <i class="bi bi-search text-4xl text-mono-gray-light mb-4 block"></i>
                    <p class="text-mono-gray">Tidak ada menu di kategori ini.</p>
                </div>
            `);
            return;
        }

        // Use DocumentFragment for performance
        const fragment = document.createDocumentFragment();

        filtered.forEach((product, index) => {
            const priceFormatted = formatRupiah(product.price);
            const imageSrc = product.image_url || 'https://via.placeholder.com/300?text=No+Image';
            
            // Stagger animation delay using inline style
            const delay = Math.min(index * 0.05, 1.0); // Cap delay at 1s max

            // Stock Badge Logic
            let stockBadge = '';
            let isOutOfStock = product.stock <= 0;
            let buttonHtml = '';

            if (isOutOfStock) {
                stockBadge = `<div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-bold shadow-sm">Habis</div>`;
                buttonHtml = `
                    <button class="mt-3 w-full py-2 bg-gray-200 text-gray-400 font-bold rounded-lg text-sm cursor-not-allowed" disabled>
                        Stok Habis
                    </button>
                `;
            } else {
                stockBadge = `<div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-xs font-bold shadow-sm">Stok: ${product.stock}</div>`;
                buttonHtml = `
                    <button class="mt-3 w-full py-2 bg-mono-off-white text-primary font-bold rounded-lg text-sm">
                        Tambah
                    </button>
                `;
            }

            const cardDiv = document.createElement('div');
            // Removed hover effects: hover:shadow-mono-lg, hover:border-primary-light, group
            cardDiv.className = `product-card bg-mono-white rounded-2xl p-4 shadow-mono border border-transparent animate-fade-in opacity-0 ${isOutOfStock ? 'opacity-70 grayscale' : ''}`;
            cardDiv.style.animationDelay = `${delay}s`;
            if (!isOutOfStock) {
                cardDiv.onclick = () => kasir.openProductModal(product.id);
            }

            cardDiv.innerHTML = `
                <div class="relative overflow-hidden rounded-xl mb-4 h-40 bg-mono-light">
                    ${stockBadge}
                    <!-- Removed hover effects: transform, group-hover:scale-110, transition-transform, duration-500 -->
                    <img src="${imageSrc}" class="w-full h-full object-cover" alt="${product.name}" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                    <div class="absolute bottom-2 right-2 bg-mono-white bg-opacity-90 backdrop-blur-sm px-3 py-1 rounded-lg shadow-sm">
                        <span class="font-bold text-mono-black text-sm">${priceFormatted}</span>
                    </div>
                </div>
                <!-- Removed hover effects: group-hover:text-primary, transition-colors -->
                <h3 class="font-display font-bold text-lg text-mono-black leading-tight mb-1">${product.name}</h3>
                <p class="text-xs text-mono-gray line-clamp-2">${product.description || 'Enak dan lezat.'}</p>
                <!-- Removed hover effects: group-hover:bg-primary, group-hover:text-mono-black, transition-colors -->
                ${buttonHtml}
            `;
            
            fragment.appendChild(cardDiv);
        });

        container.append(fragment);
    }

    function renderCart() {
        const container = $('#sidebarCartItems');
        container.empty();

        if (app.cart.length === 0) {
            container.html(`
                <div class="text-center py-10 empty-cart">
                    <i class="bi bi-basket text-4xl text-mono-gray-light mb-3 block"></i>
                    <p class="text-sm text-mono-gray">Belum ada menu yang dipilih</p>
                </div>
            `);
            $('#sidebar-subtotal').text('Rp 0');
            $('#sidebar-total').text('Rp 0');
            $('#btn-process-order').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            return;
        }

        let total = 0;

        app.cart.forEach((item, index) => {
            const itemTotal = (item.price * item.quantity) + (item.toppings.reduce((a, b) => a + b.price, 0) * item.quantity);
            total += itemTotal;

            const toppingsHtml = item.toppings.length > 0 
                ? `<div class="text-xs text-mono-gray mt-1">+ ${item.toppings.map(t => t.name).join(', ')}</div>` 
                : '';

            const html = `
                <div class="flex gap-3 bg-mono-off-white p-3 rounded-xl border border-transparent hover:border-primary-light transition-colors relative group">
                    <!-- Remove Button (Persistent & Touch Friendly) -->
                    <button class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full shadow-md z-10 flex items-center justify-center transition-transform transform active:scale-95 focus:outline-none" onclick="kasir.removeFromCart(${index})" aria-label="Hapus item">
                        <i class="bi bi-trash text-sm"></i>
                    </button>

                    <!-- Image -->
                    <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="${item.image_url || 'https://via.placeholder.com/150'}" class="w-full h-full object-cover">
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-mono-black text-sm truncate pr-6">${item.name}</h4>
                            <span class="font-bold text-mono-black text-xs">${formatRupiah(itemTotal)}</span>
                        </div>
                        ${toppingsHtml}
                        
                        <!-- Qty Control Mini -->
                        <div class="flex items-center gap-2 mt-2">
                            <button class="w-6 h-6 bg-mono-white rounded-full text-primary border border-primary-light flex items-center justify-center hover:bg-primary hover:text-mono-black transition-colors" onclick="kasir.updateCartQty(${index}, -1)">-</button>
                            <span class="text-xs font-bold text-mono-black w-4 text-center">${item.quantity}</span>
                            <button class="w-6 h-6 bg-mono-white rounded-full text-primary border border-primary-light flex items-center justify-center hover:bg-primary hover:text-mono-black transition-colors" onclick="kasir.updateCartQty(${index}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });

        $('#sidebar-subtotal').text(formatRupiah(total));
        $('#sidebar-total').text(formatRupiah(total));
        $('#btn-process-order').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
    }

    // ==========================================
    // 6. ACTIONS & MODALS
    // ==========================================

    // Open Product Modal
    window.kasir = {
        openProductModal: function(id) {
            const product = app.products.find(p => p.id === id);
            if (!product) return;
            
            // Stock Check
            if (product.stock <= 0) {
                showNotification('Stok Habis', 'Maaf, stok produk ini sedang kosong.', 'error');
                return;
            }

            // FEATURE: Conditional Logic
            // Only show modal for "Mie Ayam". Others add directly.
            const productName = product.name.toLowerCase();
            const isMieAyam = productName.includes('mie ayam');

            if (!isMieAyam) {
                // Direct Add to Cart
                // Check if already in cart and stock limit
                const existingItem = app.cart.find(item => item.id === product.id);
                const currentQty = existingItem ? existingItem.quantity : 0;
                
                if (currentQty + 1 > product.stock) {
                     showNotification('Stok Terbatas', `Hanya tersedia ${product.stock} item.`, 'warning');
                     return;
                }

                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    const cartItem = {
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        image_url: product.image_url,
                        quantity: 1,
                        toppings: [] // No toppings
                    };
                    app.cart.push(cartItem);
                }
                
                renderCart();

                // Feedback
                const btn = $('#btn-process-order');
                btn.addClass('animate-bounce');
                setTimeout(() => btn.removeClass('animate-bounce'), 1000);

                showNotification('Berhasil', `${product.name} telah ditambahkan ke keranjang!`);

                return;
            }

            // --- Show Modal for Mie Ayam ---
            app.currentProduct = { ...product, quantity: 1, selectedToppings: [] };

            // Populate Modal
            $('#modalProductName').text(product.name);
            $('#modalProductPrice').text(formatRupiah(product.price));
            $('#modalProductDesc').text(product.description || 'Tidak ada deskripsi.');
            $('#modalProductImage').attr('src', product.image_url || 'https://via.placeholder.com/300');
            $('#modalQty').text(1);
            
            // Set Max Qty based on stock (minus what's already in cart)
            const existingItem = app.cart.find(item => item.id === product.id);
            const currentQtyInCart = existingItem ? existingItem.quantity : 0;
            const maxQty = product.stock - currentQtyInCart;
            
            $('#modalQty').data('max', maxQty);
            if (maxQty <= 0) {
                 showNotification('Stok Habis', 'Anda sudah mengambil semua stok tersedia.', 'error');
                 return;
            }

            // Render Toppings
            const toppingsContainer = $('#toppingsList');
            toppingsContainer.empty();
            
            // Filter toppings based on product category
            const relevantToppings = app.toppings.filter(t => 
                t.category && product.category && 
                t.category.toLowerCase() === product.category.toLowerCase()
            );

            if (relevantToppings.length > 0) {
                relevantToppings.forEach(topping => {
                    const tHtml = `
                        <div class="topping-item flex items-center justify-between p-3 rounded-lg border border-mono-light hover:border-primary-light cursor-pointer transition-colors bg-mono-off-white">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="top-${topping.id}" value="${topping.id}" data-price="${topping.price}" data-name="${topping.name}">
                                <label for="top-${topping.id}" class="cursor-pointer select-none">
                                    <span class="font-bold text-mono-black block">${topping.name}</span>
                                </label>
                            </div>
                            <span class="text-sm font-bold text-primary">+${formatRupiah(topping.price)}</span>
                        </div>
                    `;
                    toppingsContainer.append(tHtml);
                });
            } else {
                toppingsContainer.html('<div class="text-sm text-mono-gray italic">Tidak ada topping tersedia untuk kategori ini.</div>');
            }

            updateModalSubtotal();
            
            // Show Modal
            $('#addToCartModal').removeClass('hidden');
        },

        removeFromCart: function(index) {
            app.cart.splice(index, 1);
            renderCart();
        },

        updateCartQty: function(index, change) {
            const item = app.cart[index];
            const newQty = item.quantity + change;

            if (newQty < 1) {
                // Confirm delete? For now just remove
                app.cart.splice(index, 1);
            } else {
                item.quantity = newQty;
            }
            renderCart();
        }
    };

    function updateModalSubtotal() {
        if (!app.currentProduct) return;
        
        // Parse base price as float to avoid string concatenation issues
        let price = parseFloat(app.currentProduct.price);
        
        // Add Toppings
        $('#toppingsList input:checked').each(function() {
            price += parseFloat($(this).data('price')) || 0;
        });

        const subtotal = price * parseInt(app.currentProduct.quantity);
        $('#modalSubtotal').text(formatRupiah(subtotal));
    }

    function addToCart() {
        if (!app.currentProduct) return;

        // Get Toppings
        const selectedToppings = [];
        $('#toppingsList input:checked').each(function() {
            selectedToppings.push({
                id: $(this).val(),
                name: $(this).data('name'),
                price: parseFloat($(this).data('price')) // Ensure float
            });
        });

        const cartItem = {
            id: app.currentProduct.id,
            name: app.currentProduct.name,
            price: parseFloat(app.currentProduct.price), // Ensure float
            image_url: app.currentProduct.image_url,
            quantity: parseInt(app.currentProduct.quantity),
            toppings: selectedToppings
        };

        app.cart.push(cartItem);
        renderCart();
        
        // Close Modal
        $('#addToCartModal').addClass('hidden');
        
        // Animation feedback
        const btn = $('#btn-process-order');
        btn.addClass('animate-bounce');
        setTimeout(() => btn.removeClass('animate-bounce'), 1000);

        // Toast Notification for Mie Ayam
        showNotification('Berhasil', 'Mie ayam telah ditambahkan ke keranjang!');
    }

    function processOrder() {
        const customerName = $('#sidebarCustomerName').val().trim();
        
        if (!customerName) {
            Swal.fire({
                icon: 'warning',
                title: 'Nama Kosong',
                text: 'Mohon isi nama pelanggan terlebih dahulu!',
                confirmButtonColor: '#FFA726'
            });
            $('#sidebarCustomerName').focus();
            return;
        }

        if (app.cart.length === 0) return;

        // Show loading state
        const btn = $('#btn-process-order');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Memproses...');

        // Prepare Payload
        const payload = {
            customer_name: customerName,
            items: app.cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                toppings: item.toppings.map(t => t.id)
            }))
        };

        $.ajax({
            url: '/api/orders',
            method: 'POST',
            data: payload,
            success: function (res) {
                // Show Success Modal
                $('#success-order-number').text(`#${res.queue_number}`);
                $('#successModal').removeClass('hidden');

                // Start Redirect Countdown
                startRedirectCountdown();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat memproses pesanan.',
                });
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function startRedirectCountdown() {
        let seconds = 5;
        const countdownEl = $('#countdown');
        const progressEl = $('#redirect-progress');
        
        // Animate progress bar to 100% over 5s
        progressEl.css('width', '100%');

        const interval = setInterval(() => {
            seconds--;
            countdownEl.text(seconds);

            if (seconds <= 0) {
                clearInterval(interval);
                
                // Prevent browser from restoring scroll position
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }
                
                // Force reset scroll and redirect to Hero
                window.scrollTo(0, 0);
                window.location.href = '/';
            }
        }, 1000);
    }

    // ==========================================
    // 7. UTILS & EVENT LISTENERS
    // ==========================================

    function setupEventListeners() {
        // Start Shopping (Auto Scroll)
        $('#btn-start-shopping').click(function(e) {
            e.preventDefault();
            startShoppingFlow();
        });

        // Category Tabs
        $('.category-btn').click(function() {
            $('.category-btn').removeClass('active');
            $(this).addClass('active');
            app.currentCategory = $(this).data('category');
            
            // Animate transition
            gsap.to('#products-container', {
                opacity: 0,
                y: 10,
                duration: 0.2,
                onComplete: () => {
                    renderProducts();
                    gsap.to('#products-container', {
                        opacity: 1,
                        y: 0,
                        duration: 0.3
                    });
                }
            });
        });

        // Modal Controls
        $('.modal-close').click(function() {
            $(this).closest('.modal-overlay').addClass('hidden');
        });

        $('.modal-overlay').click(function(e) {
            if ($(e.target).hasClass('modal-overlay')) {
                $(this).addClass('hidden');
            }
        });

        // Qty Controls in Modal
        $('#qty-minus').click(function() {
            if (app.currentProduct.quantity > 1) {
                app.currentProduct.quantity--;
                $('#modalQty').text(app.currentProduct.quantity);
                updateModalSubtotal();
            }
        });

        $('#qty-plus').click(function() {
            const maxQty = $('#modalQty').data('max');
            if (app.currentProduct.quantity >= maxQty) {
                 showNotification('Stok Terbatas', `Hanya tersedia ${maxQty} item.`, 'warning');
                 return;
            }
            app.currentProduct.quantity++;
            $('#modalQty').text(app.currentProduct.quantity);
            updateModalSubtotal();
        });

        // Topping Checkbox Change
        $('#toppingsList').on('change', 'input[type="checkbox"]', function() {
            updateModalSubtotal();
        });

        // Add to Cart
        $('#btn-add-to-cart').click(addToCart);

        // Process Order
        $('#btn-process-order').click(processOrder);
    }

    // Helper
    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function showNotification(title, text, icon = 'success') {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }

    // Run Init
    init();
});
