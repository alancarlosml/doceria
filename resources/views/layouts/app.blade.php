<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Doceria') }} - @yield('title', 'Bem-vindo')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets (removido temporariamente - execute npm run dev para gerar) -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- Custom styles for this project -->
    <style>
        /* Doceria Color Scheme */
        .bg-pink-pastel {
            background-color: #fce7f3;
        }
        .bg-green-pastel {
            background-color: #dcfce7;
        }
        .bg-blue-light {
            background-color: #dbeafe;
        }
        .bg-white-light {
            background-color: #fefefe;
        }

        .text-pink-pastel {
            color: #fce7f3;
        }
        .text-green-pastel {
            color: #dcfce7;
        }
        .text-blue-light {
            color: #dbeafe;
        }

        .border-pink-pastel {
            border-color: #fce7f3;
        }
        .border-green-pastel {
            border-color: #dcfce7;
        }
        .border-blue-light {
            border-color: #dbeafe;
        }

        /* Custom gradient for cards */
        .card-gradient {
            background: linear-gradient(135deg, #fce7f3 0%, #dcfce7 100%);
        }

        /* Smooth animations */
        .transition-all {
            transition: all 0.3s ease;
        }

        /* Hover effects */
        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body class="antialiased font-sans bg-gray-50">
    @yield('content')

    <!-- Scripts -->
    @stack('scripts')

    <!-- Global Flash Messages -->
    @include('includes.flash-messages')
</body>

</html>
