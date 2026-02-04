@extends('layouts.app')

@section('title', 'Cardápio do Dia')

@section('content')
<style>
    /* Carousel Styles */
    .carousel-container {
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    
    .carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }
    
    .carousel-slide {
        min-width: 100%;
        position: relative;
    }
    
    .carousel-slide img {
        width: 100%;
        height: clamp(200px, 30vh, 350px);
        object-fit: cover;
    }
    
    @media (min-width: 768px) {
        .carousel-slide img {
            height: clamp(250px, 40vh, 400px);
        }
    }
    
    .carousel-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        padding: clamp(1rem, 3vw, 2rem);
        color: white;
    }
    
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.9);
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #374151;
        transition: all 0.3s;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .carousel-btn:hover {
        background: white;
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-btn.prev {
        left: clamp(0.5rem, 2vw, 1rem);
    }
    
    .carousel-btn.next {
        right: clamp(0.5rem, 2vw, 1rem);
    }
    
    .carousel-dots {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.5rem;
        z-index: 10;
    }
    
    .carousel-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .carousel-dot.active {
        background: white;
        transform: scale(1.2);
    }
    
    /* Category Tabs */
    .category-tabs {
        display: flex;
        overflow-x: auto;
        gap: 0.5rem;
        padding: 1rem 0;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .category-tabs::-webkit-scrollbar {
        display: none;
    }
    
    .category-tab {
        flex-shrink: 0;
        padding: 0.75rem 1.5rem;
        border-radius: 9999px;
        font-weight: 500;
        font-size: 0.875rem;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .category-tab:not(.active) {
        background: #f3f4f6;
        color: #4b5563;
    }
    
    .category-tab:not(.active):hover {
        background: #e5e7eb;
    }
    
    .category-tab.active {
        background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        color: white;
        border-color: transparent;
    }
    
    /* Product Card - Coco Bambu Style (Compact) */
    .product-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }
    
    .product-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .product-image {
        width: 100%;
        height: 100px;
        object-fit: cover;
        background: linear-gradient(135deg, #fce7f3 0%, #dcfce7 100%);
    }
    
    @media (min-width: 640px) {
        .product-image {
            height: 110px;
        }
    }
    
    .product-info {
        padding: 0.625rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .product-name {
        font-weight: 600;
        font-size: clamp(0.75rem, 2vw, 0.85rem);
        color: #1f2937;
        margin-bottom: 0.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
        min-height: 2rem;
        letter-spacing: -0.01em;
    }
    
    .product-price-row {
        display: flex;
        align-items: baseline;
        gap: 0.375rem;
        margin-bottom: 0.25rem;
        flex-wrap: wrap;
    }
    
    .product-price {
        font-weight: 700;
        font-size: clamp(0.875rem, 2.5vw, 0.95rem);
        color: var(--color-success-600);
        font-family: var(--font-body);
    }
    
    .product-code {
        font-size: 0.65rem;
        color: #9ca3af;
    }
    
    .product-description {
        font-size: 0.7rem;
        color: #6b7280;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
        line-height: 1.35;
        min-height: 1.9rem;
    }
    
    .product-action {
        margin-top: 0.5rem;
    }
    
    .whatsapp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        width: 100%;
        padding: 0.4rem 0.5rem;
        background: #22c55e;
        color: white;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.7rem;
        transition: all 0.2s;
        text-decoration: none;
    }
    
    .whatsapp-btn:hover {
        background: #16a34a;
    }
    
    .whatsapp-btn.disabled {
        background: #d1d5db;
        color: #6b7280;
        cursor: not-allowed;
    }
    
    .whatsapp-btn svg {
        width: 12px;
        height: 12px;
    }
    
    /* Products Grid - More items per row */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    @media (min-width: 480px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (min-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
    }
    
    @media (min-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }
    
    @media (min-width: 1280px) {
        .products-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }
    
    /* Section Styles */
    .section-title {
        position: relative;
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .section-title h2 {
        display: inline-block;
        padding: 0 1rem;
        background: #f9fafb;
        position: relative;
        z-index: 1;
        font-size: 1.25rem;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .section-title::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    }
    
    /* Store Header */
    .store-header {
        display: flex;
        align-items: center;
        gap: clamp(0.5rem, 2vw, 1rem);
        padding: clamp(0.75rem, 2vw, 1rem) clamp(1rem, 3vw, 1.5rem);
        background: white;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 40;
    }
    
    .store-logo {
        width: clamp(40px, 10vw, 48px);
        height: clamp(40px, 10vw, 48px);
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .store-name {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: clamp(1rem, 4vw, 1.25rem);
        color: #1f2937;
        letter-spacing: -0.01em;
    }

    /* Enhanced Product Cards */
    .product-card {
        position: relative;
        overflow: hidden;
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--color-primary-400), var(--color-accent-400), var(--color-primary-400));
        opacity: 0;
        transition: opacity var(--transition-base);
    }

    .product-card:hover::before {
        opacity: 1;
    }

    /* Refined Section Titles */
    .section-title h2 {
        font-family: var(--font-display);
        font-weight: 700;
        letter-spacing: 0.02em;
        position: relative;
    }

    /* Enhanced Buttons */
    .whatsapp-btn {
        position: relative;
        overflow: hidden;
        transition: all var(--transition-base);
    }

    .whatsapp-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .whatsapp-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .whatsapp-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    /* Refined Category Tabs */
    .category-tab {
        position: relative;
        overflow: hidden;
    }

    .category-tab::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: var(--color-primary-500);
        transform: translateX(-50%);
        transition: width var(--transition-base);
    }

    .category-tab.active::after {
        width: 80%;
    }

    /* Smooth Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card {
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .product-card:nth-child(1) { animation-delay: 0.05s; }
    .product-card:nth-child(2) { animation-delay: 0.1s; }
    .product-card:nth-child(3) { animation-delay: 0.15s; }
    .product-card:nth-child(4) { animation-delay: 0.2s; }
    .product-card:nth-child(5) { animation-delay: 0.25s; }
    .product-card:nth-child(6) { animation-delay: 0.3s; }
</style>

<!-- Store Header -->
<header class="store-header shadow-sm">
    <img src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="Logo" class="store-logo">
    <div class="flex-1 min-w-0">
        <h1 class="store-name font-display truncate">Doce Doce Brigaderia</h1>
        <div class="flex items-center gap-2 mt-0.5">
            <div class="w-2 h-2 rounded-full flex-shrink-0 {{ App\Models\Setting::isStoreOpen() ? 'bg-green-500' : 'bg-red-500' }}"></div>
            <span class="text-xs {{ App\Models\Setting::isStoreOpen() ? 'text-green-600' : 'text-red-600' }} truncate">
                {{ App\Models\Setting::isStoreOpen() ? 'Aberto' : 'Fechado' }}
            </span>
        </div>
    </div>
    <a href="https://wa.me/5598984419339" target="_blank" class="p-1.5 md:p-2 text-gray-500 hover:text-green-600 transition-colors flex-shrink-0">
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </a>
</header>

<!-- Carousel Banner -->
@if($carouselBanners->isNotEmpty())
<div class="carousel-container" x-data="carousel()" x-init="init()">
    <div class="carousel-track" :style="'transform: translateX(-' + (currentSlide * 100) + '%)'">
        @foreach($carouselBanners as $banner)
        <div class="carousel-slide">
            @if($banner->link)
                <a href="{{ $banner->link }}" target="_blank">
            @endif
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?? 'Banner promocional' }}">
            @if($banner->title || $banner->description)
            <div class="carousel-overlay">
                @if($banner->title)
                    <h2 class="text-2xl font-bold mb-1">{{ $banner->title }}</h2>
                @endif
                @if($banner->description)
                    <p class="text-sm opacity-90">{{ $banner->description }}</p>
                @endif
            </div>
            @endif
            @if($banner->link)
                </a>
            @endif
        </div>
        @endforeach
    </div>
    
    @if($carouselBanners->count() > 1)
    <button class="carousel-btn prev" @click="prevSlide()">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button class="carousel-btn next" @click="nextSlide()">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
    
    <div class="carousel-dots">
        @foreach($carouselBanners as $index => $banner)
        <div class="carousel-dot" :class="currentSlide === {{ $index }} ? 'active' : ''" @click="goToSlide({{ $index }})"></div>
        @endforeach
    </div>
    @endif
</div>
@else
<!-- Default Hero if no banners -->
<section class="bg-gradient-to-br from-pink-100 via-purple-50 to-green-100 py-12 px-4">
    <div class="container mx-auto text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="Logo Doce Doce Brigaderia">
        </div>
        <h1 class="text-4xl font-display font-bold text-gray-800 mb-2">
            <span class="gradient-text">Doce Doce Brigaderia</span>
        </h1>
        <p class="text-gray-600 mb-4">Amor em cada doce!</p>
        <div class="text-sm font-semibold text-gray-700 bg-white bg-opacity-80 rounded-full px-4 py-1.5 inline-block">
            📅 Cardápio de {{ $currentDayDisplay ?? ucfirst($currentDayPt) }}
        </div>
    </div>
</section>
@endif

<!-- Announcement Banner -->
@if(App\Models\Setting::get('banner_active', false) === true && App\Models\Setting::getBannerMessage())
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 px-4 py-3">
    <div class="flex items-center">
        <span class="text-lg mr-2">📢</span>
        <p class="text-sm text-blue-800 font-medium">
            {{ App\Models\Setting::getBannerMessage() }}
        </p>
    </div>
</div>
@endif

<!-- Category Navigation Tabs -->
@if($categories->isNotEmpty() && $menuItems->isNotEmpty())
<div class="bg-white border-b border-gray-200 sticky top-[73px] z-30">
    <div class="container mx-auto px-4">
        <div class="category-tabs">
            @foreach($menuByCategory->keys() as $categoryName)
            <a href="#category-{{ Str::slug($categoryName) }}" 
               class="category-tab" 
               data-category="{{ Str::slug($categoryName) }}">
                {{ $categoryName }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Menu Section -->
<section class="py-6 md:py-8 px-2 md:px-4 bg-gray-50">
    <div class="container mx-auto max-w-6xl">
        @if($menuItems->isNotEmpty())
            @foreach($menuByCategory as $categoryName => $items)
                <div id="category-{{ Str::slug($categoryName) }}" class="mb-10 scroll-mt-32">
                    <div class="section-title">
                        <h2>{{ $categoryName }}</h2>
                    </div>

                    <div class="products-grid">
                        @foreach($items as $menuItem)
                            @php
                                $product = $menuItem->product;
                            @endphp
                            <div class="product-card">
                                <!-- Product Image -->
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <div class="product-image flex items-center justify-center">
                                        <span class="text-3xl opacity-60">{{ $product->category->emoji ?? '🍰' }}</span>
                                    </div>
                                @endif

                                <!-- Product Info -->
                                <div class="product-info">
                                    <h3 class="product-name">{{ $product->name }}</h3>
                                    <div class="product-price-row">
                                        <span class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                        {{-- <span class="product-code">Cód: {{ $product->id }}</span> --}}
                                    </div>
                                    
                                    @if($product->description)
                                        <p class="product-description">{{ $product->description }}</p>
                                    @else
                                        <p class="product-description">&nbsp;</p>
                                    @endif

                                    <div class="product-action">
                                        @if(App\Models\Setting::isStoreOpen())
                                        <a href="https://wa.me/5598984419339?text={{ urlencode('Olá, gostaria de pedir: ' . $product->name . ' (R$ ' . number_format($product->price, 2, ',', '.') . ') - Cód: ' . $product->id) }}"
                                           target="_blank"
                                           class="whatsapp-btn">
                                            <svg fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            Pedir
                                        </a>
                                        @else
                                        <span class="whatsapp-btn disabled">Fechado</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Info tip -->
            <div class="mt-8 text-center">
                <div class="inline-block bg-blue-50 border border-blue-200 rounded-lg p-3 max-w-xl">
                    <p class="text-xs text-blue-700">
                        💡 <strong>Dica:</strong> Nosso cardápio é atualizado diariamente. Volte amanhã para conferir as novidades!
                    </p>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mb-6">
                    <span class="text-6xl">😊</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">
                    Cardápio de {{ $currentDayDisplay }} ainda não disponível
                </h2>
                <p class="text-gray-600 mb-8 max-w-lg mx-auto">
                    Mas não se preocupe! Confira algumas de nossas delícias que estão sempre disponíveis.
                </p>

                @if($featuredProducts->isNotEmpty())
                    <div class="section-title mb-8">
                        <h2>🌟 Destaques da Casa</h2>
                    </div>
                    
                    <div class="products-grid">
                        @foreach($featuredProducts as $product)
                            <div class="product-card">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <div class="product-image flex items-center justify-center">
                                        <span class="text-3xl opacity-60">{{ $product->category->emoji ?? '🍰' }}</span>
                                    </div>
                                @endif

                                <div class="product-info">
                                    <h3 class="product-name">{{ $product->name }}</h3>
                                    <div class="product-price-row">
                                        <span class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                        <span class="product-code">Cód: {{ $product->id }}</span>
                                    </div>
                                    
                                    @if($product->description)
                                        <p class="product-description">{{ $product->description }}</p>
                                    @else
                                        <p class="product-description">&nbsp;</p>
                                    @endif

                                    <div class="product-action">
                                        <a href="https://wa.me/5598984419339?text={{ urlencode('Olá, gostaria de pedir: ' . $product->name . ' (R$ ' . number_format($product->price, 2, ',', '.') . ')') }}"
                                           target="_blank"
                                           class="whatsapp-btn">
                                            <svg fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            Pedir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>

<!-- Call to Action -->
<section class="bg-gradient-to-br from-pink-100 via-purple-50 to-green-100 py-12 px-4">
    <div class="container mx-auto text-center max-w-2xl">
        <h2 class="text-2xl md:text-3xl font-display font-bold text-gray-800 mb-2 md:mb-3 px-4">
            🎂 Preparado para adoçar seu dia?
        </h2>
        <p class="text-gray-600 mb-4 md:mb-6 px-4 text-sm md:text-base">
            Entre em contato conosco para encomendas especiais, festas ou eventos.
        </p>

        <a href="https://wa.me/5598984419339?text={{ urlencode('Olá, gostaria de fazer um pedido personalizado!') }}"
           target="_blank"
           class="inline-flex items-center gap-2 md:gap-3 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 md:py-3 px-4 md:px-6 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg text-sm md:text-base">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            <span>Fazer Pedido via WhatsApp</span>
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-10 px-4">
    <div class="container mx-auto max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full overflow-hidden">
                        <img class="w-full h-full object-cover" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="">
                    </div>
                    <span class="font-bold text-lg">Doce Doce Brigaderia</span>
                </div>
                <p class="text-gray-400 text-sm">
                    Transformando momentos comuns em memórias inesquecíveis com nossos doces artesanais.
                </p>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Informações</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li class="flex items-start gap-2">
                        <span>📍</span>
                        <span>Prédio Lavitta, Av. Conselheiro Hílton Rodrigues, 247 - Araçagi, São José de Ribamar - MA</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span>📞</span>
                        <span>(98) 98441-9339</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span>⏰</span>
                        <span>13h às 19h (Dom: 13h às 18h)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span>🚚</span>
                        <span>Delivery disponível</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Redes Sociais</h4>
                <div class="flex gap-4">
                    <a href="https://www.instagram.com/doce_docebrigaderia/" target="_blank" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg>
                        <span class="text-sm">Instagram</span>
                    </a>
                    <a href="https://wa.me/5598984419339" target="_blank" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg>
                        <span class="text-sm">WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-gray-700 my-6">

        <div class="text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Doce Doce Brigaderia. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

@push('scripts')
<script>
// Carousel functionality
function carousel() {
    return {
        currentSlide: 0,
        totalSlides: {{ $carouselBanners->count() }},
        autoPlayInterval: null,
        
        init() {
            if (this.totalSlides > 1) {
                this.startAutoPlay();
            }
        },
        
        startAutoPlay() {
            this.autoPlayInterval = setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        
        stopAutoPlay() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
            }
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },
        
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        
        goToSlide(index) {
            this.currentSlide = index;
            this.stopAutoPlay();
            this.startAutoPlay();
        }
    };
}

// Category tab highlight on scroll
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.category-tab');
    const sections = document.querySelectorAll('[id^="category-"]');
    
    if (tabs.length === 0 || sections.length === 0) return;
    
    // Smooth scroll on tab click
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const target = document.getElementById(targetId);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Highlight active tab on scroll
    const observerOptions = {
        root: null,
        rootMargin: '-150px 0px -50% 0px',
        threshold: 0
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const categorySlug = entry.target.id.replace('category-', '');
                tabs.forEach(tab => {
                    if (tab.dataset.category === categorySlug) {
                        tab.classList.add('active');
                    } else {
                        tab.classList.remove('active');
                    }
                });
            }
        });
    }, observerOptions);
    
    sections.forEach(section => observer.observe(section));
    
    // Set first tab as active initially
    if (tabs.length > 0) {
        tabs[0].classList.add('active');
    }
});
</script>
@endpush
@endsection
