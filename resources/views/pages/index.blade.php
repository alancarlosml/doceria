@extends('layouts.app')

@section('title', 'Cardápio do Dia')

@section('content')
<!-- Announcement Banner -->
@if(App\Models\Setting::get('banner_active', false) === true && App\Models\Setting::getBannerMessage())
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 shadow-sm">
    <div class="flex">
        {{-- <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div> --}}
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
                    <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center flex items-center justify-center gap-3">
                        <span class="inline-block w-12 h-1 bg-pink-400 rounded-full"></span>
                            {{ $categoryName }}
                        <span class="inline-block w-12 h-1 bg-pink-400 rounded-full"></span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                                        <p class="text-gray-600 mb-4 text-sm">{{ $product->description }}</p>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <div class="text-xl font-bold text-green-600">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </div>
                                        @if(App\Models\Setting::isStoreOpen())
                                        <a href="https://wa.me/5598984419339?text={{ urlencode('Olá, gostaria de pedir: ' . $product->name . ' (R$ ' . number_format($product->price, 2, ',', '.') . ')') }}"
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                                    <p class="text-gray-600 mb-4 text-sm">{{ $product->description }}</p>

                                    <div class="flex items-center justify-between">
                                        <div class="text-xl font-bold text-green-600">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </div>

                                        <a href="https://wa.me/5598984419339?text={{ urlencode('Olá, gostaria de pedir: ' . $product->name) }}"
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
            <a href="https://wa.me/5598984419339?text={{ urlencode('Olá, gostaria de fazer um pedido personalizado!') }}"
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
                    <li>📍 Prédio Lavitta, Av. Conselheiro Hílton Rodrigues, 247 - Araçagi, São José de Ribamar - MA</li>
                    <li>📞 (98) 98441-9339</li>
                    <li>⏰ Todos os dias: 13h às 19h (13h às 18h, aos domingos)</li>
                    <li>🚚 Delivery disponível</li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Redes Sociais</h4>
                <div class="flex gap-4">
                    <a href="https://www.instagram.com/doce_docebrigaderia/" target="_blank" class="text-gray-300 hover:text-white transition-colors inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg> Instagram
                    </a>
                    <a href="https://wa.me/5598984419339" target="_blank" class="text-gray-300 hover:text-white transition-colors inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg> WhatsApp
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
