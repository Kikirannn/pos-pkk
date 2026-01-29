/**
 * Inventory Management Logic
 */

$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // ==========================================
    // UI HANDLERS
    // ==========================================

    // Tab Switching: Toggle "Add Button" visibility
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.id === 'tab-products') {
            $('#btn-add-product').removeClass('d-none');
            $('#btn-add-topping').addClass('d-none');
        } else {
            $('#btn-add-product').addClass('d-none');
            $('#btn-add-topping').removeClass('d-none');
        }
    });

    // ==========================================
    // MODAL HANDLERS (Custom Tailwind Modals)
    // ==========================================

    function openModal(modalId) {
        $(`#${modalId}`).removeClass('hidden').addClass('flex');
    }

    function closeModal(modalId) {
        $(`#${modalId}`).addClass('hidden').removeClass('flex');
    }

    // Add Button Handlers
    $('#btn-add-product').click(function() {
        resetProductForm();
        openModal('productModal');
    });

    $('#btn-add-topping').click(function() {
        resetToppingForm();
        openModal('toppingModal');
    });

    // Close Button Handlers
    $('.modal-close').click(function() {
        const modalId = $(this).closest('.modal-overlay').attr('id');
        closeModal(modalId);
    });

    // Close on Outside Click
    $('.modal-overlay').on('mousedown', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });

    // Remove old Bootstrap event listeners if any were causing issues
    $('#productModal, #toppingModal').off('show.bs.modal');

    // ==========================================
    // PRODUCT CRUD
    // ==========================================

    $('#productForm').submit(function (e) {
        e.preventDefault();

        const id = $('#productId').val();
        const isEdit = !!id;
        const url = isEdit ? `/inventory/products/${id}` : '/inventory/products';

        const formData = new FormData(this);
        // Explicitly set checkbox value
        formData.set('is_available', $('#productAvailable').is(':checked') ? '1' : '0');
        // Clean Price (remove dots)
        formData.set('price', Utils.cleanNumber($('#productPrice').val()));

        // Laravel spoofing for PUT/PATCH with file upload
        if (isEdit) {
            formData.append('_method', 'POST'); // Keeping POST but Controller handles logic
        }

        $.ajax({
            url: url,
            method: 'POST', // Always POST for FormData
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                showToast(res.message, 'success');
                closeModal('productModal');
                setTimeout(() => window.location.reload(), 500); // Reload to refresh table
            },
            error: function (xhr) {
                console.error(xhr);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    for (let field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                    showToast(errorMessage, 'error');
                } else {
                    showToast('Gagal menyimpan menu. Silakan coba lagi.', 'error');
                }
            }
        });
    });

    // ==========================================
    // TOPPING CRUD
    // ==========================================

    $('#toppingForm').submit(function (e) {
        e.preventDefault();

        const id = $('#toppingId').val();
        const isEdit = !!id;
        const url = isEdit ? `/inventory/toppings/${id}` : '/inventory/toppings';
        const method = isEdit ? 'PATCH' : 'POST';

        // Serialize Form
        const data = {
            name: $('#toppingName').val(),
            category: $('#toppingCategory').val(),
            price: Utils.cleanNumber($('#toppingPrice').val()),
            is_available: $('#toppingAvailable').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                showToast(res.message, 'success');
                closeModal('toppingModal');
                setTimeout(() => window.location.reload(), 500);
            },
            error: function (xhr) {
                console.error(xhr);
                showToast('Gagal menyimpan topping.', 'error');
            }
        });
    });


    // ==========================================
    // UTILITIES
    // ==========================================

    window.inventory = {
        editProduct: function (id) {
            console.log('Fetching product details for ID:', id);
            $('#global-loader').fadeIn(100); // Force show loader

            // Fetch Details First
            $.get(`/inventory/products/${id}`)
                .done(function (product) {
                    console.log('Product data received:', product);
                    
                    $('#productId').val(product.id);
                    $('#productName').val(product.name);
                    $('#productCategory').val(product.category);
                    // Use Utils.formatNumber but ensure it treats the input as float/integer correctly
                    // product.price comes from API as "25000.00" (string) or 25000 (number)
                    // We need to parse it to integer before formatting to avoid double precision issues
                    let price = parseInt(product.price); 
                    $('#productPrice').val(Utils.formatNumber(price));
                    $('#productStock').val(product.stock);
                    $('#productDesc').val(product.description || '');
                    $('#productAvailable').prop('checked', !!product.is_available);

                    $('#productModalTitle').text('Edit Menu');
                    
                    openModal('productModal');
                })
                .fail(function(xhr) {
                    console.error('Error fetching product:', xhr);
                    showToast('Gagal memuat data menu. Cek console untuk detail.', 'error');
                })
                .always(function() {
                    $('#global-loader').fadeOut(200); // Force hide loader
                });
        },

        deleteProduct: function (id) {
            if (!confirm('Yakin ingin menghapus menu ini?')) return;
            
            const maxRetries = 3;
            let attempt = 0;

            const performDelete = () => {
                attempt++;
                $('#global-loader').fadeIn(100);

                $.ajax({
                    url: `/inventory/products/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (res) {
                        $('#global-loader').fadeOut(200);
                        showToast(res.message, 'success');
                        $(`#product-row-${id}`).fadeOut();
                    },
                    error: function (xhr) {
                        console.error(`Attempt ${attempt} failed:`, xhr);
                        
                        // Retry on 5xx errors or connection issues (status 0)
                        if (attempt < maxRetries && (xhr.status >= 500 || xhr.status === 0)) {
                            const delay = Math.pow(2, attempt) * 1000; // 2s, 4s, 8s
                            showToast(`Gagal. Mencoba lagi dalam ${delay/1000} detik...`, 'warning');
                            setTimeout(performDelete, delay);
                        } else {
                            $('#global-loader').fadeOut(200);
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                showToast(xhr.responseJSON.message, 'error');
                            } else {
                                showToast('Gagal menghapus menu. Silakan coba lagi.', 'error');
                            }
                        }
                    }
                });
            };

            performDelete();
        },

        editTopping: function (id) {
            console.log('Fetching topping details for ID:', id);
            $('#global-loader').fadeIn(100);

            $.get(`/inventory/toppings/${id}`)
                .done(function (topping) {
                    console.log('Topping data received:', topping);

                    $('#toppingId').val(topping.id);
                    $('#toppingName').val(topping.name);
                    $('#toppingCategory').val(topping.category);
                    let price = parseInt(topping.price);
                    $('#toppingPrice').val(Utils.formatNumber(price));
                    $('#toppingAvailable').prop('checked', !!topping.is_available);

                    $('#toppingModalTitle').text('Edit Topping');
                    
                    openModal('toppingModal');
                })
                .fail(function(xhr) {
                    console.error('Error fetching topping:', xhr);
                    showToast('Gagal memuat data topping.', 'error');
                })
                .always(function() {
                    $('#global-loader').fadeOut(200);
                });
        },

        deleteTopping: function (id) {
            if (!confirm('Yakin ingin menghapus topping ini?')) return;
            
            const maxRetries = 3;
            let attempt = 0;

            const performDelete = () => {
                attempt++;
                $('#global-loader').fadeIn(100);

                $.ajax({
                    url: `/inventory/toppings/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (res) {
                        $('#global-loader').fadeOut(200);
                        showToast(res.message, 'success');
                        $(`#topping-row-${id}`).fadeOut();
                    },
                    error: function (xhr) {
                        console.error(`Attempt ${attempt} failed:`, xhr);
                        
                        if (attempt < maxRetries && (xhr.status >= 500 || xhr.status === 0)) {
                            const delay = Math.pow(2, attempt) * 1000;
                            showToast(`Gagal. Mencoba lagi dalam ${delay/1000} detik...`, 'warning');
                            setTimeout(performDelete, delay);
                        } else {
                            $('#global-loader').fadeOut(200);
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                showToast(xhr.responseJSON.message, 'error');
                            } else {
                                showToast('Gagal menghapus topping. Silakan coba lagi.', 'error');
                            }
                        }
                    }
                });
            };

            performDelete();
        }
    };

    function resetProductForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#productStock').val('0');
        $('#productModalTitle').text('Tambah Menu Baru');
    }

    function resetToppingForm() {
        $('#toppingForm')[0].reset();
        $('#toppingId').val('');
        $('#toppingModalTitle').text('Tambah Topping');
    }

    function showToast(message, type = 'success') {
        const el = $('#inventoryToast');
        const body = el.find('.toast-body');

        body.text(message);
        el.removeClass('bg-success bg-danger');
        el.addClass(type === 'success' ? 'bg-success' : 'bg-danger');

        new bootstrap.Toast(el).show();
    }
});
