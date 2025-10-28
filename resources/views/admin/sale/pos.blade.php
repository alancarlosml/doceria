@extends('layouts.admin')

@section('title', 'PDV - Ponto de Venda')

@section('admin-content')
<main class="flex-1 relative overflow-hidden bg-gray-50" x-data="posSystem()">
    <div class="h-screen flex">
        
        <!-- COLUNA 1: Produtos (40%) -->
        <div class="w-[40%] bg-white border-r border-gray-200 flex flex-col">
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
                        @click="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->category->name ?? 'Sem categoria' }}')"
                        x-show="(selectedCategory === null || selectedCategory === {{ $product->category_id }}) && productMatchesSearch('{{ addslashes($product->name) }}')"
                        class="bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-pink-500 hover:shadow-lg transition-all text-left"
                    >
                        <div class="h-20 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg flex items-center justify-center mb-2">
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
        <div class="w-[40%] bg-white flex flex-col border-r border-gray-200">
            <!-- Header do Carrinho -->
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span>🧾</span>
                    <span>Pedido Atual</span>
                </h3>

                <!-- Tipo de Venda -->
                <div class="flex gap-2 mb-3">
                    <button
                        @click="setType('balcao')"
                        :class="cart.type === 'balcao' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm"
                    >
                        🪑 Balcão
                    </button>
                    <button
                        @click="setType('delivery')"
                        :class="cart.type === 'delivery' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 border'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm"
                    >
                        🏍️ Delivery
                    </button>
                </div>

                <!-- Seleção de Mesa (Balcão) -->
                <div x-show="cart.type === 'balcao'" class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mesa (Opcional)</label>
                    <select
                        x-model.number="cart.table_id"
                        @change="onTableChange"
                        class="w-full flex items-center justify-between px-3 py-2 bg-white border-2 rounded-lg text-sm font-medium transition-all"
                    >
                        <option value="">Sem mesa (venda rápida)</option>
                        @php
                            // Mesclar todas as mesas ordenadas
                            $allTables = $tables->merge($occupiedTables)->sortBy('number');
                        @endphp
                        @foreach($allTables as $table)
                        <option value="{{ $table->id }}">
                            Mesa {{ $table->number }} ({{ $table->capacity }} pessoas)
                            @if($table->status === 'ocupada')
                                - ⚠️ Ocupada
                            @else
                                - ✅ Disponível
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botão para Abrir Detalhes (Delivery/Encomenda) -->
                <div x-show="cart.type !== 'balcao'" class="mt-3" x-data="{ detailsOpen: false }">
                    <button 
                        @click="detailsOpen = !detailsOpen"
                        class="w-full flex items-center justify-between px-3 py-2 bg-white border-2 rounded-lg text-sm font-medium transition-all"
                        :class="detailsOpen ? 'border-blue-500 text-blue-700' : 'border-gray-300 text-gray-700 hover:border-gray-400'"
                    >
                        <span x-show="cart.type === 'delivery'">📋 Detalhes da Entrega</span>
                        {{-- <span x-show="cart.type === 'encomenda'">📋 Detalhes da Encomenda</span> --}}
                        <svg class="w-5 h-5 transition-transform" :class="detailsOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Collapse com Campos Adicionais -->
                    <div 
                        x-show="detailsOpen" 
                        x-collapse
                        class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-3"
                    >
                        <!-- Cliente -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                            <select 
                                x-model="cart.customer_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500"
                            >
                                <option value="">Selecione um cliente</option>
                                @foreach($recentCustomers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Delivery -->
                        <template x-if="cart.type === 'delivery'">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Endereço <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        x-model="cart.delivery_address"
                                        rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                        placeholder="Rua, número, complemento..."
                                    ></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Taxa (R$)</label>
                                        <input
                                            type="number"
                                            x-model.number="cart.delivery_fee"
                                            @input="calculateTotals()"
                                            step="0.50"
                                            min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                            placeholder="5.00"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Motoboy <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            x-model="cart.motoboy_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
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
            <div class="flex-1 overflow-y-auto p-4 max-h-[40vh]">
                <div x-show="cart.items.length === 0" class="text-center py-8 text-gray-400">
                    <div class="text-4xl mb-2">🛒</div>
                    <p class="text-sm">Carrinho vazio</p>
                </div>

                <div class="space-y-2">
                    <template x-for="(item, index) in cart.items" :key="index">
                        <div class="bg-gray-50 rounded-lg p-3 border">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-sm" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="'R$ ' + formatMoney(item.price) + ' cada'"></p>
                                </div>
                                <button @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                    ✕
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button 
                                        @click="updateQuantity(index, -1)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold"
                                    >−</button>
                                    <span class="w-8 text-center font-bold" x-text="item.quantity"></span>
                                    <button 
                                        @click="updateQuantity(index, 1)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg font-bold"
                                    >+</button>
                                </div>
                                <p class="font-bold text-green-600">R$ <span x-text="formatMoney(item.subtotal)"></span></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Rodapé com Totais e Botões -->
            <div class="border-t-2 border-gray-300 bg-white p-4">
                <div class="mb-3">
                    <label class="text-sm font-medium text-gray-700">Desconto (R$)</label>
                    <input 
                        type="number" 
                        x-model.number="cart.discount"
                        @input="calculateTotals()"
                        step="0.01"
                        class="w-full px-3 py-2 border rounded-lg text-sm"
                    >
                </div>

                <div class="space-y-1 mb-3">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal:</span>
                        <span>R$ <span x-text="formatMoney(cart.subtotal)"></span></span>
                    </div>
                    <div x-show="cart.type === 'delivery'" class="flex justify-between text-sm text-blue-600">
                        <span>Taxa de Entrega:</span>
                        <span>R$ <span x-text="formatMoney(cart.delivery_fee)"></span></span>
                    </div>
                    <div x-show="cart.discount > 0" class="flex justify-between text-sm text-red-600">
                        <span>Desconto:</span>
                        <span>- R$ <span x-text="formatMoney(cart.discount)"></span></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t font-bold text-lg">
                        <span>TOTAL:</span>
                        <span class="text-green-600">R$ <span x-text="formatMoney(cart.total)"></span></span>
                    </div>
                </div>

                <button
                    @click="finalizeSale()"
                    :disabled="cart.items.length === 0"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    ✅ Finalizar Venda
                </button>
            </div>
        </div>

        <!-- COLUNA 3: Pedidos (20%) -->
        <div class="w-[20%] bg-gray-50 flex flex-col">
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
            <div x-show="activeTab === 'pendentes'" class="flex-1 overflow-y-auto p-3">
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
            <div x-show="activeTab === 'entrega'" class="flex-1 overflow-y-auto p-3">
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
                    :class="cart.payment_method === 'dinheiro' ? 'bg-green-500 text-white' : 'bg-white border-2'"
                    class="py-3 rounded-lg font-medium"
                >
                    💵 Dinheiro
                </button>
                <button
                    @click="cart.payment_method = 'pix'"
                    :class="cart.payment_method === 'pix' ? 'bg-green-500 text-white' : 'bg-white border-2'"
                    class="py-3 rounded-lg font-medium"
                >
                    📱 PIX
                </button>
                <button
                    @click="cart.payment_method = 'cartao_debito'"
                    :class="cart.payment_method === 'cartao_debito' ? 'bg-green-500 text-white' : 'bg-white border-2'"
                    class="py-3 rounded-lg font-medium"
                >
                    💳 Débito
                </button>
                <button
                    @click="cart.payment_method = 'cartao_credito'"
                    :class="cart.payment_method === 'cartao_credito' ? 'bg-green-500 text-white' : 'bg-white border-2'"
                    class="py-3 rounded-lg font-medium"
                >
                    💳 Crédito
                </button>
            </div>

            <!-- Botões -->
            <div class="grid grid-cols-2 gap-3">
                <button
                    @click="onlyConfirmSale()"
                    :disabled="!cart.payment_method"
                    class="bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold disabled:opacity-50 text-sm"
                >
                    ✅ Confirmar
                </button>
                <button
                    @click="confirmAndPrint()"
                    :disabled="!cart.payment_method"
                    class="bg-blue-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 text-sm"
                >
                    🖨️ Confirmar & Imprimir
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

        init() {
            console.log('PDV iniciado');
            this.setType(this.cart.type); // Inicializar tipo corretamente
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
            // When table selection changes, check if it's occupied and load the sale
            const tableId = this.cart.table_id;
            if (tableId && this.occupiedTables[tableId]) {
                // Table is occupied, load the existing sale
                const saleId = this.occupiedTables[tableId];
                this.loadSale(saleId);
            } else if (!tableId) {
                // No table selected, clear any loaded sale
                this.cart.sale_id = null;
            }
            // If table is available (not occupied), just set table_id without loading sale
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
            this.calculateTotals();
            this.showToast(`${name} adicionado!`, 'success');
        },

        removeItem(index) {
            this.cart.items.splice(index, 1);
            this.calculateTotals();
        },

        updateQuantity(index, change) {
            const item = this.cart.items[index];
            item.quantity += change;
            if (item.quantity <= 0) {
                this.removeItem(index);
            } else {
                item.subtotal = item.quantity * item.price;
                this.calculateTotals();
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
                this.showToast('Adicione produtos ao carrinho!', 'error');
                return;
            }

            if (this.cart.type === 'delivery') {
                if (!this.cart.delivery_address || this.cart.delivery_address.trim() === '') {
                    this.showToast('Endereço é obrigatório para delivery!', 'error');
                    return;
                }
                if (!this.cart.motoboy_id) {
                    this.showToast('Selecione um motoboy!', 'error');
                    return;
                }
            }

            this.showModal = true;
        },

        async confirmSale() {
            if (!this.cart.payment_method) {
                this.showToast('Selecione a forma de pagamento!', 'error');
                return;
            }

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

                    this.showToast('Venda finalizada com sucesso!', 'success');
                    return saleId;
                } else {
                    throw new Error(data.message || 'Erro ao finalizar venda');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro: ' + error.message, 'error');
                throw error;
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
                    this.showToast('Recibo impresso com sucesso!', 'success');
                    this.showModal = false;
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast('Erro ao imprimir: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Erro na impressão:', error);
                this.showToast('Erro na impressão: ' + error.message, 'error');
            }
        },

        async onlyConfirmSale() {
            try {
                await this.confirmSale();
                this.showToast('Venda confirmada!', 'success');
                this.showModal = false;
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Erro ao confirmar venda:', error);
                this.showToast('Erro ao confirmar venda: ' + error.message, 'error');
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
                    this.showToast('Venda confirmada e recibo impresso!', 'success');
                } else {
                    // Print failed, but sale is still confirmed
                    this.showToast('Venda confirmada! (Impressora indisponível)', 'info');
                }

                this.showModal = false;
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Erro:', error);
                // Even if printing fails, the sale should already be confirmed at this point
                this.showToast('Venda confirmada! (Erro na impressão)', 'info');
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
                    this.showToast('Venda carregada!', 'success');
                }
            } catch (error) {
                this.showToast('Erro ao carregar venda', 'error');
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
                    this.showToast('Pedido entregue!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                this.showToast('Erro ao atualizar', 'error');
            }
        },

        showToast(message, type) {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500'
            };
            
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
