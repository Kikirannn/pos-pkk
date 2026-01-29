<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi POS Bazaar Kelas">

    <title>@yield('title', config('app.name', 'POS Bazaar'))</title>

    <!-- Fonts: Inter (Body) + Playfair Display (Headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Main Theme Colors
                        'primary': {
                            DEFAULT: '#FFA726', // Orange 400 (Light Orange)
                            hover: '#F57C00',   // Orange 700
                            light: '#FFE0B2',   // Orange 100
                        },
                        'secondary': {
                            DEFAULT: '#FDE047', // Yellow 300 (Muted Yellow)
                            hover: '#FBC02D',   // Yellow 700
                        },
                        // Re-mapped Mono to Warm/Brown Scale
                        'mono-black': '#3E2723',      // Dark Brown (Text)
                        'mono-dark': '#5D4037',       // Medium Brown
                        'mono-charcoal': '#795548',   // Light Brown
                        'mono-gray-dark': '#8D6E63',  // Brownish Gray
                        'mono-gray': '#A1887F',       // Lighter Brownish Gray
                        'mono-gray-light': '#D7CCC8', // Pale Brown
                        'mono-silver': '#EFEBE9',     // Warm Silver
                        'mono-light': '#FFE0B2',      // Orange 100 (Borders/Accents)
                        'mono-off-white': '#FFF8E1',  // Amber 50 (Page Background)
                        'mono-white': '#FFFFFF',      // White
                    },
                    fontFamily: {
                        'display': ['Playfair Display', 'serif'],
                        'body': ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'mono': '0 4px 6px -1px rgba(62, 39, 35, 0.05), 0 2px 4px -1px rgba(62, 39, 35, 0.03)',
                        'mono-md': '0 10px 15px -3px rgba(62, 39, 35, 0.07), 0 4px 6px -2px rgba(62, 39, 35, 0.03)',
                        'mono-lg': '0 20px 25px -5px rgba(62, 39, 35, 0.08), 0 10px 10px -5px rgba(62, 39, 35, 0.02)',
                        'mono-xl': '0 25px 50px -12px rgba(62, 39, 35, 0.12)',
                    }
                }
            }
        }
    </script>

    <!-- GSAP CDN -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

    <!-- Bootstrap Icons (keeping for icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFF8E1; /* Warm Cream Background */
            color: #3E2723; /* Dark Brown Text */
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Custom Scrollbar - Warm Theme */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #FFE0B2;
        }

        ::-webkit-scrollbar-thumb {
            background: #A1887F;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8D6E63;
        }

        /* Display Font Class */
        .font-display {
            font-family: 'Playfair Display', serif;
        }

        /* Smooth Transitions */
        * {
            transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease;
        }
        
        /* Helper for Bootstrap-like classes in Kasir */
        .btn-primary {
            background-color: #FFA726 !important;
            border-color: #FFA726 !important;
            color: #3E2723 !important;
        }
        .btn-primary:hover {
            background-color: #F57C00 !important;
            border-color: #F57C00 !important;
            color: #FFFFFF !important;
        }
        .text-primary {
            color: #F57C00 !important;
        }
        .bg-light {
            background-color: #FFF8E1 !important;
        }
        .bg-primary {
            background-color: #FFA726 !important;
        }
        .border-primary {
            border-color: #FFA726 !important;
        }
        .text-warning {
            color: #FBC02D !important; /* Muted Yellow */
        }
        .bg-danger {
            background-color: #D32F2F !important; /* Keep Red but maybe warmer? Or stick to standard red for alerts */
        }
        .badge.bg-danger {
            background-color: #F57C00 !important; /* Make cart badge dark orange instead of red */
            color: #FFFFFF !important;
        }

        /* Loading Overlay */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 248, 225, 0.95); /* Warm Cream Overlay */
            z-index: 9999;
            display: none;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .loader-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #FFE0B2;
            border-top-color: #FFA726; /* Orange Spinner */
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Utility: Grayscale Image Filter */
        .grayscale {
            filter: grayscale(100%);
        }

        .grayscale-hover:hover {
            filter: grayscale(0%);
        }
    </style>

    @stack('styles')
</head>

<body class="font-body">
    <!-- Global Loading Overlay -->
    <div id="global-loader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <p class="text-sm font-medium text-mono-dark">Memuat...</p>
        </div>
    </div>

    <!-- Content Area (No Navbar) -->
    <main>
        @yield('content')
    </main>

    <!-- Minimal Footer (only show on non-home pages) -->
    @if(!request()->routeIs('home') && !request()->routeIs('dapur.*') && !request()->routeIs('inventory.*'))
        <footer class="bg-mono-white border-t border-mono-light py-6 mt-auto">
            <div class="container mx-auto px-4 text-center text-mono-gray text-xs">
                <p class="mb-1">&copy; {{ date('Y') }} <strong>Bazaar Kelas POS System</strong></p>
                <p>Dibuat untuk Project PKK</p>
            </div>
        </footer>
    @endif

    <!-- Essential Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
            $('#global-loader').fadeOut(200);

            // Handle Link Clicks
            $('a').on('click', function (e) {
                let href = $(this).attr('href');
                let target = $(this).attr('target');

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
                if (!$(this).data('ajax-handled')) {
                    $('#global-loader').fadeIn(100);
                }
            });

            // Re-hide on pageshow (fixes back button caching issues)
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    $('#global-loader').hide();
                }
            });
        });

        // GSAP Registration
        gsap.registerPlugin(ScrollTrigger);
    </script>

    @stack('scripts')
</body>

</html>