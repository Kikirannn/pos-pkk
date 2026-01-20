@extends('layouts.app')

@section('title', 'Manajemen Inventori - Kantin Pintar')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">📦 Manajemen Inventori</h2>
            <div class="d-flex gap-2">
                <!-- Add Buttons (Changes dynamically based on active tab) -->
                <button class="btn btn-primary d-flex align-items-center gap-2" id="btn-add-product" data-bs-toggle="modal"
                    data-bs-target="#productModal">
                    <i class="bi bi-plus-circle"></i> Tambah Menu
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2 d-none" id="btn-add-topping"
                    data-bs-toggle="modal" data-bs-target="#toppingModal">
                    <i class="bi bi-plus-circle"></i> Tambah Topping
                </button>
            </div>
        </div>

        <!-- TABS -->
        <ul class="nav nav-tabs nav-fill mb-4" id="inventoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="tab-products" data-bs-toggle="tab"
                    data-bs-target="#products-pane" type="button" role="tab">
                    🍽️ Menu Utama
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="tab-toppings" data-bs-toggle="tab" data-bs-target="#toppings-pane"
                    type="button" role="tab">
                    🧀 Topping tambahan
                </button>
            </li>
        </ul>

        <!-- CONTENT PANES -->
        <div class="tab-content">

            <!-- === PRODUCTS PANE === -->
            <div class="tab-pane fade show active" id="products-pane" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Gambar</th>
                                        <th>Nama Menu</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="products-table-body">
                                    @forelse ($products as $product)
                                        <tr id="product-row-{{ $product->id }}">
                                            <td class="ps-4">
                                                <img src="{{ $product->image_url }}" class="rounded shadow-sm" width="50"
                                                    height="50" style="object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted text-truncate d-block"
                                                    style="max-width: 200px;">{{ $product->description ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if($product->category == 'makanan')
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-egg-fried"></i>
                                                        Makanan</span>
                                                @else
                                                    <span class="badge bg-info text-dark"><i class="bi bi-cup-straw"></i>
                                                        Minuman</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $product->formatted_price }}</td>
                                            <td>
                                                @if($product->is_available)
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-secondary">Habis</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="inventory.editProduct({{ $product->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="inventory.deleteProduct({{ $product->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data menu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === TOPPINGS PANE === -->
            <div class="tab-pane fade" id="toppings-pane" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Nama Topping</th>
                                        <th>Kategori</th>
                                        <th>Harga Tambahan</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="toppings-table-body">
                                    @forelse ($toppings as $topping)
                                        <tr id="topping-row-{{ $topping->id }}">
                                            <td class="ps-4 fw-bold">{{ $topping->name }}</td>
                                            <td>
                                                @if($topping->category == 'makanan')
                                                    <span class="badge bg-warning text-dark">Makanan</span>
                                                @else
                                                    <span class="badge bg-info text-dark">Minuman</span>
                                                @endif
                                            </td>
                                            <td>{{ $topping->formatted_price }}</td>
                                            <td>
                                                @if($topping->is_available)
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-secondary">Habis</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="inventory.editTopping({{ $topping->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="inventory.deleteTopping({{ $topping->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data topping.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= MODALS ================= -->

        <!-- Product Modal -->
        <div class="modal fade" id="productModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="productForm" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="productModalTitle">Tambah Menu Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="productId" name="id">

                            <div class="mb-3">
                                <label class="form-label">Nama Menu</label>
                                <input type="text" class="form-control" name="name" id="productName" required>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category" id="productCategory" required>
                                        <option value="makanan">Makanan</option>
                                        <option value="minuman">Minuman</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" class="form-control" name="price" id="productPrice" min="0"
                                        required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto Menu</label>
                                <input type="file" class="form-control" name="image_file" id="productImage"
                                    accept="image/*">
                                <div class="form-text small">Kosongkan jika tidak ingin mengubah foto (saat edit).</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control" name="description" id="productDesc" rows="2"></textarea>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_available" id="productAvailable"
                                    checked>
                                <label class="form-check-label" for="productAvailable">Menu Tersedia?</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSaveProduct">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Topping Modal -->
        <div class="modal fade" id="toppingModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="toppingForm">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="toppingModalTitle">Tambah Topping</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="toppingId" name="id">

                            <div class="mb-3">
                                <label class="form-label">Nama Topping</label>
                                <input type="text" class="form-control" name="name" id="toppingName" required>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Kategori Produk</label>
                                    <select class="form-select" name="category" id="toppingCategory" required>
                                        <option value="makanan">Makanan</option>
                                        <option value="minuman">Minuman</option>
                                    </select>
                                    <div class="form-text small">Topping ini muncul untuk kategori apa?</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Harga Tambahan (Rp)</label>
                                    <input type="number" class="form-control" name="price" id="toppingPrice" min="0"
                                        required>
                                </div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_available" id="toppingAvailable"
                                    checked>
                                <label class="form-check-label" for="toppingAvailable">Topping Tersedia?</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSaveTopping">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000">
            <div id="inventoryToast" class="toast align-items-center text-white border-0 shadow-lg" role="alert">
                <div class="d-flex">
                    <div class="toast-body">Message</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/inventory.js') }}"></script>
    @endpush
@endsection