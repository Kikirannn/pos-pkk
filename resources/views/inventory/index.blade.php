@extends('layouts.app')

@section('title', 'Manajemen Inventori - Bazar XII RPL 2')

@section('content')
<div class="min-h-screen bg-mono-off-white py-8">
    <div class="container mx-auto px-4">
        
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="font-display text-4xl md:text-5xl font-bold text-mono-black mb-2">Inventori</h1>
                <p class="text-mono-gray">Kelola menu dan topping untuk bazaar</p>
            </div>
            <div class="flex gap-2">
                <button id="btn-add-product" class="bg-primary text-mono-black px-6 py-3 rounded-full font-medium uppercase tracking-wide hover:bg-primary-hover hover:text-white transition-all flex items-center gap-2">
                    <i class="bi bi-plus-circle"></i> Tambah Menu
                </button>
                <button id="btn-add-topping" class="hidden bg-primary text-mono-black px-6 py-3 rounded-full font-medium uppercase tracking-wide hover:bg-primary-hover hover:text-white transition-all flex items-center gap-2">
                    <i class="bi bi-plus-circle"></i> Tambah Topping
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-8 border-b-2 border-primary-light overflow-x-auto">
            <button class="tab-btn active px-6 py-3 font-medium uppercase tracking-wide transition-all whitespace-nowrap" data-tab="products">
                🍽️ Menu Utama
            </button>
            <button class="tab-btn px-6 py-3 font-medium uppercase tracking-wide transition-all whitespace-nowrap" data-tab="toppings">
                🧀 Topping
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            
            <!-- Products Pane -->
            <div id="products-pane" class="tab-pane active">
                <div class="bg-mono-white rounded-xl border-2 border-primary-light shadow-mono overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-mono-off-white border-b-2 border-primary-light hidden md:table-header-group">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Gambar</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Nama Menu</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-mono-gray uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="products-table-body" class="block md:table-row-group divide-y divide-primary-light">
                                @forelse ($products as $product)
                                    <tr id="product-row-{{ $product->id }}" class="block md:table-row hover:bg-mono-off-white transition-colors p-4 md:p-0 border-b md:border-b-0 border-primary-light relative">
                                        <!-- Mobile Layout: Flex Container -->
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-4 md:mb-0 text-center md:text-left">
                                            <img src="{{ $product->image_url }}" class="w-full h-48 md:w-16 md:h-16 object-cover rounded-lg shadow-sm transition-transform hover:scale-110 mx-auto md:mx-0" alt="{{ $product->name }}">
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 text-center md:text-left">
                                            <div class="font-bold text-mono-black text-xl md:text-base">{{ $product->name }}</div>
                                            <div class="text-sm text-mono-gray truncate max-w-xs mx-auto md:mx-0">{{ $product->description ?? '-' }}</div>
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 text-center md:text-left">
                                            <span class="md:hidden font-bold text-mono-gray text-xs uppercase mr-2">Kategori:</span>
                                            @if($product->category == 'makanan')
                                                <span class="px-3 py-1 bg-primary text-mono-black text-xs font-medium rounded-full uppercase tracking-wide inline-block">
                                                    <i class="bi bi-egg-fried"></i> Makanan
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-secondary text-mono-black text-xs font-medium rounded-full uppercase tracking-wide inline-block">
                                                    <i class="bi bi-cup-straw"></i> Minuman
                                                </span>
                                            @endif
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 font-bold text-mono-black text-lg md:text-base text-center md:text-left">
                                            {{ $product->formatted_price }}
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 text-center md:text-left">
                                            <span class="md:hidden font-bold text-mono-gray text-xs uppercase mr-2">Stok:</span>
                                            <span class="font-bold text-mono-black {{ $product->stock <= 5 ? 'text-red-500' : '' }}">{{ $product->stock }}</span>
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-4 md:mb-0 text-center md:text-left">
                                            @if($product->is_available)
                                                <span class="px-3 py-1 bg-primary text-mono-black text-xs font-medium rounded-full inline-block">Tersedia</span>
                                            @else
                                                <span class="px-3 py-1 bg-mono-light text-mono-gray text-xs font-medium rounded-full inline-block">Habis</span>
                                            @endif
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell text-center md:text-right">
                                            <div class="flex justify-center md:justify-end gap-2 w-full">
                                                <button onclick="inventory.editProduct({{ $product->id }})" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2 md:w-10 md:h-10 border-2 border-primary hover:bg-primary hover:text-mono-black rounded-full transition-all text-sm font-bold md:font-normal">
                                                    <i class="bi bi-pencil mr-2 md:mr-0"></i> <span class="md:hidden">Edit</span>
                                                </button>
                                                <button onclick="inventory.deleteProduct({{ $product->id }})" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2 md:w-10 md:h-10 border-2 border-primary hover:bg-primary hover:text-mono-black rounded-full transition-all text-sm font-bold md:font-normal">
                                                    <i class="bi bi-trash mr-2 md:mr-0"></i> <span class="md:hidden">Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-mono-gray">
                                            <i class="bi bi-inbox text-4xl mb-2"></i>
                                            <p>Belum ada data menu.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Toppings Pane -->
            <div id="toppings-pane" class="tab-pane hidden">
                <div class="bg-mono-white rounded-xl border-2 border-mono-light shadow-mono overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-mono-off-white border-b-2 border-mono-light hidden md:table-header-group">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Nama Topping</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Harga Tambahan</th>
                                    <th class="px-6 py-4 text-xs font-bold text-mono-gray uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-mono-gray uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="toppings-table-body" class="block md:table-row-group divide-y divide-mono-light">
                                @forelse ($toppings as $topping)
                                    <tr id="topping-row-{{ $topping->id }}" class="block md:table-row hover:bg-mono-off-white transition-colors p-4 md:p-0 border-b md:border-b-0 border-mono-light relative">
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 text-center md:text-left">
                                            <span class="font-bold text-mono-black text-lg md:text-base">{{ $topping->name }}</span>
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 text-center md:text-left">
                                            <span class="md:hidden font-bold text-mono-gray text-xs uppercase mr-2">Kategori:</span>
                                            @if($topping->category == 'makanan')
                                                <span class="px-3 py-1 bg-mono-dark text-mono-white text-xs font-medium rounded-full uppercase tracking-wide inline-block">Makanan</span>
                                            @else
                                                <span class="px-3 py-1 bg-mono-gray text-mono-white text-xs font-medium rounded-full uppercase tracking-wide inline-block">Minuman</span>
                                            @endif
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-2 md:mb-0 font-bold text-mono-black text-center md:text-left">
                                            <span class="md:hidden font-bold text-mono-gray text-xs uppercase mr-2">Harga:</span>
                                            {{ $topping->formatted_price }}
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell mb-4 md:mb-0 text-center md:text-left">
                                            @if($topping->is_available)
                                                <span class="px-3 py-1 bg-mono-black text-mono-white text-xs font-medium rounded-full inline-block">Tersedia</span>
                                            @else
                                                <span class="px-3 py-1 bg-mono-light text-mono-gray text-xs font-medium rounded-full inline-block">Habis</span>
                                            @endif
                                        </td>
                                        <td class="md:px-6 md:py-4 block md:table-cell text-center md:text-right">
                                            <div class="flex justify-center md:justify-end gap-2 w-full">
                                                <button onclick="inventory.editTopping({{ $topping->id }})" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2 md:w-10 md:h-10 border-2 border-mono-dark hover:bg-mono-dark hover:text-mono-white rounded-full transition-all text-sm font-bold md:font-normal">
                                                    <i class="bi bi-pencil mr-2 md:mr-0"></i> <span class="md:hidden">Edit</span>
                                                </button>
                                                <button onclick="inventory.deleteTopping({{ $topping->id }})" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2 md:w-10 md:h-10 border-2 border-mono-dark hover:bg-mono-dark hover:text-mono-white rounded-full transition-all text-sm font-bold md:font-normal">
                                                    <i class="bi bi-trash mr-2 md:mr-0"></i> <span class="md:hidden">Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-mono-gray">
                                            <i class="bi bi-inbox text-4xl mb-2"></i>
                                            <p>Belum ada data topping.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Product Modal -->
