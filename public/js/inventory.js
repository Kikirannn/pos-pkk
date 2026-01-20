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

    // Reset Forms on Modal Open (for Add Mode)
    $('#productModal').on('show.bs.modal', function (e) {
        // Only reset if it's not opened via Edit button (Edit button sets a flag or pre-fills, logic below)
        const relatedTarget = $(e.relatedTarget);
        if (relatedTarget.attr('id') === 'btn-add-product') {
            resetProductForm();
        }
    });

    $('#toppingModal').on('show.bs.modal', function (e) {
        const relatedTarget = $(e.relatedTarget);
        if (relatedTarget.attr('id') === 'btn-add-topping') {
            resetToppingForm();
        }
    });

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
                $('#productModal').modal('hide');
                setTimeout(() => window.location.reload(), 500); // Reload to refresh table
            },
            error: function (xhr) {
                console.error(xhr);
                showToast('Gagal menyimpan menu.', 'error');
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
                $('#toppingModal').modal('hide');
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
            // Fetch Details First
            $.get(`/inventory/products/${id}`, function (product) {
                $('#productId').val(product.id);
                $('#productName').val(product.name);
                $('#productCategory').val(product.category);
                $('#productPrice').val(Utils.formatNumber(product.price));
                $('#productDesc').val(product.description || '');
                $('#productAvailable').prop('checked', product.is_available);

                $('#productModalTitle').text('Edit Menu');
                new bootstrap.Modal('#productModal').show();
            });
        },

        deleteProduct: function (id) {
            if (!confirm('Yakin ingin menghapus menu ini?')) return;

            $.ajax({
                url: `/inventory/products/${id}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    showToast(res.message, 'success');
                    $(`#product-row-${id}`).fadeOut();
                },
                error: function () {
                    showToast('Gagal menghapus menu.', 'error');
                }
            });
        },

        editTopping: function (id) {
            $.get(`/inventory/toppings/${id}`, function (topping) {
                $('#toppingId').val(topping.id);
                $('#toppingName').val(topping.name);
                $('#toppingCategory').val(topping.category);
                $('#toppingPrice').val(Utils.formatNumber(topping.price));
                $('#toppingAvailable').prop('checked', topping.is_available);

                $('#toppingModalTitle').text('Edit Topping');
                new bootstrap.Modal('#toppingModal').show();
            });
        },

        deleteTopping: function (id) {
            if (!confirm('Yakin ingin menghapus topping ini?')) return;

            $.ajax({
                url: `/inventory/toppings/${id}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    showToast(res.message, 'success');
                    $(`#topping-row-${id}`).fadeOut();
                },
                error: function () {
                    showToast('Gagal menghapus topping.', 'error');
                }
            });
        }
    };

    function resetProductForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
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
