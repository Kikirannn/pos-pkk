<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi POS Kantin SMK">

    <title>@yield('title', config('app.name', 'POS Kantin'))</title>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Styles -->
    <style>
        :root {
            /* Color Scheme Configuration */
            --primary-color: #FF6B35;
            /* Orange */
            --secondary-color: #004E89;
            /* Blue */
            --success-color: #06D6A0;
            /* Green */
            --warning-color: #FFD166;
            /* Yellow */
            --danger-color: #EF476F;
            /* Red/Pink */
            --dark-color: #1A1A1D;
            /* Almost Black */
            --light-color: #F8F9FA;
            /* White/Grey */

            /* Bootstrap Variable Overrides */
            --bs-primary: var(--primary-color);
            --bs-secondary: var(--secondary-color);
            --bs-success: var(--success-color);
            --bs-warning: var(--warning-color);
            --bs-danger: var(--danger-color);
            --bs-body-font-family: 'Inter', sans-serif;
        }

        body {
            font-family: var(--bs-body-font-family);
            background-color: #F2F4F8;
            /* Soft background */
            color: var(--dark-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Typography & Utilities */
        .fw-medium {
            font-weight: 500;
        }

        .fw-semibold {
            font-weight: 600;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom Button Styles (Solid) */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #e85a2d;
            border-color: #e85a2d;
            transform: translateY(-1px);
        }

        /* Navbar Styling */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 700;
        }

        /* Main Content Wrapper */
        main {
            flex: 1;
        }

        /* Global Loader */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
            /* Hidden by default */
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        /* Flex Container for centering (applied via JS or nested div) */
        .loader-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            min-width: 200px;
        }

        .loader-icon-container {
            position: relative;
            width: 60px;
            height: 60px;
            margin: 0 auto 1.5rem;
        }

        .loader-icon {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            font-size: 3.5rem;
            line-height: 1;
        }

        .icon-base {
            color: #e9ecef;
        }

        .icon-fill {
            color: var(--primary-color);
            -webkit-background-clip: text;
            /* Optional gradient text if needed, but solid color is fine */
            clip-path: inset(100% 0 0 0);
            animation: fillUp 2s infinite ease-in-out;
        }

        @keyframes fillUp {
            0% {
                clip-path: inset(100% 0 0 0);
            }

            40% {
                clip-path: inset(0 0 0 0);
            }

            80% {
                clip-path: inset(0 0 0 0);
                opacity: 1;
            }

            100% {
                clip-path: inset(0 0 0 0);
                opacity: 0;
            }
        }
    </style>

    @stack('styles')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Global Loading Overlay -->
    <div id="global-loader">
        <div class="loader-content">
            <div class="loader-icon-container">
                <i class="bi bi-fire loader-icon icon-base"></i>
                <i class="bi bi-fire loader-icon icon-fill"></i>
            </div>
            <h5 class="mt-2 text-dark fw-bold">Memproses...</h5>
            <p class="text-muted small mb-0">Mohon tunggu sebentar</p>
        </div>
    </div>

    <!-- Navbar (Global) -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-cup-hot-fill fs-4"></i>
                <span>POS Kantin</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-medium">
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('home') ? 'active text-primary' : '' }}"
                            href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('kasir.*') ? 'active text-primary' : '' }}"
                            href="{{ route('kasir.index') }}">
                            <i class="bi bi-tablet landscape me-1"></i> Kasir
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('dapur.*') ? 'active text-primary' : '' }}"
                            href="{{ route('dapur.index') }}">
                            <i class="bi bi-display me-1"></i> Dapur
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-muted small">
            <p class="mb-1">&copy; {{ date('Y') }} <strong>SMK Kantin Point of Sale</strong></p>
            <p class="mb-0">Dibuat untuk Project PKK</p>
        </div>
    </footer>

    <!-- Essential Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/utils.js') }}"></script>

    <!-- Global Helper Script -->
    <script>
        // Setup CSRF Token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global AJAX Loader
        $(document).ajaxStart(function () {
            $('#global-loader').fadeIn(100);
        }).ajaxStop(function () {
            $('#global-loader').fadeOut(200);
        });

        // Page Navigation Loader
        $(document).ready(function () {
            // Check performance navigation to determine if it's a reload or back/forward
            // ensuring we hide loader if it was stuck
            $('#global-loader').fadeOut(200);

            // Handle Link Clicks
            $('a').on('click', function (e) {
                let href = $(this).attr('href');
                let target = $(this).attr('target');

                // Conditions to SHOW loader:
                // 1. Has href
                // 2. Not hash link (#)
                // 3. Not javascript:void
                // 4. Not open in new tab (target=_blank)
                // 5. Not Ctrl/Cmd click
                if (
                    href &&
                    !href.startsWith('#') &&
                    !href.startsWith('javascript') &&
                    target !== '_blank' &&
                    !e.ctrlKey && !e.metaKey
                ) {
                    $('#global-loader').fadeIn(100);
                }
            });

            // Handle Standard Form Submit (Non-AJAX)
            $('form').on('submit', function () {
                // Check if the form is NOT handled by AJAX (usually indicated by preventing default or lack of 'action')
                if (!$(this).data('ajax-handled')) {
                    $('#global-loader').fadeIn(100);
                }
            });

            // Re-hide on pageshow (fixes back button caching issues in Chrome/Safari)
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    $('#global-loader').hide();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>