<div id="productModal" class="modal-overlay hidden fixed inset-0 bg-mono-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-mono-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-mono-xl">
        <form id="productForm" enctype="multipart/form-data">
            <!-- Header -->
            <div class="sticky top-0 bg-mono-black text-mono-white p-6 flex justify-between items-center rounded-t-2xl">
                <h3 class="font-display text-2xl font-bold" id="productModalTitle">Tambah Menu Baru</h3>
                <button type="button" class="modal-close text-mono-white hover:text-mono-light text-2xl">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <input type="hidden" id="productId" name="id">

                <div>
                    <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Nama Menu</label>
                    <input type="text" name="name" id="productName" required class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Kategori</label>
                        <select name="category" id="productCategory" required class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors">
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Harga (Rp)</label>
                        <input type="text" name="price" id="productPrice" placeholder="0" required class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors input-currency">
                    </div>
                    <div>
                        <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Stok</label>
                        <input type="number" name="stock" id="productStock" value="0" required min="0" class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Foto Menu</label>
                    <input type="file" name="image_file" id="productImage" accept="image/*" class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors">
                    <p class="text-xs text-mono-gray mt-1">Kosongkan jika tidak ingin mengubah foto (saat edit)</p>
                </div>

                <div>
                    <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" id="productDesc" rows="3" class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_available" id="productAvailable" checked class="w-5 h-5 accent-mono-black">
                    <label for="productAvailable" class="text-mono-black font-medium">Menu Tersedia?</label>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-mono-off-white p-6 flex gap-3 rounded-b-2xl">
                <button type="button" class="modal-close flex-1 border-2 border-mono-dark text-mono-dark py-3 rounded-full font-bold uppercase tracking-wide hover:bg-mono-dark hover:text-mono-white transition-all">
                    Batal
                </button>
                <button type="submit" id="btnSaveProduct" class="flex-1 bg-mono-black text-mono-white py-3 rounded-full font-bold uppercase tracking-wide hover:bg-mono-charcoal transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Topping Modal -->
