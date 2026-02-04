<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-16x16.png') }}" sizes="16x16">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <meta name="theme-color" content="#fce7f3">
    <meta name="msapplication-TileColor" content="#fce7f3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Doce Doce Brigaderia">
    <meta name="application-name" content="Doce Doce Brigaderia">

    <title>{{ config('app.name', 'Doceria') }} - @yield('title', 'Bem-vindo')</title>

    <!-- Google Fonts - Elegant Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets (removido temporariamente - execute npm run dev para gerar) -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- Custom styles for this project -->
    <style>
        /* CSS Variables - Professional Color System */
        :root {
            /* Primary Palette - Refined Pink & Cream */
            --color-primary-50: #fef2f8;
            --color-primary-100: #fce7f3;
            --color-primary-200: #f9cfe7;
            --color-primary-300: #f5a7d5;
            --color-primary-400: #f075b8;
            --color-primary-500: #ec4899;
            --color-primary-600: #db2777;
            --color-primary-700: #be185d;
            --color-primary-800: #9f1239;
            --color-primary-900: #831843;

            /* Accent Colors - Gold & Warm Tones */
            --color-accent-50: #fffbeb;
            --color-accent-100: #fef3c7;
            --color-accent-200: #fde68a;
            --color-accent-300: #fcd34d;
            --color-accent-400: #fbbf24;
            --color-accent-500: #f59e0b;
            --color-accent-600: #d97706;

            /* Neutral Palette - Sophisticated Grays */
            --color-neutral-50: #fafaf9;
            --color-neutral-100: #f5f5f4;
            --color-neutral-200: #e7e5e4;
            --color-neutral-300: #d6d3d1;
            --color-neutral-400: #a8a29e;
            --color-neutral-500: #78716c;
            --color-neutral-600: #57534e;
            --color-neutral-700: #44403c;
            --color-neutral-800: #292524;
            --color-neutral-900: #1c1917;

            /* Success/Green - Refined */
            --color-success-50: #f0fdf4;
            --color-success-100: #dcfce7;
            --color-success-500: #22c55e;
            --color-success-600: #16a34a;
            --color-success-700: #15803d;

            /* Typography */
            --font-display: 'Inter', serif;
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-elegant: 'Cormorant Garamond', serif;

            /* Spacing & Effects */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-elegant: 0 4px 20px rgba(236, 72, 153, 0.15);

            /* Transitions */
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Typography System */
        body {
            font-family: var(--font-body);
            font-feature-settings: "kern" 1, "liga" 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        h1, h2, h3, h4, h5, h6,
        .font-display {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .font-elegant {
            font-family: var(--font-elegant);
            font-weight: 500;
        }

        /* Legacy Color Classes - Enhanced */
        .bg-pink-pastel {
            background-color: var(--color-primary-100);
        }
        .bg-green-pastel {
            background-color: var(--color-success-100);
        }
        .bg-blue-light {
            background-color: #dbeafe;
        }
        .bg-white-light {
            background-color: #fefefe;
        }

        .text-pink-pastel {
            color: var(--color-primary-100);
        }
        .text-green-pastel {
            color: var(--color-success-100);
        }
        .text-blue-light {
            color: #dbeafe;
        }

        .border-pink-pastel {
            border-color: var(--color-primary-100);
        }
        .border-green-pastel {
            border-color: var(--color-success-100);
        }
        .border-blue-light {
            border-color: #dbeafe;
        }

        /* Enhanced Gradients */
        .card-gradient {
            background: linear-gradient(135deg, var(--color-primary-100) 0%, var(--color-success-100) 100%);
        }

        .gradient-elegant {
            background: linear-gradient(135deg, var(--color-primary-50) 0%, var(--color-accent-50) 50%, var(--color-success-50) 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-accent-500) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Smooth Animations */
        .transition-all {
            transition: all var(--transition-base);
        }

        .transition-elegant {
            transition: all var(--transition-base);
        }

        /* Enhanced Hover Effects */
        .hover-scale {
            transition: transform var(--transition-base), box-shadow var(--transition-base);
        }

        .hover-scale:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        /* Elegant Shadows */
        .shadow-elegant {
            box-shadow: var(--shadow-elegant);
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Focus States */
        *:focus-visible {
            outline: 2px solid var(--color-primary-500);
            outline-offset: 2px;
        }

        /* Enhanced Buttons */
        button, .btn {
            transition: all var(--transition-base);
        }

        button:hover, .btn:hover {
            transform: translateY(-1px);
        }

        button:active, .btn:active {
            transform: translateY(0);
        }

        /* Loading States */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .loading-shimmer {
            animation: shimmer 2s infinite;
            background: linear-gradient(to right, #f0f0f0 0%, #e0e0e0 20%, #f0f0f0 40%, #f0f0f0 100%);
            background-size: 1000px 100%;
        }

        /* Smooth Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-neutral-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-neutral-300);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-neutral-400);
        }

        /* Enhanced Cards */
        .card {
            transition: all var(--transition-base);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        /* Refined Input Fields */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        textarea,
        select {
            transition: all var(--transition-base);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
        }
    </style>
</head>

<body class="antialiased font-sans bg-gray-50">
    @yield('content')

    <!-- QZ Tray Library (para impressão direta em impressoras térmicas) -->
    <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.min.js"></script>
    <script src="{{ asset('js/qz-print.js') }}"></script>
    
    <!-- Printer Agent Integration -->
    <script src="{{ asset('js/printer-agent.js') }}"></script>
    
    <!-- Scripts -->
    @stack('scripts')

    <!-- Global Flash Messages -->
    @include('includes.flash-messages')
</body>

</html>
