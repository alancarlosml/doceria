@extends('layouts.app')

@section('title', 'Cardápio do Dia')

@section('content')
<!-- Announcement Banner -->
@if(App\Models\Setting::getBannerMessage())
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 shadow-sm">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700 font-medium">
                📢 {{ App\Models\Setting::getBannerMessage() }}
            </p>
        </div>
    </div>
</div>
@endif

<!-- Hero Section -->
<section class="card-gradient py-16 px-4">
    <div class="container mx-auto text-center">
        <h1 class="text-5xl md:text-4xl font-bold text-gray-800 mb-4">
            <div class="flex mx-auto text-center">
                <div class="container text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-2">
                        <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="Logo Doce Doce Brigaderia">
                    </div>
                    <span class="text-pink-600">Doce Doce</span>
                    <span class="text-green-600">Brigaderia</span>
                </div>
            </div>
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Sabor e alegria em cada mordida! Confira nosso cardápio especial de hoje.
        </p>
        <div class="text-lg font-semibold text-gray-700 bg-white bg-opacity-80 rounded-full px-6 py-2 inline-block">
            📅 Cardápio de {{ $currentDayDisplay ?? ucfirst($currentDayPt) }}
        </div>
    </div>
</section>

<!-- Menu Section -->
<section class="py-16 px-4">
    <div class="container mx-auto max-w-6xl">
        @if($menuItems->isNotEmpty())
            <!-- Status da Loja -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center gap-2 {{ App\Models\Setting::isStoreOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-4 py-2 rounded-full text-sm font-medium">
                    <div class="w-4 h-4 rounded-full {{ App\Models\Setting::isStoreOpen() ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <span>{{ App\Models\Setting::isStoreOpen() ? 'Loja Aberta' : 'Loja Fechada' }} - Estamos {{ App\Models\Setting::isStoreOpen() ? 'atendendo' : 'indisponíveis no momento' }}</span>
                </div>
            </div>

            @foreach($menuByCategory as $categoryName => $items)
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center flex items-center justify-center gap-3">
                        <span class="inline-block w-12 h-1 bg-pink-400 rounded-full"></span>
                        🍽️ {{ $categoryName }}
                        <span class="inline-block w-12 h-1 bg-pink-400 rounded-full"></span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($items as $menuItem)
                            @php
                                $product = $menuItem->product;
                            @endphp
                            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale overflow-hidden">
                                <!-- Product Image -->
                                <div class="h-48 bg-gradient-to-br from-pink-100 to-green-100 flex items-center justify-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-6xl" style="filter: brightness(1.2); opacity: 0.7;">
                                            {{ $product->category->emoji ?? '🍰' }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="text-xl font-bold text-gray-800 flex-1">{{ $product->name }}</h3>
                                    </div>
                                    
                                    @if($product->description)
                                        <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <div class="text-2xl font-bold text-green-600">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </div>
                                        @if(App\Models\Setting::isStoreOpen())
                                        <a href="https://wa.me/5598991655848?text={{ urlencode('Olá, gostaria de pedir: ' . $product->name . ' (R$ ' . number_format($product->price, 2, ',', '.') . ')') }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-full transition-all duration-300 hover-scale">
                                            <span>🛒 Pedir</span>
                                        </a>
                                        @else
                                        <strong
                                           class="inline-flex items-center gap-2 bg-gray-500 text-white font-semibold py-3 px-6 rounded-full transition-all duration-300 hover-scale disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span>🛒 Pedir</span>
                                        </strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Informação sobre atualização do cardápio -->
            <div class="mt-12 text-center">
                <div class="inline-block bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-2xl">
                    <p class="text-sm text-blue-800">
                        💡 <strong>Dica:</strong> Nosso cardápio é atualizado diariamente. Volte amanhã para conferir as novidades!
                    </p>
                </div>
            </div>
        @else
            <!-- Fallback se não houver produtos no cardápio de hoje -->
            <div class="text-center py-16">
                <div class="mb-8">
                    <span class="text-6xl">😊</span>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    Ops! Ainda não preparamos o cardápio especial de {{ $currentDayDisplay }}
                </h2>
                <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                    Mas não se preocupe! Confira algumas de nossas delícias que estão sempre disponíveis.
                    Entre em contato conosco para fazer seu pedido personalizado!
                </p>

                @if($featuredProducts->isNotEmpty())
                    <h3 class="text-2xl font-bold text-gray-800 mb-8">🌟 Destaques da Casa</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($featuredProducts as $product)
                            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale overflow-hidden">
                                <div class="h-48 bg-gradient-to-br from-pink-100 to-green-100 flex items-center justify-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-6xl" style="filter: brightness(1.2); opacity: 0.7;">
                                            {{ $product->category->emoji ?? '🍰' }}
                                        </div>
                                    @endif
                                </div>

                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>

                                    <div class="flex items-center justify-between">
                                        <div class="text-2xl font-bold text-green-600">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </div>

                                        <a href="https://wa.me/5598991655848?text={{ urlencode('Olá, gostaria de pedir: ' . $product->name) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-full transition-all duration-300 hover-scale">
                                            <span>🛒 Pedir</span>
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
<section class="card-gradient py-16 px-4">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            🎂 Preparado para adoçar seu dia?
        </h2>
        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
            Entre em contato conosco para encomendas especiais, festas ou eventos.
            Nossas doces criações são feitas com muito amor e ingredientes selecionados.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="https://wa.me/5598991655848?text={{ urlencode('Olá, gostaria de fazer um pedido personalizado!') }}"
               target="_blank"
               class="inline-flex items-center gap-3 bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                <span>Fazer Pedido via WhatsApp</span>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-12 px-4">
    <div class="container mx-auto max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center">
                            <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="">
                        </div>
                        <span class="font-bold text-white-800">Doce Doce Brigaderia</span>
                    </div>
                </h3>
                <p class="text-gray-300">
                    Transformando momentos comuns em memórias inesquecíveis com nossos doces artesanais.
                </p>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Informações</h4>
                <ul class="space-y-2 text-gray-300">
                    <li>📍 São Luís - MA</li>
                    <li>📞 (98) 99165-5848</li>
                    <li>⏰ Seg-Sáb: 8h às 18h</li>
                    <li>🚚 Delivery disponível</li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Redes Sociais</h4>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        📘 Facebook
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        📸 Instagram
                    </a>
                    <a href="https://wa.me/5598991655848" target="_blank" class="text-gray-300 hover:text-white transition-colors">
                        📱 WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-gray-700 my-8">

        <div class="text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Doce Doce Brigaderia. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
@endsection
