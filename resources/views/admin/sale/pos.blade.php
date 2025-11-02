@extends('layouts.admin')

@section('title', 'PDV - Ponto de Venda')

@section('admin-content')
<main class="flex-1 relative overflow-hidden bg-gray-50" x-data="posSystem()">
    <div class="h-screen flex overflow-hidden">
        
        <!-- COLUNA 1: Produtos (40%) -->
        <div class="w-[40%] min-w-[300px] bg-white border-r border-gray-200 flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-pink-50 to-purple-50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center text-white text-xl">
                        🛒
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">PDV - Ponto de Venda</h2>
                        <p class="text-xs text-gray-600">Caixa #{{ $openCashRegister->id }} - {{ Auth::user()->name }}</p>
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
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap"
                >
                    🍽️ Todos
                </button>
                @foreach($categories as $category)
                <button
                    @click="selectedCategory = {{ $category->id }}"
                    :class="selectedCategory === {{ $category->id }} ? 'bg-pink-500 text-white' : 'bg-white text-gray-700'"
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap"
                >
                    {{ $category->emoji ?? '📦' }} {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Grid de Produtos -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-3 gap-3">
                    @foreach($products as $product)
                    <button
                        @click="addItemWithFeedback({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->category->name ?? 'Sem categoria' }}')"
                        x-show="(selectedCategory === null || selectedCategory === {{ $product->category_id }}) && productMatchesSearch('{{ addslashes($product->name) }}')"
                        x-data="{ justAdded: false }"
                        :class="justAdded ? 'scale-95 border-green-500 bg-green-50' : 'bg-white border-2 border-gray-200 hover:border-pink-500 hover:shadow-lg'"
                        class="rounded-xl p-4 transition-all duration-200 text-left active:scale-95"
                    >
                        <div class="h-20 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg flex items-center justify-center mb-2 transition-transform duration-200 hover:scale-105">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover rounded-lg">
                            @else
                                <span class="text-3xl">{{ $product->category->emoji ?? '🍰' }}</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-gray-800 text-xs mb-1 line-clamp-2">{{ $product->name }}</h4>
                        <p class="text-sm font-bold text-pink-600">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- COLUNA 2: Carrinho (40%) -->
        <div class="w-[40%] min-w-[350px] bg-white flex flex-col border-r border-gray-200 overflow-hidden">
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
                                        Endereço <span class="text-red-500">*</span>
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
            <div class="overflow-y-auto p-3 min-h-0" style="max-height: calc(100vh - 450px);">
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
            <div class="border-t-2 border-gray-300 bg-white p-3 flex-shrink-0">
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
                    class="w-full bg-green-600 hover:bg-green-700 active:scale-95 text-white py-2.5 rounded-lg font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md hover:shadow-lg disabled:shadow-none"
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
        <div class="w-[20%] min-w-[250px] bg-gray-50 flex flex-col overflow-hidden">
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
                                {{ ucfirst($sale->status) }}
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
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 relative">
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

            <!-- Forma de Pagamento -->
            <label class="block text-sm font-bold mb-3">Forma de Pagamento:</label>
            <div class="grid grid-cols-2 gap-2 mb-6">
                <button
                    @click="cart.payment_method = 'dinheiro'"
                    :class="cart.payment_method === 'dinheiro' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                    class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                >
                    💵 Dinheiro
                </button>
                <button
                    @click="cart.payment_method = 'pix'"
                    :class="cart.payment_method === 'pix' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                    class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                >
                    📱 PIX
                </button>
                <button
                    @click="cart.payment_method = 'cartao_debito'"
                    :class="cart.payment_method === 'cartao_debito' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                    class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                >
                    💳 Débito
                </button>
                <button
                    @click="cart.payment_method = 'cartao_credito'"
                    :class="cart.payment_method === 'cartao_credito' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                    class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                >
                    💳 Crédito
                </button>
            </div>

            <!-- Botões -->
            <div class="grid grid-cols-2 gap-3">
                <button
                    @click="onlyConfirmSale()"
                    :disabled="!cart.payment_method || isLoading"
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
                    :disabled="!cart.payment_method || isLoading"
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
        
        selectedTableOccupied: false,

        init() {
            console.log('PDV iniciado');
            this.setType(this.cart.type); // Inicializar tipo corretamente
            this.updateTableOptions(); // Desabilitar mesas ocupadas no select
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
                if (!this.cart.delivery_address || this.cart.delivery_address.trim() === '') {
                    this.showToast('⚠️ Endereço é obrigatório para delivery!', 'error');
                    return;
                }
                if (!this.cart.motoboy_id) {
                    this.showToast('⚠️ Selecione um motoboy!', 'error');
                    return;
                }
            }

            this.showModal = true;
        },

        async confirmSale() {
            if (!this.cart.payment_method) {
                this.showToast('⚠️ Selecione a forma de pagamento!', 'error');
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

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ...this.cart,
                        finalize: true
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Store the sale ID (important for printing)
                    const saleId = data.sale?.id || this.cart.sale_id;
                    this.cart.sale_id = saleId;

                    this.showToast('🎉 Venda finalizada com sucesso!', 'success');
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
                await this.confirmSale();
                this.showToast('✅ Venda confirmada!', 'success');
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
                const saleId = await this.confirmSale();

                // Try to print after confirmation (but don't fail if printing fails)
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
                    this.showToast('🎉 Venda confirmada e recibo impresso!', 'success');
                } else {
                    // Print failed, but sale is still confirmed
                    this.showToast('✅ Venda confirmada! (Impressora indisponível)', 'info');
                }

                this.showModal = false;
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Erro:', error);
                // Even if printing fails, the sale should already be confirmed at this point
                this.showToast('✅ Venda confirmada! (Erro na impressão)', 'info');
                this.showModal = false;
                setTimeout(() => window.location.reload(), 1000);
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
                if (data.success) {
                    this.showToast('✅ Pedido marcado como entregue!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                this.showToast('❌ Erro ao atualizar status', 'error');
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
