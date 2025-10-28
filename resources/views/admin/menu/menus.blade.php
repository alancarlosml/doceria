@extends('layouts.admin')

@section('title', 'Cardápio Semanal - Doceria Delícia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none" x-data="menuManager('{{ $currentDayName }}')">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        🍽️ Cardápio Semanal
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Organize os produtos disponíveis por dia da semana
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <span class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 opacity-75 cursor-not-allowed">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Auto-salvamento ativado
                    </span>
                </div>
            </div>

            <!-- Days Navigation -->
            <div class="mb-8">
                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Selecionar dia da semana</label>
                    <select id="tabs" name="tabs" @change="changeDay($event.target.value)" class="block w-full focus:ring-blue-500 focus:border-blue-500 border-gray-300 rounded-md">
                        @php
                            $days = [
                                'segunda' => 'Segunda',
                                'terca' => 'Terça',
                                'quarta' => 'Quarta',
                                'quinta' => 'Quinta',
                                'sexta' => 'Sexta',
                                'sabado' => 'Sábado',
                                'domingo' => 'Domingo'
                            ];
                        @endphp
                        @foreach($days as $key => $name)
                            <option value="{{ $key }}" {{ $currentDayName === $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden sm:block">
                    <div class="flex flex-row rounded-lg bg-white border border-gray-200 p-1 space-x-1">
                        @php
                            $currentDate = \Carbon\Carbon::now();
                            $startOfWeek = $currentDate->copy()->startOfWeek();
                        @endphp

                        @foreach($days as $key => $name)
                            @php
                                $dayIndex = array_search($key, array_keys($days));
                                $dateForDay = $startOfWeek->copy()->addDays($dayIndex);
                            @endphp
                            <button
                                type="button"
                                @click="changeDay('{{ $key }}')"
                                :class="currentDay === '{{ $key }}' ? 'bg-blue-500 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                                class="flex-1 rounded-md px-3 py-2 text-sm font-medium leading-5 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center"
                            >
                                <span class="block">{{ $name }}</span>
                                <span class="block text-xs font-normal" :class="currentDay === '{{ $key }}' ? 'text-blue-100' : 'text-gray-400'">
                                    {{ $dateForDay->format('d/m') }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Menu Content -->
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
                <!-- Selected Products for Current Day -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold flex items-center">
                            <span class="text-2xl mr-2">✅</span>
                            Cardápio Ativo - <span x-text="getCurrentDayName(currentDay)" class="ml-2"></span>
                        </h3>
                        <span class="bg-green-700 text-green-100 px-3 py-1 rounded-full text-sm font-medium">
                            <span x-text="selectedProducts.length"></span> produtos selecionados
                        </span>
                    </div>

                    <div class="min-h-[200px]">
                        <div x-show="selectedProducts.length === 0" class="text-center py-12 text-green-100">
                            <span class="text-6xl block mb-4">🍽️</span>
                            <p class="text-lg">Nenhum produto selecionado para hoje</p>
                            <p class="text-sm opacity-75">Use os toggles abaixo para adicionar produtos ao cardápio</p>
                        </div>

                        <div x-show="selectedProducts.length > 0" class="space-y-6">
                            <template x-for="category in Object.keys(getCategoriesWithProducts())" :key="category">
                                <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur-sm">
                                    <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                        <span class="mr-2">📂</span>
                                        <span x-text="category"></span>
                                        <span class="ml-2 text-sm font-normal text-green-200">
                                            (<span x-text="getCategoriesWithProducts()[category].length"></span> produtos)
                                        </span>
                                    </h4>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <template x-for="product in getCategoriesWithProducts()[category]" :key="product.id">
                                            <div class="bg-white bg-opacity-20 rounded-lg p-3 backdrop-blur-sm border border-white border-opacity-30">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-white text-sm" x-text="product.name"></h5>
                                                        <p class="text-xs font-medium text-green-200" x-text="'R$ ' + product.price_formatted"></p>
                                                    </div>
                                                    <div>
                                                        <div>
                                                            <button
                                                                type="button"
                                                                @click="toggleProduct(product.id, product.name, product.category_name, product.price_formatted)"
                                                                :class="isSelected(product.id) ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-200 hover:bg-gray-300'"
                                                                class="relative inline-flex items-center justify-center w-12 h-12 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                            >
                                                                <svg class="w-6 h-6 text-white" :class="isSelected(product.id) && 'animate-pulse'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isSelected(product.id) ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4'"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- All Available Products -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="text-2xl mr-2">📦</span>
                            Todos os Produtos Disponíveis
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie quais produtos estarão disponíveis no cardápio
                        </p>
                    </div>

                    <div class="p-6">
                        @php
                            $productsByCategory = collect($products)->groupBy(function($product) {
                                return $product->category->name;
                            })->sortKeys();
                        @endphp

                        @foreach($productsByCategory as $categoryName => $productsInCategory)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="mr-3">📂</span>
                                {{ $categoryName }}
                                <span class="ml-2 text-sm font-normal text-gray-500">
                                    ({{ $productsInCategory->count() }} produtos)
                                </span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($productsInCategory as $product)
                                <div class="bg-gray-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900 leading-tight">{{ $product->name }}</h4>
                                            <p class="text-sm font-semibold text-green-600 mt-2">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <button
                                                type="button"
                                                @click="toggleProduct({{ $product->id }}, '{{ $product->name }}', '{{ $product->category->name }}', '{{ number_format($product->price, 2, ',', '.') }}')"
                                                :class="isSelected({{ $product->id }}) ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-200 hover:bg-gray-300'"
                                                class="relative inline-flex items-center justify-center w-12 h-12 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                <svg class="w-6 h-6 text-white" :class="isSelected({{ $product->id }}) && 'animate-pulse'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isSelected({{ $product->id }}) ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4'"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    @if($product->description)
                                    <p class="text-xs text-gray-600 line-clamp-2">{{ Str::limit($product->description, 60) }}</p>
                                    @else
                                    <p class="text-xs text-gray-400 italic">Sem descrição</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Alpine.js Menu Manager -->
<script>
function menuManager(initialDay) {
    return {
        currentDay: initialDay,
        selectedProducts: [],
        loading: false,

        init() {
            console.log('Menu Manager iniciado com dia:', this.currentDay);
            this.loadMenuForDay(this.currentDay);
        },

        changeDay(day) {
            if (this.loading || this.currentDay === day) return;
            console.log('Mudando para dia:', day);
            this.currentDay = day;
            this.loadMenuForDay(day);

            // Atualizar URL sem recarregar a página
            window.history.pushState({}, '', `/gestor/menus/${day}`);
        },

        async loadMenuForDay(day) {
            this.loading = true;
            console.log('Carregando menu para:', day);

            try {
                const response = await fetch(`/gestor/menu-data/${day}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('Dados recebidos:', data);
                    
                    this.selectedProducts = data.map(item => ({
                        id: item.product_id,
                        name: item.product.name,
                        category_name: item.product.category?.name || 'Sem categoria',
                        price_formatted: new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(item.product.price),
                        available: item.available
                    }));
                    
                    console.log('Produtos selecionados:', this.selectedProducts);
                } else {
                    console.error('Erro na resposta:', response.status);
                    throw new Error('Falha ao carregar cardápio');
                }
            } catch (error) {
                console.error('Erro ao carregar cardápio:', error);
                this.showToast('Erro ao carregar cardápio. Tente novamente.', 'error');
            } finally {
                this.loading = false;
            }
        },

        isSelected(productId) {
            return this.selectedProducts.some(p => p.id === productId);
        },

        async toggleProduct(productId, productName, categoryName, priceFormatted) {
            const isCurrentlySelected = this.isSelected(productId);
            const newState = !isCurrentlySelected;

            try {
                const response = await fetch('/gestor/menus/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        day_of_week: this.currentDay,
                        available: newState
                    })
                });

                if (response.ok) {
                    const result = await response.json();

                    if (newState) {
                        // Add to selected products
                        this.selectedProducts.push({
                            id: productId,
                            name: productName,
                            category_name: categoryName,
                            price_formatted: priceFormatted,
                            available: true
                        });

                        this.showToast(`${productName} adicionado ao cardápio!`, 'success');
                    } else {
                        // Remove from selected products
                        this.selectedProducts = this.selectedProducts.filter(p => p.id !== productId);

                        this.showToast(`${productName} removido do cardápio!`, 'info');
                    }
                } else {
                    throw new Error('Falha na requisição');
                }
            } catch (error) {
                console.error('Erro ao toggle produto:', error);
                this.showToast('Erro ao atualizar cardápio. Tente novamente.', 'error');
            }
        },

        getCurrentDayName(day) {
            const dayNames = {
                'segunda': 'Segunda-feira',
                'terca': 'Terça-feira',
                'quarta': 'Quarta-feira',
                'quinta': 'Quinta-feira',
                'sexta': 'Sexta-feira',
                'sabado': 'Sábado',
                'domingo': 'Domingo'
            };
            return dayNames[day] || day;
        },

        getCategoriesWithProducts() {
            const categorized = {};
            this.selectedProducts.forEach(product => {
                if (!categorized[product.category_name]) {
                    categorized[product.category_name] = [];
                }
                categorized[product.category_name].push(product);
            });
            return categorized;
        },

        showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'info' ? 'bg-blue-500' : 'bg-gray-500'
            } text-white max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <span class="text-lg mr-2">${
                        type === 'success' ? '✅' :
                        type === 'error' ? '❌' :
                        type === 'info' ? 'ℹ️' : '💡'
                    }</span>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}
</script>
@endsection
