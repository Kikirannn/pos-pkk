@extends('layouts.app')

@section('title', 'Kitchen Display System - Kantin Pintar')

@section('content')
    <div class="container-fluid pb-5">

        <!-- 1. Header Bar (Fixed) -->
        <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 70px; z-index: 1020;">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="mb-0 fw-bold"><i class="bi bi-fire text-danger me-2"></i>Dapur Monitor</h4>
                        <span class="badge bg-danger rounded-pill px-3 py-2 fs-6" id="pending-count">
                            0 Menunggu
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <!-- Connection Status -->
                        <div id="connection-status" class="d-flex align-items-center text-success small fw-bold">
                            <div class="spinner-grow spinner-grow-sm me-2" role="status"></div>
                            <span>Live Sync</span>
                        </div>

                        <!-- Last Updated -->
                        <div class="text-muted small border-start ps-3">
                            Updated: <span id="last-updated">--:--:--</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Filter Buttons -->
                <div class="mt-3 border-top pt-2">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="statusFilter" id="filterAll" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary" for="filterAll">Semua</label>

                        <input type="radio" class="btn-check" name="statusFilter" id="filterNew" autocomplete="off">
                        <label class="btn btn-outline-danger" for="filterNew">
                            🔴 Pesanan Baru <span class="badge bg-white text-danger ms-1" id="badge-new">0</span>
                        </label>

                        <input type="radio" class="btn-check" name="statusFilter" id="filterProcessing" autocomplete="off">
                        <label class="btn btn-outline-warning" for="filterProcessing">
                            🟡 Sedang Diproses <span class="badge bg-white text-warning ms-1" id="badge-processing">0</span>
                        </label>

                        <input type="radio" class="btn-check" name="statusFilter" id="filterDone" autocomplete="off">
                        <label class="btn btn-outline-success" for="filterDone">
                            ✅ Selesai <span class="badge bg-white text-success ms-1" id="badge-done">0</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Orders Grid -->
        <div id="orders-grid" class="row g-4">
            <!-- Loading Skeleton -->
            @for ($i = 0; $i < 3; $i++)
                <div class="col-md-6 col-xl-4 skeleton-loader">
                    <div class="card h-100 border-0 shadow-sm" aria-hidden="true">
                        <div class="card-header bg-secondary bg-opacity-10 py-3">
                            <div class="d-flex justify-content-between">
                                <span class="placeholder col-4 py-3 rounded"></span>
                                <span class="placeholder col-3 py-3 rounded"></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="placeholder-glow">
                                <span class="placeholder col-7"></span>
                                <span class="placeholder col-4"></span>
                                <span class="placeholder col-4"></span>
                                <span class="placeholder col-6"></span>
                            </p>
                        </div>
                    </div>
                </div>
            @endfor

            <!-- Empty State (Hidden by default) -->
            <div id="empty-state" class="col-12 text-center py-5 d-none">
                <div class="py-5">
                    <i class="bi bi-check-circle-fill text-success display-1"></i>
                    <h2 class="mt-4 fw-bold text-muted">Semua Pesanan Selesai!</h2>
                    <p class="text-muted">Bersantai sejenak sambil menunggu pesanan baru.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Audio Notification -->
    <audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    @push('styles')
        <style>
            /* Card Styles for different statuses */
            .card-new {
                border-left: 6px solid var(--danger-color) !important;
                animation: slideIn 0.3s ease;
            }

            .card-new .card-header {
                background-color: rgba(239, 71, 111, 0.1);
                /* Danger Tint */
            }

            .card-processing {
                border-left: 6px solid var(--warning-color) !important;
                animation: fadeIn 0.3s ease;
            }

            .card-processing .card-header {
                background-color: rgba(255, 209, 102, 0.2);
                /* Warning Tint */
            }

            /* Large Text for Readability */
            .order-number {
                font-size: 2.5rem;
                letter-spacing: -1px;
                line-height: 1;
            }

            .item-list {
                font-size: 1.25rem;
            }

            .topping-list {
                font-size: 1rem;
                color: #6c757d;
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

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
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
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('js/dapur.js') }}"></script>
        {{-- JS Logic implemented in external file --}}
        <script>
            console.log('Dapur view loaded.');
        </script>
    @endpush

@endsection