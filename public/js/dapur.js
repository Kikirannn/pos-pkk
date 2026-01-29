/**
 * Kitchen Display System (KDS) Logic - Tailwind Version
 * 
 * Features:
 * - Real-time Polling (10s interval, adjustable)
 * - Differential Order Updates
 * - Audio & Browser Notifications
 * - Status Management (New -> Processing -> Done)
 * - Performance Optimized DOM Updates (Smart Diffing)
 * - Network Resilience (Exponential Backoff)
 */

$(document).ready(function () {

    // ==========================================
    // 1. STATE MANAGEMENT
    // ==========================================
    const app = {
        orders: [],             // Local storage of fetched orders
        lastOrderIds: new Set(),// Set of IDs to detect new orders
        currentFilter: 'all',   // 'all', 'new', 'processing', 'done'
        pollingTimer: null,     // Changed from interval to timer for recursive timeout
        isPolling: false,
        lastUpdated: null,
        audio: document.getElementById('notif-sound'),
        pollingRate: 10000,      // 10 seconds (Default)
        retryCount: 0,
        maxRetries: 5,
        doneTimer: null,
        autoSortDone: true      // Feature: Auto-sort 'done' to bottom
    };

    // ==========================================
    // 2. INITIALIZATION
    // ==========================================

    function init() {
        console.log('Initializing Kitchen Display System (Smart Polling)...');

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
                stopPolling();
            } else {
                startPolling();
                fetchOrders(); // Immediate fetch on return
            }
        });

        // Add Auto-Refresh Toggle to UI
        if ($('#auto-refresh-toggle').length === 0) {
             // Inject Settings Controls
             const settingsHtml = `
                <div class="flex items-center gap-4 ml-4 border-l-2 border-primary-light pl-4">
                    <!-- Refresh Indicator -->
                    <div id="refresh-indicator" class="hidden text-primary">
                        <i class="bi bi-arrow-repeat animate-spin text-xl"></i>
                    </div>

                    <!-- Auto Sort Toggle -->
                    <div class="flex items-center gap-2" title="Pindahkan pesanan selesai ke bawah">
                        <input type="checkbox" id="auto-sort-toggle" class="form-checkbox h-4 w-4 text-primary rounded focus:ring-primary" checked>
                        <label for="auto-sort-toggle" class="text-sm text-mono-gray cursor-pointer select-none font-bold">Auto Sort</label>
                    </div>

                    <!-- Refresh Interval -->
                    <div class="flex items-center gap-2">
                         <i class="bi bi-stopwatch text-mono-gray"></i>
                         <select id="refresh-interval" class="text-sm border-gray-300 rounded-md focus:ring-primary focus:border-primary py-1 px-2 bg-white">
                             <option value="10000" selected>10s</option>
                             <option value="30000">30s</option>
                             <option value="60000">60s</option>
                         </select>
                    </div>
                </div>
            `;
            $('#connection-status').after(settingsHtml);
            
            // Auto Sort Event
            $('#auto-sort-toggle').change(function() {
                app.autoSortDone = this.checked;
                renderOrders(); // Re-render immediately
            });

            // Interval Event
            $('#refresh-interval').change(function() {
                app.pollingRate = parseInt($(this).val());
                // Restart polling with new rate
                stopPolling();
                startPolling();
            });
        }
    }

    // ==========================================
    // 3. POLLING SYSTEM (Recursive with Backoff)
    // ==========================================

    function startPolling() {
        if (app.isPolling) return;
        app.isPolling = true;
        updateConnectionStatus('online');
        scheduleNextPoll();
    }

    function stopPolling() {
        if (app.pollingTimer) {
            clearTimeout(app.pollingTimer);
            app.pollingTimer = null;
        }
        app.isPolling = false;
    }

    function scheduleNextPoll(delay = app.pollingRate) {
        if (!app.isPolling) return;
        
        if (app.pollingTimer) clearTimeout(app.pollingTimer);
        
        app.pollingTimer = setTimeout(() => {
            fetchOrders();
        }, delay);
    }

    function fetchOrders() {
        $('#refresh-indicator').removeClass('hidden'); // Show indicator

        $.ajax({
            url: '/api/orders/pending',
            method: 'GET',
            timeout: 5000, // 5s timeout
            success: function (response) {
                app.retryCount = 0; // Reset retry
                handleOrdersUpdate(response.orders);
                updateConnectionStatus('online');
                updateLastUpdated();
                $('#refresh-indicator').addClass('hidden'); // Hide indicator
                
                // Schedule next normal poll
                scheduleNextPoll(app.pollingRate);
            },
            error: function (xhr, status, error) {
                console.warn('Polling error:', error);
                $('#refresh-indicator').addClass('hidden'); // Hide indicator

                // Exponential Backoff Logic
                app.retryCount++;
                let backoffDelay = app.pollingRate;
                
                if (app.retryCount > 1) {
                    // 10s -> 15s -> 22.5s -> ...
                    backoffDelay = Math.min(app.pollingRate * Math.pow(1.5, app.retryCount - 1), 60000);
                    console.log(`Retrying in ${backoffDelay/1000}s...`);
                    updateConnectionStatus('offline');
                }

                // Critical Auth Errors -> Stop
                if (xhr.status === 401 || xhr.status === 419) {
                    stopPolling();
                    alert('Sesi habis. Mohon refresh halaman.');
                    window.location.reload();
                    return;
                }
                
                // Schedule retry
                scheduleNextPoll(backoffDelay);
            }
        });
    }

    /**
     * Handle incoming data and detect changes
     * @param {Array} newOrders 
     */
    function handleOrdersUpdate(newOrders) {
        // Detect New Orders
        if (app.lastOrderIds.size > 0) {
            newOrders.forEach(order => {
                if (!app.lastOrderIds.has(order.id) && order.status === 'new') {
                    // Trigger Notification per new order
                    notifyNewOrder(order);
                    playNotificationSound();
                }
            });
        }

        // Update Set
        app.lastOrderIds = new Set(newOrders.map(o => o.id));

        // Always update data store
        app.orders = newOrders;
        
        // Smart Render (Diffing inside)
        renderOrders();
        updateStats();
    }

    // ==========================================
    // 4. RENDER FUNCTIONS (OPTIMIZED)
    // ==========================================

    function renderOrders() {
        const container = $('#orders-grid');
        const emptyState = $('#empty-state');

        // Filter Orders
        let filteredOrders = [];
        if (app.currentFilter !== 'all') {
            filteredOrders = app.orders.filter(o => o.status === app.currentFilter);
        } else {
            // Clone to avoid modifying source
            filteredOrders = [...app.orders];
            
            // Custom Sorting Logic
            if (app.autoSortDone) {
                // Priority: Active (New/Processing) > Done
                // Secondary: Chronological (ID ASC)
                filteredOrders.sort((a, b) => {
                    const isDoneA = a.status === 'done';
                    const isDoneB = b.status === 'done';

                    if (isDoneA !== isDoneB) {
                        return isDoneA ? 1 : -1; // Done goes to bottom
                    }
                    return a.id - b.id; // Both same group, sort by ID
                });
            } else {
                // Sort by ID (Chronological) regardless of status
                filteredOrders.sort((a, b) => a.id - b.id);
            }
        }

        // Empty State Handling
        if (filteredOrders.length === 0) {
            // Only toggle classes if needed to avoid flicker
            if (!container.hasClass('hidden')) {
                container.addClass('hidden');
                emptyState.removeClass('hidden');
            }
            // Clear container to be safe, but we hid it anyway
            container.empty(); 
            return;
        } else {
            if (container.hasClass('hidden')) {
                container.removeClass('hidden');
                emptyState.addClass('hidden');
            }
        }

        // ------------------------------------------
        // SMART DOM UPDATE (Prevents Flickering + Enforces Order)
        // ------------------------------------------
        
        // 1. Mark all existing cards
        container.children().addClass('marked-for-removal');

        filteredOrders.forEach(order => {
            let existingCard = $(`#order-${order.id}`);
            
            if (existingCard.length > 0) {
                // UPDATE EXISTING CARD
                existingCard.removeClass('marked-for-removal');
                
                // Update Elapsed Time (Always)
                existingCard.find('.elapsed-time-display').text(order.elapsed_time);

                // Check Status Change
                const currentStatus = existingCard.data('status');
                if (currentStatus !== order.status) {
                    // If status changed, we need to re-render the whole card
                    const newCardHtml = generateOrderCard(order);
                    existingCard.replaceWith(newCardHtml);
                    // Re-select after replace
                    existingCard = $(`#order-${order.id}`);
                }
            } else {
                // CREATE NEW CARD
                const newCardHtml = generateOrderCard(order);
                existingCard = $(newCardHtml);
            }

            // CRITICAL: Append to container to enforce order
            // If element is already in DOM, append() moves it to the end
            container.append(existingCard);
        });

        // 2. Remove cards that are no longer in the list
        $('.marked-for-removal').remove();
    }

    function generateOrderCard(order) {
        // Styles based on Status
        let cardBorderClass = '';
        let badgeClass = '';
        let btnAction = '';
        let statusLabel = '';

        if (order.status === 'new') {
            cardBorderClass = 'border-l-8 border-mono-black'; // Black strip
            badgeClass = 'bg-mono-black text-white';
            statusLabel = 'Baru';
            btnAction = `
                <button class="w-full bg-primary text-mono-black font-bold py-3 rounded-xl hover:bg-primary-hover transition-colors flex items-center justify-center gap-2" 
                        onclick="kitchen.updateStatus(${order.id}, 'processing', this)">
                    <i class="bi bi-fire"></i> Proses Pesanan
                </button>
            `;
        } else if (order.status === 'processing') {
            cardBorderClass = 'border-l-8 border-secondary'; // Yellow strip
            badgeClass = 'bg-secondary text-mono-black';
            statusLabel = 'Sedang Dimasak';
            btnAction = `
                <button class="w-full bg-green-500 text-white font-bold py-3 rounded-xl hover:bg-green-600 transition-colors flex items-center justify-center gap-2" 
                        onclick="kitchen.updateStatus(${order.id}, 'done', this)">
                    <i class="bi bi-check-lg"></i> Selesai Masak
                </button>
            `;
        } else if (order.status === 'done') {
            cardBorderClass = 'border-l-8 border-mono-gray opacity-75'; // Gray strip
            badgeClass = 'bg-mono-gray text-white';
            statusLabel = 'Selesai';
            btnAction = `
                <div class="text-center text-green-600 font-bold py-3 border-2 border-green-100 rounded-xl bg-green-50">
                    <i class="bi bi-check-all text-xl"></i><br>Siap Diambil
                </div>
            `;
        }

        // Items List
        let itemsHtml = '<ul class="space-y-4 mb-4">';
        order.items.forEach(item => {
            // Render Toppings
            let toppingsHtml = '';
            if (item.toppings && item.toppings.length > 0) {
                const toppingsList = item.toppings.map(t => {
                    // Show quantity if > 1 (future proof)
                    const qtyLabel = t.quantity > 1 ? `<span class="font-bold text-mono-black">x${t.quantity}</span>` : '';
                    return `
                        <li class="flex items-center gap-2 text-sm text-mono-gray">
                            <i class="bi bi-plus text-xs"></i> ${t.name} ${qtyLabel}
                        </li>
                    `;
                }).join('');
                
                toppingsHtml = `
                    <div class="mt-2 ml-10 pl-3 border-l-2 border-dashed border-mono-light bg-mono-off-white rounded-r-lg p-2">
                        <ul class="space-y-1">
                            ${toppingsList}
                        </ul>
                    </div>
                `;
            }

            itemsHtml += `
                <li class="pb-3 border-b border-mono-light last:border-0">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start flex-1">
                            <span class="font-display font-bold text-mono-black text-2xl w-12 text-center bg-mono-light rounded-lg py-1">${item.quantity}</span>
                            <div class="flex-1 ml-4">
                                <span class="font-bold text-lg text-mono-black block">${item.product_name}</span>
                                ${toppingsHtml}
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                             <span class="font-bold text-lg text-primary block">${item.formatted_subtotal}</span>
                        </div>
                    </div>
                </li>
            `;
        });
        itemsHtml += '</ul>';

        // Order Time
        const timeCreated = order.formatted_created_at ? order.formatted_created_at.split(',')[1] : '--:--';

        // Add ID and Data Attributes for DOM Diffing
        return `
            <div id="order-${order.id}" data-status="${order.status}" class="order-card bg-mono-white rounded-xl shadow-sm hover:shadow-mono-md transition-all duration-300 overflow-hidden ${cardBorderClass} animate-fade-in">
                <!-- Header -->
                <div class="p-5 border-b border-mono-light bg-opacity-50 ${order.status === 'new' ? 'bg-red-50' : ''}">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide ${badgeClass}">
                            ${statusLabel}
                        </span>
                        <span class="text-mono-gray font-mono text-sm flex items-center gap-1">
                            <i class="bi bi-clock"></i> ${timeCreated}
                        </span>
                    </div>
                    <div class="flex justify-between items-end mt-3">
                        <div>
                            <div class="text-xs text-mono-gray uppercase tracking-widest font-bold mb-1">Nomor Antrian</div>
                            <h3 class="font-display text-5xl font-bold text-mono-black leading-none tracking-tight">
                                ${order.queue_number}
                            </h3>
                            <div class="mt-2 text-primary font-bold flex items-center gap-2 text-sm bg-primary-light px-2 py-1 rounded-md inline-block">
                                <i class="bi bi-person-fill"></i> ${order.customer_name || 'Tanpa Nama'}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-mono-gray mb-1">Durasi</div>
                            <div class="text-lg font-mono font-bold text-mono-black elapsed-time-display bg-mono-off-white px-2 py-1 rounded border border-mono-light">
                                ${order.elapsed_time}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-5">
                    ${itemsHtml}
                    
                    <!-- Action Button -->
                    <div class="mt-4 pt-4 border-t-2 border-dashed border-mono-light">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-mono-gray font-bold text-sm">Total Pesanan</span>
                            <span class="text-2xl font-display font-bold text-mono-black">${order.formatted_total}</span>
                        </div>
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

        $('#pending-count').text(`${total} Pesanan Aktif`); 
        $('#badge-new').text(newCount);
        $('#badge-processing').text(processingCount);
        $('#badge-done').text(doneCount);

        // Update Title for browser tab indication
        document.title = total > 0 ? `(${total}) Dapur` : 'Dapur - Bazaar';
    }

    // ==========================================
    // 5. ACTIONS & UTILITIES
    // ==========================================

    // Expose Global Action for Inline OnClick
    window.kitchen = {
        updateStatus: function (orderId, newStatus, btnElement) {
            // UI Feedback
            const originalText = $(btnElement).html();
            $(btnElement).prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Loading...');

            $.ajax({
                url: `/api/orders/${orderId}/status`,
                method: 'PATCH',
                data: {
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // Fetch immediately to sync UI
                    fetchOrders();
                },
                error: function (xhr) {
                    console.error('Update failed:', xhr);
                    alert('Gagal update status. Cek koneksi.');
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
                body: `${order.items_count} Items - ${order.customer_name}`,
                icon: '/favicon.ico'
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
            el.html(`
                <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-mono-gray">Live Sync</span>
            `);
        } else {
            el.html(`
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <span class="text-sm font-medium text-red-500">Offline</span>
            `);
        }
    }

    function updateLastUpdated() {
        const now = new Date();
        $('#last-updated').text(now.toLocaleTimeString());
    }

    function setupEventListeners() {
        // Filter Buttons
        $('.filter-btn').click(function() {
            // UI Toggle
            $('.filter-btn').removeClass('active bg-primary text-mono-black border-primary').addClass('text-mono-gray border-transparent hover:bg-primary-light');
            $(this).addClass('active bg-primary text-mono-black border-primary').removeClass('text-mono-gray border-transparent hover:bg-primary-light');

            // Set Filter
            app.currentFilter = $(this).data('status');
            renderOrders();
        });
    }

    // Run Init
    init();
});
