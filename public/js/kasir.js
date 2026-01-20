/**
 * Kasir Self-Service Application Logic
 * 
 * Features:
 * - State Management (Cart, Products, Toppings)
 * - API Integration (Fetch & Submit)
 * - Dynamic Rendering
 * - Complex Price Calculation
 * - Idle Timer
 */

$(document).ready(function () {

    // ==========================================
    // 1. STATE MANAGEMENT
    // ==========================================
    let state = {
        products: [],
        toppings: { makanan: [], minuman: [] },
        cart: [],
        currentProduct: null,     // Product currently being viewed in modal
        currentQty: 1,
        selectedToppings: [],     // Array of topping objects
        activeCategory: 'makanan', // Default category
        idleTimer: null,
        idleTimeLimit: 60000,     // 60 seconds to reset
    };

    // ==========================================
    // 2. INITIALIZATION & DATA FETCHING
    // ==========================================

    function init() {
        console.log('Initializing Kasir App...');

        // Initial Fetch
        fetchToppings();
        fetchProducts('makanan'); // Default Load

        // Start Idle Timer
        resetIdleTimer();

        // Setup Global Event Listeners for User Activity (Reset Timer)
        $(document).on('click mousemove touchstart keydown', resetIdleTimer);
    }

    /**
     * Fetch products by category from API
     * @param {string} category - 'makanan' or 'minuman'
     */
    function fetchProducts(category) {
        state.activeCategory = category;
        showLoadingProducts();

        $.ajax({
            url: '/api/products',
            method: 'GET',
            data: { category: category },
            success: function (response) {
                state.products = response;
                renderProducts(state.products);
            },
            error: function (err) {
                console.error('Error fetching products:', err);
                showToast('Gagal memuat produk. Silakan coba lagi.', 'error');
                $('#products-container').html('<div class="col-12 text-center py-5 text-muted">Gagal memuat data.</div>');
            }
        });
    }

    /**
     * Fetch all available toppings
     */
    function fetchToppings() {
        $.ajax({
            url: '/api/toppings',
            method: 'GET',
            success: function (response) {
                state.toppings = response; // Expected format: { makanan: [], minuman: [] }
            },
            error: function (err) {
                console.error('Error fetching toppings:', err);
            }
        });
    }

    // ==========================================
    // 3. RENDERING FUNCTIONS
    // ==========================================

    /**
     * Render product cards grid
     * @param {Array} products 
     */
    function renderProducts(products) {
        const container = $('#products-container');
        container.empty();

        if (products.length === 0) {
            container.html(`
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted opacity-25"></i>
                    <p class="mt-3 text-muted fs-4">Menu tidak ditemukan</p>
                </div>
            `);
            return;
        }

        products.forEach(product => {
            const html = `
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm product-card-hover cursor-pointer" onclick="app.openToppingModal(${product.id})">
                        <div class="position-relative overflow-hidden">
                            <img src="${product.image_url}" class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">
                            <button class="btn btn-primary position-absolute bottom-0 end-0 m-3 rounded-circle shadow" style="width: 45px; height: 45px;">
                                <i class="bi bi-plus-lg text-white"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-1 text-truncate">${product.name}</h5>
                            <p class="card-text text-primary fw-bold fs-5 mb-0">${product.formatted_price}</p>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });
    }

    /**
     * Render items in Cart Modal
     */
    function renderCart() {
        const list = $('#cartItemsList');
        list.empty();

        if (state.cart.length === 0) {
            list.html(`
                <div class="text-center py-5">
                    <i class="bi bi-cart-x fs-1 text-muted opacity-50"></i>
                    <p class="mt-3 text-muted fs-5">Keranjang masih kosong</p>
                </div>
            `);
            $('#cart-grand-total').text('Rp 0');
            $('#btn-checkout').prop('disabled', true);
            return;
        }

        let grandTotal = 0;

        state.cart.forEach((item, index) => {
            const itemTotal = calculateItemSubtotal(item);
            grandTotal += itemTotal;

            const toppingsHtml = item.toppings.length > 0
                ? `<div class="text-muted small mt-1">
                    <i class="bi bi-plus small"></i> ${item.toppings.map(t => t.name).join(', ')}
                   </div>`
                : '';

            const html = `
                <div class="list-group-item p-3 border-bottom-0 border-top bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="fw-bold mb-0">${item.product.name}</h6>
                            ${toppingsHtml}
                        </div>
                        <div class="fw-bold text-primary">${formatPrice(itemTotal)}</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" onclick="app.updateCartQty(${index}, -1)" style="width: 32px; height: 32px;">-</button>
                            <span class="fw-bold px-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-primary rounded-circle" onclick="app.updateCartQty(${index}, 1)" style="width: 32px; height: 32px;">+</button>
                        </div>
                        <button class="btn btn-sm btn-link text-danger text-decoration-none p-0" onclick="app.removeFromCart(${index})">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            `;
            list.append(html);
        });

        $('#cart-grand-total').text(formatPrice(grandTotal));
        $('#btn-checkout').prop('disabled', false);
    }

    function showLoadingProducts() {
        const skeleton = `
            <div class="col skeleton-loader"><div class="card h-100 border-0 shadow-sm"><div class="card-img-top bg-light" style="height: 200px;"></div><div class="card-body"><h5 class="placeholder-glow"><span class="placeholder col-6"></span></h5><p class="placeholder-glow"><span class="placeholder col-4"></span></p></div></div></div>
        `;
        $('#products-container').html(skeleton.repeat(4));
    }

    // ==========================================
    // 4. MODAL LOGIC (ADD TO CART)
    // ==========================================

    /**
     * Open Modal to configure product (add toppings)
     * @param {number} productId 
     */
    window.app.openToppingModal = function (productId) {
        state.currentProduct = state.products.find(p => p.id === productId);
        state.currentQty = 1;
        state.selectedToppings = [];

        if (!state.currentProduct) return;

        // Populate Modal Info
        $('#modalProductName').text(state.currentProduct.name);
        $('#modalProductPrice').text(state.currentProduct.formatted_price);
        $('#modalProductDesc').text(state.currentProduct.description || 'Tidak ada deskripsi');
        $('#modalProductImage').attr('src', state.currentProduct.image_url);
        $('#modalQty').text(state.currentQty);

        // Render Toppings specific to product category
        renderToppingsSelection(state.currentProduct.category);

        updateModalSubtotal();

        // Show Modal
        new bootstrap.Modal('#addToCartModal').show();
    };

    function renderToppingsSelection(category) {
        const container = $('#toppingsList');
        container.empty();

        // Get relevant toppings (ensure access safe)
        const relevantToppings = state.toppings[category] || [];

        if (relevantToppings.length === 0) {
            container.html('<div class="text-muted text-center small py-2">Tidak ada topping tersedia untuk kategori ini.</div>');
            return;
        }

        relevantToppings.forEach(topping => {
            const html = `
                <div class="d-flex justify-content-between align-items-center p-2 rounded hover-bg-light border-bottom border-light">
                    <div class="form-check flex-grow-1">
                        <input class="form-check-input topping-checkbox" type="checkbox" value="${topping.id}" id="topping-${topping.id}" 
                               data-price="${topping.price}" data-name="${topping.name}">
                        <label class="form-check-label w-100" for="topping-${topping.id}">
                            ${topping.name}
                        </label>
                    </div>
                    <span class="fw-bold text-muted small">+${formatPrice(topping.price)}</span>
                </div>
            `;
            container.append(html);
        });

        // Bind change events
        $('.topping-checkbox').change(function () {
            const id = parseInt($(this).val());
            const price = parseFloat($(this).data('price'));
            const name = $(this).data('name');
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                state.selectedToppings.push({ id, price, name });
            } else {
                state.selectedToppings = state.selectedToppings.filter(t => t.id !== id);
            }
            updateModalSubtotal();
        });
    }

    function updateModalSubtotal() {
        if (!state.currentProduct) return;

        // Calculate: (Product Price + Total Topping Price) * Qty
        const toppingTotal = state.selectedToppings.reduce((sum, t) => sum + t.price, 0);
        const subtotal = (parseFloat(state.currentProduct.price) + toppingTotal) * state.currentQty;

        $('#modalSubtotal').text(formatPrice(subtotal));
    }

    // ==========================================
    // 5. CART MANAGEMENT
    // ==========================================

    $('#btn-add-to-cart').click(function () {
        if (!state.currentProduct) return;

        const cartItem = {
            product: state.currentProduct,
            quantity: state.currentQty,
            toppings: [...state.selectedToppings] // Clone array
        };

        state.cart.push(cartItem);
        updateCartCount();
        showToast('Item berhasil ditambahkan ke keranjang!', 'success');

        // Hide Modal properly
        const modalEl = document.getElementById('addToCartModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    });

    window.app.removeFromCart = function (index) {
        state.cart.splice(index, 1);
        renderCart();
        updateCartCount();
    }

    window.app.updateCartQty = function (index, change) {
        const item = state.cart[index];
        const newQty = item.quantity + change;

        if (newQty < 1) return; // Minimum 1
        if (newQty > 99) return; // Max 99

        item.quantity = newQty;
        renderCart();
    }

    function updateCartCount() {
        const count = state.cart.reduce((sum, item) => sum + item.quantity, 0);
        $('#cart-count').text(count);

        // Animate badge
        $('#cart-count').addClass('animate__pulse');
        setTimeout(() => $('#cart-count').removeClass('animate__pulse'), 500);
    }

    function calculateItemSubtotal(item) {
        const toppingTotal = item.toppings.reduce((sum, t) => sum + t.price, 0);
        return (parseFloat(item.product.price) + toppingTotal) * item.quantity;
    }

    // ==========================================
    // 6. ORDER SUBMISSION
    // ==========================================

    $('#btn-checkout').click(function () {
        if (state.cart.length === 0) return;

        // Validate Customer Name
        const customerName = $('#customerName').val().trim();
        if (!customerName) {
            showToast('Mohon isi nama pemesan.', 'error');
            $('#customerName').focus();
            return;
        }

        // Disable button & Show Loading
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

        // Prepare Payload
        const payload = {
            customer_name: customerName,
            items: state.cart.map(item => ({
                product_id: item.product.id,
                quantity: item.quantity,
                toppings: item.toppings.map(t => t.id)
            }))
        };

        $.ajax({
            url: '/api/orders',
            method: 'POST',
            data: payload,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Success Flow
                $('#cartModal').modal('hide'); // Close Cart

                // Show Success Modal
                $('#success-order-number').text('#' + response.order_number);
                new bootstrap.Modal('#successModal').show();

                // Start Countdown & Redirect
                startSuccessCountdown();

                state.cart = []; // Clear Cart
                $('#customerName').val(''); // Clear Name
                updateCartCount();
            },
            error: function (xhr) {
                console.error('Order Failed:', xhr);
                const msg = xhr.responseJSON?.message || 'Gagal membuat pesanan.';
                showToast(msg, 'error');

                // Reset Button
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    function startSuccessCountdown() {
        let count = 5;
        const interval = setInterval(() => {
            count--;
            $('#countdown').text(count);
            if (count <= 0) {
                clearInterval(interval);
                window.location.reload(); // Hard refresh to reset everything
            }
        }, 1000);
    }

    // ==========================================
    // 7. UTILITIES
    // ==========================================

    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(price).replace('Rp', 'Rp '); // Add space for readability
    }

    function showToast(message, type = 'success') {
        const toastEl = $('#liveToast');
        toastEl.find('.toast-body span').text(message);

        // Adjust color based on type
        if (type === 'error') {
            toastEl.removeClass('bg-success').addClass('bg-danger');
        } else {
            toastEl.removeClass('bg-danger').addClass('bg-success');
        }

        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    // ==========================================
    // 8. IDLE TIMER
    // ==========================================
    function resetIdleTimer() {
        clearTimeout(state.idleTimer);

        // Set new timer
        state.idleTimer = setTimeout(() => {
            // Check if modals are open to determine if we should warn or just reload
            // Ideally, simple reload is enough for MVP kiosk
            window.location.reload();
        }, state.idleTimeLimit);
    }

    // ==========================================
    // 9. EVENT BINDINGS (CONTROLS)
    // ==========================================

    // Quantity Controls in Modal
    $('#qty-minus').click(function () {
        if (state.currentQty > 1) {
            state.currentQty--;
            $('#modalQty').text(state.currentQty);
            updateModalSubtotal();
        }
    });

    $('#qty-plus').click(function () {
        if (state.currentQty < 99) {
            state.currentQty++;
            $('#modalQty').text(state.currentQty);
            updateModalSubtotal();
        }
    });

    // Cart Modal Open
    $('#cartModal').on('show.bs.modal', function () {
        renderCart();
    });

    // Tab Switching
    $('#categoryTabs button').on('shown.bs.tab', function (event) {
        const category = $(event.target).data('category');
        fetchProducts(category);
    });

    // Initialize App
    init();

});

// Expose functions globally for inline onclick handlers (cleaner than lots of standard listeners)
window.app = window.app || {};
