@extends('layouts.admin')

@section('title', 'PDV - Ponto de Venda')

@section('admin-content')
<main class="flex-1 relative overflow-hidden bg-gray-50" x-data="posSystem()">
    <div class="h-screen flex flex-col lg:flex-row overflow-hidden">
        
        <!-- COLUNA 1: Produtos (40%) -->
        <div class="w-full lg:w-[40%] lg:min-w-[300px] bg-white border-r border-gray-200 flex flex-col overflow-hidden order-1 lg:order-1">
            <!-- Header -->
            <div class="p-3 md:p-4 border-b border-gray-200 bg-gradient-to-r from-pink-50 to-purple-50">
                <div class="flex items-center gap-2 md:gap-3 mb-2 md:mb-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-pink-500 rounded-full flex items-center justify-center text-white text-lg md:text-xl flex-shrink-0">
                        🛒
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base md:text-xl font-bold text-gray-800 truncate">PDV - Ponto de Venda</h2>
                        <p class="text-xs text-gray-600 truncate">Caixa #{{ $openCashRegister->id }} - {{ Auth::user()->name }}</p>
                    </div>
                </div>
                
                <input 
                    type="text" 
                    x-model.debounce.300ms="searchQuery"
                    placeholder="🔍 Buscar produtos..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500"
                >
            </div>

            <!-- Categorias -->
            <div class="flex overflow-x-auto border-b border-gray-200 bg-gray-50 px-2 py-2 gap-2">
                <button
                    @click="selectedCategory = null"
                    :class="selectedCategory === null ? 'bg-pink-500 text-white' : 'bg-white text-gray-700'"
                    class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium text-xs md:text-sm whitespace-nowrap flex-shrink-0"
                >
                    🍽️ Todos
                </button>
                @foreach($categories as $category)
                <button
                    @click="selectedCategory = {{ $category->id }}"
                    :class="selectedCategory === {{ $category->id }} ? 'bg-pink-500 text-white' : 'bg-white text-gray-700'"
                    class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium text-xs md:text-sm whitespace-nowrap flex-shrink-0"
                >
                    {{ $category->emoji ?? '📦' }} {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Grid de Produtos -->
            <div class="flex-1 overflow-y-auto p-2 md:p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($products as $product)
                    <button
                        @click="addItemWithFeedback({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->category->name ?? 'Sem categoria' }}')"
                        x-show="(selectedCategory === null || selectedCategory === {{ $product->category_id }}) && productMatchesSearch('{{ addslashes($product->name) }}')"
                        x-data="{ justAdded: false }"
                        :class="justAdded ? 'scale-95 border-green-500 bg-green-50' : 'bg-white border-2 border-gray-200 hover:border-pink-500 hover:shadow-lg'"
                        class="rounded-lg p-2 transition-all duration-200 text-left active:scale-95"
                    >
                        <div class="h-16 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg flex items-center justify-center mb-1.5 transition-transform duration-200 hover:scale-105">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover rounded-lg">
                            @else
                                <span class="text-2xl">{{ $product->category->emoji ?? '🍰' }}</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-gray-800 text-xs mb-0.5 line-clamp-2">{{ $product->name }}</h4>
                        <p class="text-xs font-bold text-pink-600">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- COLUNA 2: Carrinho (40%) -->
        <div class="w-full lg:w-[40%] lg:min-w-[350px] bg-white flex flex-col border-r border-gray-200 overflow-hidden order-3 lg:order-2">
            <!-- Header do Carrinho -->
            <div class="p-3 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50 flex-shrink-0 max-h-[35vh] overflow-y-auto">
                <h3 class="text-base font-bold text-gray-800 mb-2 flex items-center gap-2">
                    <span>🧾</span>
                    <span>Pedido Atual</span>
                </h3>

                <!-- Tipo de Venda -->
                <div class="flex gap-2 mb-2">
                    <button
                        @click="setType('balcao')"
                        :class="cart.type === 'balcao' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border'"
                        class="flex-1 py-1.5 rounded-lg font-medium text-xs"
                    >
                        🪑 Balcão
                    </button>
                    <button
                        @click="setType('delivery')"
                        :class="cart.type === 'delivery' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 border'"
                        class="flex-1 py-1.5 rounded-lg font-medium text-xs"
                    >
                        🏍️ Delivery
                    </button>
                </div>

                <!-- Seleção de Mesa (Balcão) -->
                <div x-show="cart.type === 'balcao'" class="mb-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mesa (Opcional)</label>
                    <select
                        id="tableSelect"
                        x-model.number="cart.table_id"
                        @change="onTableChange"
                        x-ref="tableSelect"
                        class="w-full flex items-center justify-between px-2 py-1.5 bg-white border-2 rounded-lg text-xs font-medium transition-all"
                    >
                        <option value="">Sem mesa (venda rápida)</option>
                        @php
                            // Mostrar todas as mesas - as ocupadas serão desabilitadas no frontend
                            $allTables = $tables->merge($occupiedTables)->sortBy('number');
                        @endphp
                        @foreach($allTables as $table)
                        <option 
                            value="{{ $table->id }}"
                            data-status="{{ $table->status }}"
                            data-table-id="{{ $table->id }}"
                        >
                            Mesa {{ $table->number }} ({{ $table->capacity }} pessoas)
                            @if($table->status === 'ocupada')
                                - ⚠️ Ocupada
                            @else
                                - ✅ Disponível
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p x-show="selectedTableOccupied" class="text-xs text-red-600 mt-1">⚠️ Esta mesa está ocupada!</p>
                </div>

                <!-- Botão para Abrir Detalhes (Delivery/Encomenda) -->
                <div x-show="cart.type !== 'balcao'" class="mt-2" x-data="{ detailsOpen: false }">
                    <button 
                        @click="detailsOpen = !detailsOpen"
                        class="w-full flex items-center justify-between px-2 py-1.5 bg-white border-2 rounded-lg text-xs font-medium transition-all"
                        :class="detailsOpen ? 'border-blue-500 text-blue-700' : 'border-gray-300 text-gray-700 hover:border-gray-400'"
                    >
                        <span x-show="cart.type === 'delivery'">📋 Detalhes da Entrega</span>
                        {{-- <span x-show="cart.type === 'encomenda'">📋 Detalhes da Encomenda</span> --}}
                        <svg class="w-4 h-4 transition-transform" :class="detailsOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Collapse com Campos Adicionais -->
                    <div 
                        x-show="detailsOpen" 
                        x-collapse
                        class="mt-1.5 p-2 bg-gray-50 border border-gray-200 rounded-lg space-y-2"
                    >
                        <!-- Cliente -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Cliente</label>
                            <select 
                                x-model="cart.customer_id"
                                class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-pink-500"
                            >
                                <option value="">Selecione um cliente</option>
                                @foreach($recentCustomers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Delivery -->
                        <template x-if="cart.type === 'delivery'">
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">
                                        Endereço
                                    </label>
                                    <textarea
                                        x-model="cart.delivery_address"
                                        rows="2"
                                        class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500"
                                        placeholder="Rua, número, complemento..."
                                    ></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-1.5">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Taxa (R$)</label>
                                        <input
                                            type="number"
                                            x-model.number="cart.delivery_fee"
                                            @input="calculateTotals()"
                                            step="0.50"
                                            min="0"
                                            class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500"
                                            placeholder="5.00"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-0.5">
                                            Motoboy <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            x-model="cart.motoboy_id"
                                            class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">Selecione...</option>
                                            @foreach($motoboys as $motoboy)
                                            <option value="{{ $motoboy->id }}">{{ $motoboy->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Encomenda -->
                        {{-- <template x-if="cart.type === 'encomenda'">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Data <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        x-model="cart.delivery_date"
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Hora <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="time"
                                        x-model="cart.delivery_time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                                    >
                                </div>
                            </div>
                        </template> --}}
                    </div>
                </div>

                <!-- Campos Delivery -->
                {{-- <div x-show="cart.type === 'delivery'" class="space-y-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                        <select x-model="cart.customer_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">Selecione...</option>
                            @foreach($recentCustomers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Endereço *</label>
                        <textarea
                            x-model="cart.delivery_address"
                            rows="2"
                            class="w-full px-3 py-2 border rounded-lg text-sm"
                            placeholder="Rua, número, bairro..."
                        ></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Taxa</label>
                            <input
                                type="number"
                                x-model.number="cart.delivery_fee"
                                @input="calculateTotals()"
                                step="0.50"
                                class="w-full px-3 py-2 border rounded-lg text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motoboy *</label>
                            <select x-model="cart.motoboy_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Selecione...</option>
                                @foreach($motoboys as $motoboy)
                                <option value="{{ $motoboy->id }}">{{ $motoboy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Lista de Itens -->
            <div class="overflow-y-auto p-2 md:p-3 min-h-0" style="max-height: calc(100vh - 450px);">
                <div x-show="cart.items.length === 0" class="text-center py-6 text-gray-400">
                    <div class="text-3xl mb-2">🛒</div>
                    <p class="text-sm">Carrinho vazio</p>
                </div>

                <div class="space-y-2">
                    <template x-for="(item, index) in cart.items" :key="'item-' + item.product_id + '-' + index">
                        <div 
                            x-data="{ isNew: false }"
                            x-init="
                                isNew = true;
                                setTimeout(() => isNew = false, 500);
                            "
                            :class="isNew ? 'animate-slide-in bg-green-50 border-green-300' : 'bg-gray-50 border-gray-200'"
                            class="rounded-lg p-2 border-2 transition-all duration-300"
                        >
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex-1">
                                    <h4 class="font-medium text-xs" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="'R$ ' + formatMoney(item.price) + ' cada'"></p>
                                </div>
                                <button 
                                    @click="removeItemWithFeedback(index)"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 text-sm w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 active:scale-90"
                                    title="Remover item"
                                >
                                    ✕
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between mt-1">
                                <div class="flex items-center gap-1">
                                    <button 
                                        @click="updateQuantity(index, -1)"
                                        class="w-7 h-7 bg-gray-200 hover:bg-gray-300 active:scale-90 rounded-lg font-bold text-sm transition-all duration-150"
                                    >−</button>
                                    <span 
                                        x-data="{ changed: false }"
                                        x-effect="
                                            if (item.quantity) {
                                                changed = true;
                                                setTimeout(() => changed = false, 300);
                                            }
                                        "
                                        :class="changed ? 'scale-125 text-green-600 font-extrabold' : 'text-gray-800'"
                                        class="w-8 text-center font-bold text-sm transition-all duration-150"
                                        x-text="item.quantity"
                                    ></span>
                                    <button 
                                        @click="updateQuantity(index, 1)"
                                        class="w-7 h-7 bg-gray-200 hover:bg-gray-300 active:scale-90 rounded-lg font-bold text-sm transition-all duration-150"
                                    >+</button>
                                </div>
                                <p 
                                    x-data="{ changed: false }"
                                    x-effect="
                                        if (item.subtotal) {
                                            changed = true;
                                            setTimeout(() => changed = false, 300);
                                        }
                                    "
                                    :class="changed ? 'scale-110' : ''"
                                    class="font-bold text-green-600 text-sm transition-transform duration-200"
                                >
                                    R$ <span x-text="formatMoney(item.subtotal)"></span>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Rodapé com Totais e Botões -->
            <div class="border-t-2 border-gray-300 bg-white p-2 md:p-3 flex-shrink-0">
                <div class="mb-2">
                    <label class="text-xs font-medium text-gray-700">Desconto (R$)</label>
                    <input 
                        type="number" 
                        x-model.number="cart.discount"
                        @input="calculateTotals()"
                        step="0.01"
                        class="w-full px-2 py-1.5 border rounded-lg text-sm"
                    >
                </div>

                <div class="space-y-1 mb-2">
                    <div class="flex justify-between text-xs">
                        <span>Subtotal:</span>
                        <span>R$ <span x-text="formatMoney(cart.subtotal)"></span></span>
                    </div>
                    <div x-show="cart.type === 'delivery'" class="flex justify-between text-xs text-blue-600">
                        <span>Taxa de Entrega:</span>
                        <span>R$ <span x-text="formatMoney(cart.delivery_fee)"></span></span>
                    </div>
                    <div x-show="cart.discount > 0" class="flex justify-between text-xs text-red-600">
                        <span>Desconto:</span>
                        <span>- R$ <span x-text="formatMoney(cart.discount)"></span></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t font-bold text-base">
                        <span>TOTAL:</span>
                        <span class="text-green-600">R$ <span x-text="formatMoney(cart.total)"></span></span>
                    </div>
                </div>

                <button
                    @click="finalizeSale()"
                    :disabled="cart.items.length === 0 || isLoading"
                    class="w-full bg-green-600 hover:bg-green-700 active:scale-95 text-white py-2 md:py-2.5 rounded-lg font-bold text-xs md:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md hover:shadow-lg disabled:shadow-none"
                >
                    <span x-show="!isLoading">✅ Finalizar Venda</span>
                    <span x-show="isLoading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processando...
                    </span>
                </button>
            </div>
        </div>

        <!-- COLUNA 3: Pedidos (20%) -->
        <div class="w-full lg:w-[20%] lg:min-w-[250px] bg-gray-50 flex flex-col overflow-hidden order-2 lg:order-3 hidden lg:flex">
            <!-- Tabs -->
            <div class="flex border-b">
                <button
                    @click="activeTab = 'pendentes'"
                    :class="activeTab === 'pendentes' ? 'bg-white text-pink-600 border-b-2 border-pink-600' : 'bg-gray-100 text-gray-600'"
                    class="flex-1 py-3 text-xs font-medium"
                >
                    Pendentes ({{ $pendingSales->count() }})
                </button>
                <button
                    @click="activeTab = 'entrega'"
                    :class="activeTab === 'entrega' ? 'bg-white text-pink-600 border-b-2 border-pink-600' : 'bg-gray-100 text-gray-600'"
                    class="flex-1 py-3 text-xs font-medium"
                >
                    Entrega ({{ $inDeliverySales->count() }})
                </button>
            </div>

            <!-- Pedidos Pendentes -->
            <div x-show="activeTab === 'pendentes'" class="flex-1 overflow-y-auto p-3 min-h-0">
                @if($pendingSales->count() > 0)
                    @foreach($pendingSales as $sale)
                    <div class="bg-white border rounded-lg p-3 mb-2 text-xs cursor-pointer hover:border-pink-400" @click="loadSale({{ $sale->id }})">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold">
                                @if($sale->type === 'balcao')🪑 @else🏍️@endif
                                #{{ $sale->id }}
                            </span>
                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                {{ $sale->status->label() }}
                            </span>
                        </div>
                        @if($sale->table)
                        <p class="text-gray-600 mb-1">Mesa {{ $sale->table->number }}</p>
                        @endif
                        @if($sale->customer)
                        <p class="text-gray-600 mb-1">{{ $sale->customer->name }}</p>
                        @endif
                        <p class="font-bold text-green-600">R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
                        <p class="text-gray-400 mt-1">{{ $sale->created_at->format('H:i') }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-12 text-gray-400">
                        <div class="text-4xl mb-2">📋</div>
                        <p class="text-xs">Sem pedidos</p>
                    </div>
                @endif
            </div>

            <!-- Pedidos em Entrega -->
            <div x-show="activeTab === 'entrega'" class="flex-1 overflow-y-auto p-3 min-h-0">
                @if($inDeliverySales->count() > 0)
                    @foreach($inDeliverySales as $sale)
                    <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-3 mb-2 text-xs">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold">🏍️ #{{ $sale->id }}</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                Em Entrega
                            </span>
                        </div>
                        @if($sale->customer)
                        <p class="text-gray-600 mb-1">{{ $sale->customer->name }}</p>
                        @endif
                        @if($sale->motoboy)
                        <p class="text-gray-600 mb-1">Motoboy: {{ $sale->motoboy->name }}</p>
                        @endif
                        @if($sale->delivery_address)
                        <p class="text-gray-600 mb-2 text-xs break-words">{{ Str::limit($sale->delivery_address, 40) }}</p>
                        @endif
                        <p class="font-bold text-green-600 mb-2">R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
                        <p class="text-gray-400 mb-3">Saiu: {{ $sale->updated_at->format('H:i') }}</p>
                        <button
                            @click="markAsDelivered({{ $sale->id }})"
                            class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded text-xs font-bold"
                        >
                            ✅ Entregue
                        </button>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-12 text-gray-400">
                        <div class="text-4xl mb-2">🏍️</div>
                        <p class="text-xs">Sem entregas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Finalização -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-4">
        <div class="bg-white rounded-2xl p-6 max-w-lg w-full mx-4 relative max-h-[95vh] overflow-y-auto">
            <!-- Botão X para fechar no canto superior direito -->
            <button
                @click="showModal = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center"
                title="Fechar"
            >
                ✕
            </button>
            <h3 class="text-2xl font-bold mb-4 pr-8">💳 Finalizar Venda</h3>

            <!-- Resumo -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span>Subtotal:</span>
                    <span class="font-bold">R$ <span x-text="formatMoney(cart.subtotal)"></span></span>
                </div>
                <div x-show="cart.type === 'delivery'" class="flex justify-between mb-2">
                    <span>Taxa:</span>
                    <span class="text-blue-600">R$ <span x-text="formatMoney(cart.delivery_fee)"></span></span>
                </div>
                <div x-show="cart.discount > 0" class="flex justify-between mb-2">
                    <span>Desconto:</span>
                    <span class="text-red-600">- R$ <span x-text="formatMoney(cart.discount)"></span></span>
                </div>
                <div class="flex justify-between pt-2 border-t text-xl">
                    <span class="font-bold">TOTAL:</span>
                    <span class="font-bold text-green-600">R$ <span x-text="formatMoney(cart.total)"></span></span>
                </div>
            </div>

            <!-- Toggle Pagamento Dividido -->
            <div class="flex items-center justify-between mb-4 p-3 bg-purple-50 rounded-lg">
                <div class="flex items-center gap-2">
                    <span class="text-lg">💰</span>
                    <span class="font-medium text-gray-700">Pagamento Dividido</span>
                </div>
                <button 
                    @click="toggleSplitPayment()"
                    :class="payment.isSplit ? 'bg-purple-600' : 'bg-gray-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                >
                    <span 
                        :class="payment.isSplit ? 'translate-x-6' : 'translate-x-1'"
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    ></span>
                </button>
            </div>

            <!-- Pagamento Simples (Uma forma) -->
            <div x-show="!payment.isSplit">
                <label class="block text-sm font-bold mb-3">Forma de Pagamento:</label>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button
                        @click="selectPaymentMethod('dinheiro')"
                        :class="cart.payment_method === 'dinheiro' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                        class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                    >
                        💵 Dinheiro
                    </button>
                    <button
                        @click="selectPaymentMethod('pix')"
                        :class="cart.payment_method === 'pix' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                        class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                    >
                        📱 PIX
                    </button>
                    <button
                        @click="selectPaymentMethod('cartao_debito')"
                        :class="cart.payment_method === 'cartao_debito' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                        class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                    >
                        💳 Débito
                    </button>
                    <button
                        @click="selectPaymentMethod('cartao_credito')"
                        :class="cart.payment_method === 'cartao_credito' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                        class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                    >
                        💳 Crédito
                    </button>
                </div>

                <!-- Valor Recebido e Troco (apenas para dinheiro) -->
                <div x-show="cart.payment_method === 'dinheiro'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <label class="block text-sm font-bold text-yellow-800 mb-2">💵 Valor Recebido:</label>
                    <input 
                        type="text" 
                        inputmode="decimal"
                        x-model="payment.amountReceivedFormatted"
                        @input="handleAmountReceivedInput($event)"
                        @focus="$event.target.select()"
                        :placeholder="'Mínimo: R$ ' + formatMoney(cart.total)"
                        class="w-full px-4 py-3 border-2 border-yellow-300 rounded-lg text-lg font-bold text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                    >
                    
                    <!-- Atalhos de valor -->
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <button 
                            @click="setAmountReceived(Math.ceil(cart.total / 10) * 10)"
                            class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                        >
                            R$ <span x-text="formatMoney(Math.ceil(cart.total / 10) * 10)"></span>
                        </button>
                        <button 
                            @click="setAmountReceived(Math.ceil(cart.total / 20) * 20)"
                            class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                        >
                            R$ <span x-text="formatMoney(Math.ceil(cart.total / 20) * 20)"></span>
                        </button>
                        <button 
                            @click="setAmountReceived(Math.ceil(cart.total / 50) * 50)"
                            class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                        >
                            R$ <span x-text="formatMoney(Math.ceil(cart.total / 50) * 50)"></span>
                        </button>
                        <button 
                            @click="setAmountReceived(100)"
                            class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                        >
                            R$ 100,00
                        </button>
                    </div>
                    
                    <!-- Troco -->
                    <div x-show="payment.changeAmount > 0" class="mt-3 p-3 bg-green-100 border border-green-300 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-green-800">🔄 TROCO:</span>
                            <span class="text-2xl font-bold text-green-700">R$ <span x-text="formatMoney(payment.changeAmount)"></span></span>
                        </div>
                    </div>
                    
                    <!-- Aviso se valor insuficiente -->
                    <div x-show="payment.amountReceived > 0 && payment.amountReceived < cart.total" class="mt-3 p-3 bg-red-100 border border-red-300 rounded-lg">
                        <span class="text-red-700 font-medium">⚠️ Valor insuficiente! Faltam R$ <span x-text="formatMoney(cart.total - payment.amountReceived)"></span></span>
                    </div>
                </div>
            </div>

            <!-- Pagamento Dividido (Múltiplas formas) -->
            <div x-show="payment.isSplit">
                <label class="block text-sm font-bold mb-3">Formas de Pagamento:</label>
                
                <!-- Lista de pagamentos -->
                <div class="space-y-3 mb-4">
                    <template x-for="(split, index) in payment.splits" :key="index">
                        <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                            <select 
                                x-model="split.method"
                                @change="updateSplitPayments()"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            >
                                <option value="">Selecione...</option>
                                <option value="dinheiro">💵 Dinheiro</option>
                                <option value="pix">📱 PIX</option>
                                <option value="cartao_debito">💳 Débito</option>
                                <option value="cartao_credito">💳 Crédito</option>
                            </select>
                            <input 
                                type="text" 
                                inputmode="decimal"
                                :value="formatMoneyInput(split.value)"
                                @input="handleSplitValueInput($event, index)"
                                @focus="$event.target.select()"
                                placeholder="R$ 0,00"
                                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm text-right"
                            >
                            <button 
                                @click="removeSplitPayment(index)"
                                x-show="payment.splits.length > 1"
                                class="p-2 text-red-500 hover:bg-red-100 rounded-lg"
                            >
                                ✕
                            </button>
                        </div>
                    </template>
                </div>
                
                <!-- Botão adicionar forma -->
                <button 
                    @click="addSplitPayment()"
                    class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-purple-400 hover:text-purple-600 font-medium mb-4"
                >
                    + Adicionar forma de pagamento
                </button>
                
                <!-- Resumo do pagamento dividido -->
                <div class="bg-purple-50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Total informado:</span>
                        <span 
                            class="font-bold"
                            :class="payment.splitTotal >= cart.total ? 'text-green-600' : 'text-red-600'"
                        >
                            R$ <span x-text="formatMoney(payment.splitTotal)"></span>
                        </span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Total da venda:</span>
                        <span class="font-bold">R$ <span x-text="formatMoney(cart.total)"></span></span>
                    </div>
                    <div x-show="payment.splitTotal < cart.total" class="flex justify-between pt-2 border-t border-purple-200">
                        <span class="text-red-600 font-medium">Faltam:</span>
                        <span class="font-bold text-red-600">R$ <span x-text="formatMoney(cart.total - payment.splitTotal)"></span></span>
                    </div>
                    <div x-show="payment.splitTotal > cart.total" class="flex justify-between pt-2 border-t border-purple-200">
                        <span class="text-orange-600 font-medium">Excedente (troco):</span>
                        <span class="font-bold text-orange-600">R$ <span x-text="formatMoney(payment.splitTotal - cart.total)"></span></span>
                    </div>
                </div>
                
                <!-- Campos de troco para dinheiro no split -->
                <template x-for="(split, index) in payment.splits" :key="'cash-' + index">
                    <div x-show="split.method === 'dinheiro' && split.value > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <label class="block text-sm font-bold text-yellow-800 mb-2">
                            💵 Valor Recebido em Dinheiro (Pagamento <span x-text="index + 1"></span>):
                        </label>
                        <input 
                            type="text" 
                            inputmode="decimal"
                            :value="formatMoneyInput(split.amountReceived)"
                            @input="handleSplitAmountReceivedInput($event, index)"
                            @focus="$event.target.select()"
                            :placeholder="'Mínimo: R$ ' + formatMoney(split.value)"
                            class="w-full px-3 py-2 border-2 border-yellow-300 rounded-lg text-center font-bold"
                        >
                        <div x-show="split.changeAmount > 0" class="mt-2 p-2 bg-green-100 rounded flex justify-between items-center">
                            <span class="text-green-800 font-medium">🔄 Troco:</span>
                            <span class="font-bold text-green-700">R$ <span x-text="formatMoney(split.changeAmount)"></span></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Botões -->
            <div class="grid grid-cols-2 gap-3 mt-4">
                <button
                    @click="onlyConfirmSale()"
                    :disabled="!isPaymentValid() || isLoading"
                    class="bg-green-600 hover:bg-green-700 active:scale-95 text-white py-3 rounded-lg font-bold disabled:opacity-50 text-sm transition-all duration-200 shadow-md hover:shadow-lg disabled:shadow-none"
                >
                    <span x-show="!isLoading">✅ Confirmar</span>
                    <span x-show="isLoading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                <button
                    @click="confirmAndPrint()"
                    :disabled="!isPaymentValid() || isLoading"
                    class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white py-3 rounded-lg font-bold disabled:opacity-50 text-sm transition-all duration-200 shadow-md hover:shadow-lg disabled:shadow-none"
                >
                    <span x-show="!isLoading">🖨️ Confirmar & Imprimir</span>
                    <span x-show="isLoading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</main>

<script>
function posSystem() {
    return {
        searchQuery: '',
        selectedCategory: null,
        activeTab: 'pendentes',
        showModal: false,
        isLoading: false,
        lastAddedProduct: null,

        // Occupied tables with sale IDs
        occupiedTables: @json($occupiedTablesWithSales),

        cart: {
            sale_id: null,
            type: 'balcao',
            table_id: null,
            customer_id: null,
            motoboy_id: null,
            delivery_address: '',
            delivery_fee: 0.00,
            payment_method: null,
            items: [],
            subtotal: 0,
            discount: 0,
            total: 0
        },
        
        // Sistema de pagamento expandido
        payment: {
            isSplit: false, // Se é pagamento dividido
            amountReceived: 0, // Valor recebido (para dinheiro)
            amountReceivedFormatted: '', // Valor formatado para exibição
            changeAmount: 0, // Troco
            splits: [ // Array de pagamentos divididos
                { method: '', value: 0, amountReceived: 0, changeAmount: 0 }
            ],
            splitTotal: 0 // Total dos pagamentos divididos
        },
        
        selectedTableOccupied: false,

        init() {
            console.log('PDV iniciado');
            this.setType(this.cart.type); // Inicializar tipo corretamente
            this.updateTableOptions(); // Desabilitar mesas ocupadas no select
            this.resetPayment(); // Inicializar pagamento
        },
        
        // ===== FUNÇÕES DE PAGAMENTO =====
        
        resetPayment() {
            this.payment = {
                isSplit: false,
                amountReceived: 0,
                amountReceivedFormatted: '',
                changeAmount: 0,
                splits: [{ method: '', value: 0, amountReceived: 0, changeAmount: 0 }],
                splitTotal: 0
            };
        },
        
        // ===== FUNÇÕES DE MÁSCARA MONETÁRIA =====
        
        // Formata valor numérico para exibição no input (R$ 1.234,56)
        formatMoneyInput(value) {
            if (value === 0 || value === '' || value === null || value === undefined) return '';
            return parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        
        // Converte string formatada para número
        parseMoneyInput(value) {
            if (!value || value === '') return 0;
            // Remove pontos (milhares) e substitui vírgula por ponto (decimal)
            const cleaned = value.toString()
                .replace(/[^\d,.-]/g, '') // Remove tudo exceto números, vírgula, ponto e hífen
                .replace(/\./g, '') // Remove pontos (milhares)
                .replace(',', '.'); // Substitui vírgula por ponto
            return parseFloat(cleaned) || 0;
        },
        
        // Handler para input do valor recebido (pagamento simples)
        handleAmountReceivedInput(event) {
            const rawValue = event.target.value;
            const numericValue = this.parseMoneyInput(rawValue);
            this.payment.amountReceived = numericValue;
            this.payment.amountReceivedFormatted = this.formatMoneyInput(numericValue);
            this.calculateChange();
        },
        
        // Handler para input do valor no pagamento dividido
        handleSplitValueInput(event, index) {
            const rawValue = event.target.value;
            const numericValue = this.parseMoneyInput(rawValue);
            this.payment.splits[index].value = numericValue;
            // Atualiza o input com valor formatado
            this.$nextTick(() => {
                event.target.value = this.formatMoneyInput(numericValue);
            });
            this.updateSplitPayments();
        },
        
        // Handler para input do valor recebido no pagamento dividido
        handleSplitAmountReceivedInput(event, index) {
            const rawValue = event.target.value;
            const numericValue = this.parseMoneyInput(rawValue);
            this.payment.splits[index].amountReceived = numericValue;
            // Atualiza o input com valor formatado
            this.$nextTick(() => {
                event.target.value = this.formatMoneyInput(numericValue);
            });
            this.calculateSplitChange(index);
        },
        
        toggleSplitPayment() {
            this.payment.isSplit = !this.payment.isSplit;
            if (this.payment.isSplit) {
                // Iniciar com o valor total no primeiro split
                this.payment.splits = [{ method: '', value: this.cart.total, amountReceived: 0, changeAmount: 0 }];
                this.cart.payment_method = null;
                // Limpar valor formatado do pagamento simples
                this.payment.amountReceivedFormatted = '';
                this.payment.amountReceived = 0;
            } else {
                this.payment.splits = [{ method: '', value: 0, amountReceived: 0, changeAmount: 0 }];
            }
            this.updateSplitPayments();
        },
        
        selectPaymentMethod(method) {
            this.cart.payment_method = method;
            // Resetar valor recebido ao mudar método
            if (method !== 'dinheiro') {
                this.payment.amountReceived = 0;
                this.payment.amountReceivedFormatted = '';
                this.payment.changeAmount = 0;
            }
        },
        
        setAmountReceived(value) {
            this.payment.amountReceived = value;
            this.payment.amountReceivedFormatted = this.formatMoneyInput(value);
            this.calculateChange();
        },
        
        calculateChange() {
            if (this.payment.amountReceived >= this.cart.total) {
                this.payment.changeAmount = this.payment.amountReceived - this.cart.total;
            } else {
                this.payment.changeAmount = 0;
            }
        },
        
        addSplitPayment() {
            this.payment.splits.push({ method: '', value: 0, amountReceived: 0, changeAmount: 0 });
        },
        
        removeSplitPayment(index) {
            if (this.payment.splits.length > 1) {
                this.payment.splits.splice(index, 1);
                this.updateSplitPayments();
            }
        },
        
        updateSplitPayments() {
            this.payment.splitTotal = this.payment.splits.reduce((sum, split) => sum + (parseFloat(split.value) || 0), 0);
        },
        
        calculateSplitChange(index) {
            const split = this.payment.splits[index];
            if (split.amountReceived >= split.value) {
                split.changeAmount = split.amountReceived - split.value;
            } else {
                split.changeAmount = 0;
            }
        },
        
        isPaymentValid() {
            if (this.payment.isSplit) {
                // Verificar se todos os splits têm método selecionado
                const allMethodsSelected = this.payment.splits.every(split => split.method && split.value > 0);
                // Verificar se o total cobre a venda
                const totalCovers = this.payment.splitTotal >= this.cart.total;
                // Verificar se pagamentos em dinheiro têm valor recebido suficiente
                const cashValid = this.payment.splits.every(split => {
                    if (split.method === 'dinheiro' && split.value > 0) {
                        return split.amountReceived >= split.value;
                    }
                    return true;
                });
                return allMethodsSelected && totalCovers && cashValid;
            } else {
                // Pagamento simples
                if (!this.cart.payment_method) return false;
                // Se for dinheiro, verificar valor recebido
                if (this.cart.payment_method === 'dinheiro') {
                    return this.payment.amountReceived >= this.cart.total;
                }
                return true;
            }
        },
        
        getPaymentDataForSubmit() {
            if (this.payment.isSplit) {
                return {
                    payment_method: 'split', // Indica pagamento dividido
                    payment_methods_split: this.payment.splits.map(split => ({
                        method: split.method,
                        value: split.value,
                        amount_received: split.method === 'dinheiro' ? split.amountReceived : null,
                        change_amount: split.method === 'dinheiro' ? split.changeAmount : null
                    })),
                    amount_received: null,
                    change_amount: this.payment.splitTotal > this.cart.total ? this.payment.splitTotal - this.cart.total : 0
                };
            } else {
                return {
                    payment_method: this.cart.payment_method,
                    payment_methods_split: null,
                    amount_received: this.cart.payment_method === 'dinheiro' ? this.payment.amountReceived : null,
                    change_amount: this.cart.payment_method === 'dinheiro' ? this.payment.changeAmount : null
                };
            }
        },
        
        updateTableOptions() {
            // Desabilitar mesas ocupadas no select quando não houver sale_id
            // ou quando houver sale_id mas a mesa ocupada não for da venda atual
            this.$nextTick(() => {
                const select = document.getElementById('tableSelect');
                if (select) {
                    const options = select.querySelectorAll('option[data-table-id]');
                    options.forEach(option => {
                        const tableId = parseInt(option.getAttribute('data-table-id'));
                        const isOccupied = this.occupiedTables[tableId] && this.occupiedTables[tableId] !== null;
                        const isCurrentSale = this.cart.sale_id && this.occupiedTables[tableId] === this.cart.sale_id;
                        
                        // Desabilitar se:
                        // 1. Mesa está ocupada E
                        // 2. (Não há venda atual OU a mesa ocupada não é da venda atual)
                        if (isOccupied && (!this.cart.sale_id || !isCurrentSale)) {
                            option.disabled = true;
                            option.style.color = '#9ca3af'; // Cinza para indicar desabilitado
                        } else {
                            option.disabled = false;
                            option.style.color = '';
                        }
                    });
                }
            });
        },

        formatMoney(value) {
            return parseFloat(value || 0).toFixed(2).replace('.', ',');
        },

        productMatchesSearch(name) {
            if (!this.searchQuery) return true;
            return name.toLowerCase().includes(this.searchQuery.toLowerCase());
        },

        setType(type) {
            this.cart.type = type;
            if (type === 'balcao') {
                this.cart.delivery_fee = 0;
                this.cart.delivery_address = '';
                this.cart.motoboy_id = null;
            } else {
                this.cart.table_id = null;
                this.cart.delivery_fee = 5.00;
            }
            this.calculateTotals();
        },

        onTableChange() {
            const tableId = this.cart.table_id;
            
            // Verificar se a mesa está ocupada
            if (tableId && this.occupiedTables[tableId]) {
                // Se estiver editando uma venda existente, pode carregar a mesa ocupada dela
                if (this.cart.sale_id && this.occupiedTables[tableId] === this.cart.sale_id) {
                    // É a mesa da venda atual, permitir
                    this.selectedTableOccupied = false;
                    this.updateTableOptions();
                    return;
                }
                
                // Se é uma nova venda (sem sale_id), não permitir selecionar mesa ocupada
                if (!this.cart.sale_id) {
                    this.selectedTableOccupied = true;
                    this.showToast('⚠️ Esta mesa está ocupada! Não é possível selecioná-la para uma nova venda.', 'error');
                    // Limpar a seleção
                    this.$nextTick(() => {
                        this.cart.table_id = null;
                        this.updateTableOptions();
                    });
                    
                    // Tentar carregar a venda existente da mesa ocupada (opcional)
                    const saleId = this.occupiedTables[tableId];
                    if (confirm('Esta mesa está ocupada. Deseja carregar a venda existente desta mesa?')) {
                        this.loadSale(saleId);
                    }
                    return;
                }
                
                // Se estiver editando uma venda, mas tentando selecionar mesa de outra venda
                if (this.cart.sale_id && this.occupiedTables[tableId] !== this.cart.sale_id) {
                    this.selectedTableOccupied = true;
                    this.showToast('⚠️ Esta mesa está ocupada por outra venda! Não é possível selecioná-la.', 'error');
                    // Restaurar mesa anterior
                    this.$nextTick(() => {
                        this.cart.table_id = null;
                        this.updateTableOptions();
                    });
                    return;
                }
                
                // Carregar venda da mesa ocupada (caso seja permitido)
                const saleId = this.occupiedTables[tableId];
                this.loadSale(saleId);
            } else {
                // Mesa disponível ou nenhuma mesa selecionada
                this.selectedTableOccupied = false;
                this.updateTableOptions();
                
                if (!tableId) {
                    // No table selected, clear any loaded sale se não for edição
                    if (!this.cart.sale_id) {
                        this.cart.sale_id = null;
                    }
                }
            }
        },

        addItem(id, name, price, category) {
            const existing = this.cart.items.find(item => item.product_id === id);
            if (existing) {
                existing.quantity++;
                existing.subtotal = existing.quantity * existing.price;
            } else {
                this.cart.items.push({
                    product_id: id,
                    name: name,
                    price: parseFloat(price),
                    category: category,
                    quantity: 1,
                    subtotal: parseFloat(price)
                });
            }
            this.lastAddedProduct = id;
            this.calculateTotals();
            this.showToast(`✨ ${name} adicionado ao carrinho!`, 'success');
        },

        addItemWithFeedback(id, name, price, category) {
            // Adiciona feedback visual no botão
            const button = event.currentTarget;
            button.classList.add('animate-pulse');
            
            this.addItem(id, name, price, category);
            
            // Remove feedback visual após animação
            setTimeout(() => {
                button.classList.remove('animate-pulse');
            }, 300);
        },

        removeItem(index) {
            const item = this.cart.items[index];
            this.cart.items.splice(index, 1);
            this.calculateTotals();
            this.showToast(`${item.name} removido do carrinho`, 'info');
        },

        removeItemWithFeedback(index) {
            const item = this.cart.items[index];
            this.removeItem(index);
        },

        updateQuantity(index, change) {
            const item = this.cart.items[index];
            item.quantity += change;
            if (item.quantity <= 0) {
                this.removeItem(index);
            } else {
                item.subtotal = item.quantity * item.price;
                this.calculateTotals();
                // Feedback visual sutil para mudança de quantidade
                if (change > 0) {
                    this.showToast(`Quantidade de ${item.name} aumentada`, 'info');
                }
            }
        },

        calculateTotals() {
            this.cart.subtotal = this.cart.items.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
            const fee = parseFloat(this.cart.delivery_fee) || 0;
            const discount = parseFloat(this.cart.discount) || 0;
            this.cart.total = this.cart.subtotal + fee - discount;
        },

        finalizeSale() {
            if (this.cart.items.length === 0) {
                this.showToast('⚠️ Adicione produtos ao carrinho!', 'error');
                return;
            }

            if (this.cart.type === 'delivery') {
                if (!this.cart.motoboy_id) {
                    this.showToast('⚠️ Selecione um motoboy!', 'error');
                    return;
                }
            }

            // Resetar estado do pagamento ao abrir modal
            this.resetPayment();
            this.showModal = true;
        },

        async confirmSale(closeAccount = false) {
            if (!this.isPaymentValid()) {
                this.showToast('⚠️ Complete os dados de pagamento!', 'error');
                return;
            }

            this.isLoading = true;
            try {
                let url, method;
                if (this.cart.sale_id) {
                    // Update existing sale
                    url = `/gestor/vendas/${this.cart.sale_id}`;
                    method = 'PUT';
                } else {
                    // Create new sale
                    url = '/gestor/vendas';
                    method = 'POST';
                }

                // Obter dados de pagamento
                const paymentData = this.getPaymentDataForSubmit();

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ...this.cart,
                        ...paymentData,
                        finalize: true,
                        close_account: closeAccount // true = fechar conta da mesa, false = apenas atualizar pedido
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Store the sale ID (important for printing)
                    const saleId = data.sale?.id || this.cart.sale_id;
                    this.cart.sale_id = saleId;

                    // Mensagem diferente baseada no tipo de operação
                    const message = closeAccount ? '🎉 Venda finalizada com sucesso!' : '✅ Pedido atualizado com sucesso!';
                    this.showToast(message, 'success');
                    return saleId;
                } else {
                    throw new Error(data.message || 'Erro ao finalizar venda');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('❌ Erro: ' + error.message, 'error');
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        async printSale() {
            if (!this.cart.payment_method) {
                this.showToast('Selecione a forma de pagamento!', 'error');
                return;
            }

            try {
                // First finalize the sale
                const saleId = await this.confirmSale();

                // Then print
                const response = await fetch(`/gestor/vendas/${saleId}/print-receipt`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.showToast('🖨️ Recibo impresso com sucesso!', 'success');
                    this.showModal = false;
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast('❌ Erro ao imprimir: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Erro na impressão:', error);
                this.showToast('❌ Erro na impressão: ' + error.message, 'error');
            }
        },

        async onlyConfirmSale() {
            try {
                // Se é venda de mesa (table_id definido), apenas atualiza sem fechar conta
                // Se é venda rápida (sem mesa), finaliza normalmente
                const isTableSale = !!this.cart.table_id;
                const closeAccount = !isTableSale; // Só fecha conta se NÃO tiver mesa
                
                await this.confirmSale(closeAccount);
                this.showModal = false;
                this.selectedTableOccupied = false;
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Erro ao confirmar venda:', error);
                this.showToast('❌ Erro ao confirmar venda: ' + error.message, 'error');
            }
        },

        async confirmAndPrint() {
            try {
                // Confirmar e Imprimir sempre fecha a conta (closeAccount = true)
                // pois indica que o cliente está pagando e saindo
                const saleId = await this.confirmSale(true);

                // Fechar modal imediatamente após confirmar a venda
                this.showModal = false;
                
                // Tentar imprimir via QZ Tray em background (não bloqueia)
                this.printViaQZTray(saleId).catch(error => {
                    console.log('Impressão em background falhou:', error.message);
                });

                // Recarregar página após um breve delay
                setTimeout(() => window.location.reload(), 1500);
            } catch (error) {
                console.error('Erro:', error);
                // Se a venda falhou, mostrar erro
                this.showToast('❌ Erro ao confirmar venda: ' + error.message, 'error');
            }
        },

        async printViaQZTray(saleId) {
            try {
                // Tentar usar Printer Agent primeiro (mais confiável)
                if (typeof PrinterAgent !== 'undefined') {
                    const result = await PrinterAgent.printSaleReceipt(saleId);
                    if (result.success) {
                        const methodName = result.method === 'agent' ? 'Agente' : 
                                          result.method === 'qz-tray' ? 'QZ Tray' : 'Servidor';
                        this.showToast(`🖨️ Recibo impresso via ${methodName}!`, 'success');
                        return;
                    }
                }

                // Fallback: método antigo QZ Tray
                if (typeof QZPrint === 'undefined') {
                    console.log('QZ Tray não carregado - impressão ignorada');
                    return;
                }

                // Verificar conexão com QZ Tray (com timeout curto)
                if (!QZPrint.isConnected()) {
                    try {
                        const connected = await Promise.race([
                            QZPrint.init(),
                            new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 3000))
                        ]);
                        if (!connected) {
                            console.log('QZ Tray não conectado - impressão ignorada');
                            return;
                        }
                    } catch (e) {
                        console.log('QZ Tray não disponível - impressão ignorada');
                        return;
                    }
                }

                // Verificar se há impressora configurada
                const printerName = QZPrint.getPrinter();
                if (!printerName) {
                    this.showToast('⚠️ Nenhuma impressora configurada. Configure em Configurações > Impressora.', 'info');
                    return;
                }

                // Buscar dados do recibo do servidor
                const response = await fetch(`/gestor/vendas/${saleId}/receipt-data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Erro ao obter dados do recibo');
                }

                // Imprimir via QZ Tray
                await QZPrint.printReceipt(data.receipt);
                this.showToast('🖨️ Recibo impresso com sucesso!', 'success');

            } catch (error) {
                console.error('Erro ao imprimir:', error);
                // Não mostrar toast de erro intrusivo, apenas logar
                // A venda já foi confirmada com sucesso
            }
        },

        async loadSale(id) {
            try {
                const response = await fetch(`/gestor/vendas/${id}/pos-data`);
                const data = await response.json();

                if (data.success) {
                    const sale = data.sale;
                    this.cart = {
                        sale_id: sale.id,
                        type: sale.type,
                        table_id: sale.table_id,
                        customer_id: sale.customer_id,
                        motoboy_id: sale.motoboy_id,
                        delivery_address: sale.delivery_address || '',
                        delivery_fee: parseFloat(sale.delivery_fee) || 0,
                        payment_method: sale.payment_method,
                        items: sale.items,
                        subtotal: 0,
                        discount: parseFloat(sale.discount) || 0,
                        total: 0
                    };
                    this.calculateTotals();
                    this.selectedTableOccupied = false;
                    this.updateTableOptions(); // Atualizar opções após carregar venda
                    this.showToast('📋 Venda carregada com sucesso!', 'success');
                }
            } catch (error) {
                this.showToast('❌ Erro ao carregar venda', 'error');
            }
        },

        async markAsDelivered(id) {
            if (!confirm('Confirmar entrega?')) return;

            try {
                const response = await fetch(`/gestor/vendas/${id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: 'entregue' })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    // Erro HTTP (422, 500, etc.)
                    const errorMessage = data.message || `Erro ${response.status}: ${response.statusText}`;
                    this.showToast(`❌ ${errorMessage}`, 'error');
                    console.error('Erro ao atualizar status:', data);
                    return;
                }

                if (data.success) {
                    this.showToast('✅ Pedido marcado como entregue!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast(`❌ ${data.message || 'Erro ao atualizar status'}`, 'error');
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                this.showToast('❌ Erro ao atualizar status: ' + error.message, 'error');
            }
        },

        showToast(message, type) {
            const configs = {
                success: {
                    bg: 'bg-green-500',
                    icon: '✅',
                    border: 'border-green-600'
                },
                error: {
                    bg: 'bg-red-500',
                    icon: '❌',
                    border: 'border-red-600'
                },
                info: {
                    bg: 'bg-blue-500',
                    icon: 'ℹ️',
                    border: 'border-blue-600'
                }
            };
            
            const config = configs[type] || configs.info;
            const toastId = 'toast-' + Date.now();
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `fixed top-4 right-4 ${config.bg} ${config.border} border-2 text-white px-6 py-3 rounded-lg shadow-2xl z-50 transform transition-all duration-300 translate-x-full opacity-0 flex items-center gap-2 font-medium`;
            toast.innerHTML = `
                <span class="text-xl">${config.icon}</span>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            // Animar entrada
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 10);
            
            // Animar saída
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                toast.style.opacity = '0';
                setTimeout(() => {
                    const element = document.getElementById(toastId);
                    if (element) element.remove();
                }, 300);
            }, 3000);
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

/* Animação suave para produtos adicionados */
@keyframes pulse-subtle {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
}

/* Melhorar feedback visual em inputs e selects */
input:focus, select:focus, textarea:focus {
    outline: none;
    ring: 2px;
    ring-color: rgb(236, 72, 153);
}

/* Transições suaves para botões */
button {
    transition-property: transform, box-shadow, background-color, border-color;
    transition-duration: 150ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects melhorados */
button:hover:not(:disabled) {
    transform: translateY(-1px);
}

button:active:not(:disabled) {
    transform: translateY(0);
}

/* Animação para valores que mudam */
@keyframes highlight {
    0%, 100% {
        background-color: transparent;
    }
    50% {
        background-color: rgba(34, 197, 94, 0.1);
    }
}
</style>
@endsection
