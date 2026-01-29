@extends('layouts.app')

@section('title', 'Kitchen Display System - Bazar XII RPL 2')

@section('content')
<div class="min-h-screen bg-mono-off-white pb-12">
    
    <!-- Header Bar (Sticky) -->
    <div class="sticky top-0 z-30 bg-mono-white border-b-2 border-primary-light shadow-mono">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center mb-4">
                <!-- Title & Counter -->
                <div class="flex items-center gap-4">
                    <h1 class="font-display text-3xl md:text-4xl font-bold text-mono-black flex items-center gap-2">
                        <i class="bi bi-fire text-primary"></i> Dapur Monitor
                    </h1>
                    <span id="pending-count" class="bg-primary text-mono-black px-4 py-2 rounded-full text-sm font-bold">
                        0 Menunggu
                    </span>
                </div>

                <!-- Status & Time -->
                <div class="flex items-center gap-4">
                    <div id="connection-status" class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-mono-gray">Live Sync</span>
                    </div>
                    <div class="text-sm text-mono-gray border-l-2 border-primary-light pl-4">
                        <span id="last-updated">--:--:--</span>
                    </div>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-2 overflow-x-auto pb-2">
                <button class="filter-btn active px-6 py-2 rounded-full border-2 border-primary font-medium text-sm uppercase tracking-wide transition-all whitespace-nowrap" data-status="all">
                    Semua
                </button>
                <button class="filter-btn px-6 py-2 rounded-full border-2 border-primary font-medium text-sm uppercase tracking-wide transition-all whitespace-nowrap" data-status="new">
                    🔴 Baru <span id="badge-new" class="ml-2 bg-primary text-mono-black px-2 py-0.5 rounded-full text-xs">0</span>
                </button>
                <button class="filter-btn px-6 py-2 rounded-full border-2 border-primary font-medium text-sm uppercase tracking-wide transition-all whitespace-nowrap" data-status="processing">
                    🟡 Proses <span id="badge-processing" class="ml-2 bg-primary text-mono-black px-2 py-0.5 rounded-full text-xs">0</span>
                </button>
                <button class="filter-btn px-6 py-2 rounded-full border-2 border-primary font-medium text-sm uppercase tracking-wide transition-all whitespace-nowrap" data-status="done">
                    ✅ Selesai <span id="badge-done" class="ml-2 bg-primary text-mono-black px-2 py-0.5 rounded-full text-xs">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="container mx-auto px-4 py-8">
        <div id="orders-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Loading Skeleton -->
            @for ($i = 0; $i < 3; $i++)
                <div class="skeleton-loader bg-mono-white rounded-xl p-6 border-2 border-primary-light">
                    <div class="flex justify-between mb-4">
                        <div class="h-8 bg-mono-light rounded w-24 animate-pulse"></div>
                        <div class="h-8 bg-mono-light rounded w-20 animate-pulse"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-4 bg-mono-light rounded w-full animate-pulse"></div>
                        <div class="h-4 bg-mono-light rounded w-3/4 animate-pulse"></div>
                        <div class="h-4 bg-mono-light rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
            @endfor

            <!-- Empty State (Hidden by default) -->
            <div id="empty-state" class="hidden col-span-full text-center py-20">
                <i class="bi bi-check-circle text-8xl text-mono-gray-light mb-4"></i>
                <h2 class="font-display text-3xl font-bold text-mono-gray mb-2">Semua Pesanan Selesai!</h2>
                <p class="text-mono-gray">Bersantai sejenak sambil menunggu pesanan baru.</p>
            </div>
        </div>
    </div>

</div>

<!-- Audio Notification -->
<audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

@push('styles')
<style>
    /* Filter Buttons */
    .filter-btn {
        color: #7A7A7A;
        background-color: transparent;
        border-color: #FFA726; /* Orange Border */
    }
    .filter-btn.active {
        background-color: #FFA726; /* Orange */
        color: #3E2723; /* Dark Brown Text */
        border-color: #FFA726;
    }
    .filter-btn:not(.active):hover {
        background-color: #FFF8E1; /* Light Cream */
        color: #3E2723;
    }

    /* Order Cards */
    .order-card {
        transition: all 0.3s ease;
        animation: slideIn 0.4s ease;
    }

    .order-card-new {
        border-left: 6px solid #0A0A0A;
    }

    .order-card-processing {
        border-left: 6px solid #4A4A4A;
    }

    .order-card-done {
        border-left: 6px solid #ABABAB;
        opacity: 0.7;
    }

    .order-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    /* Order Number */
    .order-number {
        font-size: 3rem;
        letter-spacing: -2px;
        line-height: 1;
    }

    /* Item List */
    .item-list {
        font-size: 1.125rem;
    }

    .topping-list {
        font-size: 0.875rem;
        color: #7A7A7A;
        font-style: italic;
    }

    /* Animations */
    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .fade-out {
        animation: fadeOut 0.5s forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-new {
        background-color: #0A0A0A;
        color: #FAFAFA;
    }

    .status-processing {
        background-color: #4A4A4A;
        color: #FAFAFA;
    }

    .status-done {
        background-color: #E5E5E5;
        color: #0A0A0A;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/dapur.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dapur view loaded with monochrome theme.');
        
        // GSAP Animations for order cards
        gsap.from('.skeleton-loader', {
            opacity: 0,
            y: 20,
            stagger: 0.1,
            duration: 0.6,
            ease: 'power2.out'
        });

        // Filter logic is handled entirely by dapur.js to ensure state consistency
    });
</script>
@endpush

@endsection