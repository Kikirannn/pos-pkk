/**
* Kitchen Display System (KDS) Logic
* 
* Features:
* - Real-time Polling (3s interval)
* - Differential Order Updates
* - Audio & Browser Notifications
* - Status Management (New -> Processing -> Done)
* - Performance Optimized DOM Updates
*/

$(document).ready(function () {

    // ==========================================
    // 1. STATE MANAGEMENT
    // ==========================================
    const app = {
        orders: [],             // Local storage of fetched orders
        lastOrderIds: new Set(),// Set of IDs to detect new orders
        currentFilter: 'all',   // 'all', 'new', 'processing'
        pollingInterval: null,
        isPolling: false,
        lastUpdated: null,
        audio: document.getElementById('notif-sound'),
        pollingRate: 3000,      // 3 seconds
        retryCount: 0,
        maxRetries: 5,
        doneTimer: null
    };

    // ==========================================
    // 2. INITIALIZATION
    // ==========================================

    function init() {
        console.log('Initializing Kitchen Display System...');

        // Request Notification Permission (Browser)
        if ('Notification' in window) {
            Notification.requestPermission();
        }

        // Initial Fetch
        fetchOrders();
        startPolling();

        // Event Listeners
        setupEventListeners();

        // Handle Visibility Change (Optimize Resources)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Optional: Slow down polling when hidden
                stopPolling();
                app.pollingRate = 10000; // Slow down to 10s
                startPolling();
            } else {
                // Resume normal polling
                stopPolling();
                app.pollingRate = 3000; // Normal rate
                startPolling();
                fetchOrders(); // Immediate fetch
            }
        });
    }

    // ==========================================
    // 3. POLLING SYSTEM
    // ==========================================

    function startPolling() {
        if (app.isPolling) return;

        app.pollingInterval = setInterval(fetchOrders, app.pollingRate);
        app.isPolling = true;
        updateConnectionStatus('online');
    }

    function stopPolling() {
        clearInterval(app.pollingInterval);
        app.isPolling = false;
    }

    function fetchOrders() {
        $.ajax({
            url: '/api/orders/pending',
            method: 'GET',
            timeout: 5000, // 5s timeout
            success: function (response) {
                app.retryCount = 0; // Reset retry
                handleOrdersUpdate(response.orders);
                updateConnectionStatus('online');
                updateLastUpdated();
            },
            error: function (xhr, status, error) {
                console.warn('Polling error:', error);

                // Exponential Backoff Logic (simple)
                app.retryCount++;
                if (app.retryCount > 3) {
                    updateConnectionStatus('offline');
                }

                // Don't stop polling, just let interval continue
                // unless critical auth error
                if (xhr.status === 401 || xhr.status === 419) {
                    stopPolling();
                    alert('Sesi habis. Mohon refresh halaman.');
                    window.location.reload();
                }
            }
        });
    }

    /**
     * Handle incoming data and detect changes
     * @param {Array} newOrders 
     */
    function handleOrdersUpdate(newOrders) {
        // Detect New Orders
        const newOrderIds = new Set(newOrders.map(o => o.id));
        let hasNewOrder = false;

        // Check for completely new IDs that we haven't seen in this session
        // Only if we already have some data (to avoid noise on initial load)
        if (app.lastOrderIds.size > 0) {
            newOrders.forEach(order => {
                if (!app.lastOrderIds.has(order.id) && order.status === 'new') {
                    hasNewOrder = true;
                    // Trigger Notification per new order
                    notifyNewOrder(order);
                }
            });
        }

        // Update Set
        app.lastOrderIds = new Set(newOrders.map(o => o.id));

        // Check if data actually changed to avoid unnecessary re-renders
        // Using distinct JSON string comparison for deep check
        const isDataChanged = JSON.stringify(app.orders) !== JSON.stringify(newOrders);

        if (isDataChanged) {
            app.orders = newOrders;
            renderOrders();
            updateStats();

            if (hasNewOrder) {
                playNotificationSound();
            }
        }
    }

    // ==========================================
    // 4. RENDER FUNCTIONS (OPTIMIZED)
    // ==========================================

    function renderOrders() {
        const container = $('#orders-grid');

        // Filter Orders
        let filteredOrders = app.orders;
        if (app.currentFilter !== 'all') {
            filteredOrders = app.orders.filter(o => o.status === app.currentFilter);
        }

        // Empty State
        if (filteredOrders.length === 0) {
            container.addClass('d-none'); // Hide grid temporarily
            container.empty(); // Clean
            $('#empty-state').removeClass('d-none');
            return;
        } else {
            $('#empty-state').addClass('d-none');
            container.removeClass('d-none');
        }

        // Generate HTML using DocumentFragment for performance
        // (For simplicity with jQuery, we build a large string)
        let html = '';

        filteredOrders.forEach(order => {
            html += generateOrderCard(order);
        });

        // Replace Content
        container.html(html);
    }

    function generateOrderCard(order) {
        // Styles based on Status
        let cardClass = '';
        let badgeClass = '';
        let btnAction = '';

        if (order.status === 'new') {
            cardClass = 'card-new';
            badgeClass = 'bg-danger';
            btnAction = `
                <button class="btn btn-warning w-100 fw-bold d-flex align-items-center justify-content-center gap-2" 
                        onclick="kitchen.updateStatus(${order.id}, 'processing', this)">
                    <i class="bi bi-fire"></i> Proses Pesanan
                </button>
            `;
        } else if (order.status === 'processing') {
            cardClass = 'card-processing';
            badgeClass = 'bg-warning text-dark';
            btnAction = `
                <button class="btn btn-success w-100 fw-bold d-flex align-items-center justify-content-center gap-2" 
                        onclick="kitchen.updateStatus(${order.id}, 'done', this)">
                    <i class="bi bi-check-lg"></i> Selesai Masak
                </button>
            `;
        } else if (order.status === 'done') {
            cardClass = 'card-done border-start border-success border-5 opacity-75';
            badgeClass = 'bg-success';
            btnAction = `
                <div class="text-center text-success fw-bold py-2">
                    <i class="bi bi-check-all fs-4"></i><br>Selesai
                </div>
            `;
        }

        // Items List
        let itemsHtml = '<ul class="list-unstyled mb-0 item-list">';
        order.items.forEach(item => {
            const toppingsHtml = item.toppings.length > 0
                ? `<div class="topping-list ms-3 border-start ps-2 border-3"><i class="bi bi-plus small"></i> ${item.toppings.join(', ')}</div>`
                : '';

            itemsHtml += `
                <li class="mb-2">
                    <div class="fw-bold">${item.quantity}x ${item.product_name}</div>
                    ${toppingsHtml}
                </li>
            `;
        });
        itemsHtml += '</ul>';

        return `
            <div class="col-md-6 col-xl-4 animate__animated animate__fadeIn">
                <div class="card h-100 shadow-sm border-0 ${cardClass}">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge ${badgeClass} mb-2 text-uppercase">${order.status}</span>
                                <div class="order-number fw-black text-dark">#${order.order_number}</div>
                                <div class="fw-bold text-primary text-truncate fs-5" style="max-width: 150px;">
                                    <i class="bi bi-person-fill"></i> ${order.customer_name || 'Tanpa Nama'}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-5">${order.formatted_created_at.split(',')[1]}</div>
                                <div class="text-muted small">${order.elapsed_time}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        ${itemsHtml}
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pb-3 pt-0">
                        <hr class="text-muted opacity-25 my-3">
                        ${btnAction}
                    </div>
                </div>
            </div>
        `;
    }

    function updateStats() {
        const total = app.orders.length;
        const newCount = app.orders.filter(o => o.status === 'new').length;
        const processingCount = app.orders.filter(o => o.status === 'processing').length;
        const doneCount = app.orders.filter(o => o.status === 'done').length;

        $('#pending-count').text(`${total} Pesanan Hari Ini`); // Updated label
        $('#badge-new').text(newCount);
        $('#badge-processing').text(processingCount);
        $('#badge-done').text(doneCount);

        // Update Title for browser tab indication
        document.title = total > 0 ? `(${total}) Dapur - Kantin Pintar` : 'Dapur - Kantin Pintar';
    }

    // ==========================================
    // 5. ACTIONS & UTILITIES
    // ==========================================

    // Expose Global Action for Inline OnClick
    window.kitchen = {
        updateStatus: function (orderId, newStatus, btnElement) {
            // UI Feedback
            const originalText = $(btnElement).html();
            $(btnElement).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');

            $.ajax({
                url: `/api/orders/${orderId}/status`,
                method: 'PATCH',
                data: {
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    showToast(`Status updated: #${response.data.status}`, 'success');
                    // Fetch immediately to sync UI
                    fetchOrders();
                },
                error: function (xhr) {
                    console.error('Update failed:', xhr);
                    showToast('Gagal update status via server.', 'error');
                    // Revert UI
                    $(btnElement).prop('disabled', false).html(originalText);
                }
            });
        }
    };

    function notifyNewOrder(order) {
        // Browser Notification
        if (Notification.permission === "granted") {
            const notification = new Notification(`Pesanan Baru #${order.order_number}`, {
                body: `${order.items_count} Items menunggu diproses.`,
                icon: '/favicon.ico' // Ensure this exists or remove
            });

            notification.onclick = function () {
                window.focus();
                this.close();
            };
        }
    }

    function playNotificationSound() {
        try {
            app.audio.currentTime = 0;
            app.audio.play().catch(e => console.log('Audio autoplay blocked', e));
        } catch (e) {
            console.error('Audio play error', e);
        }
    }

    function updateConnectionStatus(status) {
        const el = $('#connection-status');
        if (status === 'online') {
            el.html('<div class="spinner-grow spinner-grow-sm me-2 text-success" role="status"></div><span class="text-success">Live Sync</span>');
        } else {
            el.html('<i class="bi bi-wifi-off me-2 text-danger"></i><span class="text-danger">Offline Reconnecting...</span>');
        }
    }

    function updateLastUpdated() {
        const now = new Date();
        $('#last-updated').text(now.toLocaleTimeString());
    }

    function showToast(message, type = 'success') {
        // Reuse global toast function if available, or simple alert fallback for now
        // Assuming the same toast structure as Kasir page is available or just log
        console.log(`[TOAST ${type}]: ${message}`);
        // Can implement a dedicated toast here if needed
    }

    function setupEventListeners() {
        // Filters
        $('input[name="statusFilter"]').change(function () {
            if ($('#filterAll').is(':checked')) app.currentFilter = 'all';
            if ($('#filterNew').is(':checked')) app.currentFilter = 'new';
            if ($('#filterProcessing').is(':checked')) app.currentFilter = 'processing';
            if ($('#filterDone').is(':checked')) app.currentFilter = 'done';

            renderOrders();
        });
    }

    // Run Init
    init();
});