<div id="toppingModal" class="modal-overlay hidden fixed inset-0 bg-mono-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-mono-white rounded-2xl max-w-lg w-full shadow-mono-xl">
        <form id="toppingForm">
            <!-- Header -->
            <div class="bg-mono-black text-mono-white p-6 flex justify-between items-center rounded-t-2xl">
                <h3 class="font-display text-2xl font-bold" id="toppingModalTitle">Tambah Topping</h3>
                <button type="button" class="modal-close text-mono-white hover:text-mono-light text-2xl">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <input type="hidden" id="toppingId" name="id">

                <div>
                    <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Nama Topping</label>
                    <input type="text" name="name" id="toppingName" required class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Kategori Produk</label>
                        <select name="category" id="toppingCategory" required class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors">
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>
                        <p class="text-xs text-mono-gray mt-1">Topping untuk kategori apa?</p>
                    </div>
                    <div>
                        <label class="block text-mono-gray text-sm uppercase tracking-wide mb-2">Harga Tambahan (Rp)</label>
                        <input type="text" name="price" id="toppingPrice" placeholder="0" required class="w-full border-2 border-mono-light rounded-lg px-4 py-3 focus:outline-none focus:border-mono-dark transition-colors input-currency">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_available" id="toppingAvailable" checked class="w-5 h-5 accent-mono-black">
                    <label for="toppingAvailable" class="text-mono-black font-medium">Topping Tersedia?</label>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-mono-off-white p-6 flex gap-3 rounded-b-2xl">
                <button type="button" class="modal-close flex-1 border-2 border-mono-dark text-mono-dark py-3 rounded-full font-bold uppercase tracking-wide hover:bg-mono-dark hover:text-mono-white transition-all">
                    Batal
                </button>
                <button type="submit" id="btnSaveTopping" class="flex-1 bg-mono-black text-mono-white py-3 rounded-full font-bold uppercase tracking-wide hover:bg-mono-charcoal transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="inventoryToast" class="hidden fixed top-6 right-6 z-50 bg-mono-black text-mono-white px-6 py-4 rounded-lg shadow-mono-xl flex items-center gap-3">
    <span id="toast-message">Message</span>
    <button onclick="this.parentElement.classList.add('hidden')" class="text-mono-white hover:text-mono-light">
        <i class="bi bi-x-lg"></i>
    </button>
</div>

@push('scripts')
<script src="{{ asset('js/inventory.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                
                // Update button states
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update panes
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('active');
                });
                document.getElementById(tab + '-pane').classList.remove('hidden');
                document.getElementById(tab + '-pane').classList.add('active');
                
                // Show/hide appropriate add button
                if (tab === 'products') {
                    document.getElementById('btn-add-product').classList.remove('hidden');
                    document.getElementById('btn-add-topping').classList.add('hidden');
                } else {
                    document.getElementById('btn-add-product').classList.add('hidden');
                    document.getElementById('btn-add-topping').classList.remove('hidden');
                }
                
                // GSAP animation
                gsap.from('.tab-pane.active', {
                    opacity: 0,
                    y: 10,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });
        });

        // Modal controls
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.modal-overlay').classList.add('hidden');
            });
        });

        // Open product modal
        document.getElementById('btn-add-product').addEventListener('click', function() {
            document.getElementById('productModal').classList.remove('hidden');
        });

        // Open topping modal
        document.getElementById('btn-add-topping').addEventListener('click', function() {
            document.getElementById('toppingModal').classList.remove('hidden');
        });

        // GSAP entrance animation
        gsap.from('table tbody tr', {
            opacity: 0,
            y: 10,
            stagger: 0.05,
            duration: 0.4,
            ease: 'power2.out'
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Tab Buttons */
    .tab-btn {
        color: #7A7A7A;
        border-bottom: 3px solid transparent;
    }
    .tab-btn.active {
        color: #0A0A0A;
        border-bottom-color: #0A0A0A;
    }
    .tab-btn:hover:not(.active) {
        color: #2A2A2A;
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
</style>
@endpush

@endsection