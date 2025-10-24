@extends('layouts.admin')

@section('title', 'PDV - Ponto de Venda - Doce Doce Brigaderia')

@section('admin-content')
<main class="flex-1 relative overflow-hidden bg-gray-50" x-data="posSystem()">
    <!-- Container Principal com 3 Colunas -->
    <div class="h-screen flex">
        
        <!-- COLUNA 1: Produtos e Categorias (40%) -->
        <div class="w-2/5 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header com Busca -->
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
                
                <!-- Busca de Produtos -->
                <input 
                    type="text" 
                    x-model="searchQuery"
                    @input="filterProducts()"
                    placeholder="🔍 Buscar produtos..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
            </div>

            <!-- Abas de Categorias -->
            <div class="flex overflow-x-auto border-b border-gray-200 bg-gray-50 px-2 py-2 gap-2">
                <button
                    @click="selectedCategory = null"
                    :class="selectedCategory === null ? 'bg-pink-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors"
                >
                    🍽️ Todos
                </button>
                @foreach($categories as $category)
                <button
                    @click="selectedCategory = {{ $category->id }}"
                    :class="selectedCategory === {{ $category->id }} ? 'bg-pink-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2"
                >
                    <span>{{ $category->emoji ?? '📦' }}</span>
                    <span>{{ $category->name }}</span>
                    <span class="text-xs opacity-75">({{ $category->products_count }})</span>
                </button>
                @endforeach
            </div>

            <!-- Grid de Produtos -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 gap-3">
                    @foreach($products as $product)
                    <button
                        @click="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->category->name ?? 'Sem categoria' }}')"
                        x-show="(selectedCategory === null || selectedCategory === {{ $product->category_id }}) && productMatchesSearch({{ $product->id }}, '{{ addslashes($product->name) }}')"
                        class="bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-pink-500 hover:shadow-lg transition-all duration-200 text-left group"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                    >
                        <!-- Imagem/Emoji -->
                        <div class="h-24 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover rounded-lg">
                            @else
                                <span class="text-4xl">{{ $product->category->emoji ?? '🍰' }}</span>
                            @endif
                        </div>
                        
                        <!-- Info do Produto -->
                        <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2">{{ $product->name }}</h4>
                        <p class="text-lg font-bold text-pink-600">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        
                        <!-- Badge Categoria -->
                        <div class="mt-2">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                {{ $product->category->name ?? 'Sem categoria' }}
                            </span>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- COLUNA 2: Carrinho/Pedido Atual (35%) -->
        <div class="w-2/5 bg-white flex flex-col">
            <!-- Header do Carrinho -->
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="text-2xl">🧾</span>
                        <span>Pedido Atual</span>
                    </h3>
                    <button 
                        @click="clearCart()"
                        x-show="cart.items.length > 0"
                        class="text-red-600 hover:text-red-800 font-medium text-sm"
                    >
                        🗑️ Limpar
                    </button>
                </div>

                <!-- Tipo de Venda -->
                <div class="flex gap-2">
                    <button
                        @click="cart.type = 'balcao'; cart.table_id = null; cart.delivery_fee = 0; cart.motoboy_id = null; calculateTotals();"
                        :class="cart.type === 'balcao' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm transition-colors"
                    >
                        🏪 Balcão
                    </button>
                    <button
                        @click="cart.type = 'delivery'; cart.table_id = null; if(cart.delivery_fee === 0) cart.delivery_fee = 5.00; calculateTotals();"
                        :class="cart.type === 'delivery' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm transition-colors"
                    >
                        🏍️ Delivery
                    </button>
                    <button
                        @click="cart.type = 'encomenda'; cart.table_id = null; cart.delivery_fee = 0; cart.motoboy_id = null; calculateTotals();"
                        :class="cart.type === 'encomenda' ? 'bg-purple-500 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm transition-colors"
                    >
                        📦 Encomenda
                    </button>
                </div>
            </div>

            <!-- Informações Adicionais Baseadas no Tipo -->
            <div class="p-4 border-b border-gray-200 bg-gray-50" x-show="cart.type !== 'balcao'">
                <!-- Cliente -->
                <div class="mb-3">
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

                <!-- Delivery - Endereço -->
                <template x-if="cart.type === 'delivery'">
                    <div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Endereço de Entrega <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                x-model="cart.delivery_address"
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="Rua, número, complemento..."
                            ></textarea>
                        </div>

                        <!-- Taxa de Entrega -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Taxa de Entrega (R$)</label>
                            <input 
                                type="number" 
                                x-model.number="cart.delivery_fee"
                                @input="calculateTotals()"
                                step="0.50"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="Ex: 5.00"
                            >
                            <p class="text-xs text-gray-500 mt-1">Padrão: R$ 5,00 - Digite 0 para frete grátis</p>
                        </div>

                        <!-- Motoboy -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Motoboy <span class="text-red-500">*</span>
                            </label>
                            <select 
                                x-model="cart.motoboy_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Selecione um motoboy</option>
                                @foreach($motoboys as $motoboy)
                                <option value="{{ $motoboy->id }}">{{ $motoboy->name }} - {{ $motoboy->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </template>

                <!-- Encomenda - Data e Hora -->
                <template x-if="cart.type === 'encomenda'">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input 
                                type="date" 
                                x-model="cart.delivery_date"
                                :min="new Date().toISOString().split('T')[0]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                            <input 
                                type="time" 
                                x-model="cart.delivery_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                            >
                        </div>
                    </div>
                </template>
            </div>

            <!-- Lista de Itens do Carrinho -->
            <div class="flex-1 overflow-y-auto p-4" style="max-height: calc(100vh - 520px);">
                <div x-show="cart.items.length === 0" class="text-center py-12 text-gray-400">
                    <div class="text-6xl mb-4">🛒</div>
                    <p class="text-lg">Carrinho vazio</p>
                    <p class="text-sm">Adicione produtos para iniciar o pedido</p>
                </div>

                <div class="space-y-2">
                    <template x-for="(item, index) in cart.items" :key="index">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="item.category"></p>
                                </div>
                                <button 
                                    @click="removeItem(index)"
                                    class="text-red-500 hover:text-red-700 ml-2"
                                >
                                    ❌
                                </button>
                            </div>
                            
                            <!-- Quantidade e Preço -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button 
                                        @click="updateQuantity(index, -1)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center font-bold"
                                    >
                                        −
                                    </button>
                                    <span class="w-12 text-center font-bold" x-text="item.quantity"></span>
                                    <button 
                                        @click="updateQuantity(index, 1)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center font-bold"
                                    >
                                        +
                                    </button>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">R$ <span x-text="item.price.toFixed(2).replace('.', ',')"></span> cada</p>
                                    <p class="font-bold text-green-600">R$ <span x-text="item.subtotal.toFixed(2).replace('.', ',')"></span></p>
                                </div>
                            </div>

                            <!-- Observações do Item -->
                            <div class="mt-2">
                                <input 
                                    type="text" 
                                    x-model="item.notes"
                                    placeholder="Observações..."
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                >
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Resumo e Totais - SEMPRE VISÍVEL -->
            <div class="border-t border-gray-200 p-4 bg-gray-50" style="min-height: 280px;">
                <!-- Desconto -->
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Desconto (R$)</label>
                    <input 
                        type="number" 
                        x-model.number="cart.discount"
                        @input="calculateTotals()"
                        step="0.01"
                        min="0"
                        class="w-32 px-3 py-1 border border-gray-300 rounded-lg text-sm text-right"
                    >
                </div>

                <!-- Subtotal -->
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">R$ <span x-text="cart.subtotal.toFixed(2).replace('.', ',')"></span></span>
                </div>

                <!-- Taxa de Entrega (só para delivery) -->
                <div x-show="cart.type === 'delivery'" class="flex items-center justify-between text-sm mb-2">
                    <span class="text-gray-600">Taxa de Entrega:</span>
                    <span class="font-medium text-blue-600">R$ <span x-text="cart.delivery_fee.toFixed(2).replace('.', ',')"></span></span>
                </div>

                <!-- Desconto Aplicado -->
                <div x-show="cart.discount > 0" class="flex items-center justify-between text-sm mb-2">
                    <span class="text-gray-600">Desconto:</span>
                    <span class="font-medium text-red-600">- R$ <span x-text="cart.discount.toFixed(2).replace('.', ',')"></span></span>
                </div>

                <!-- Total -->
                <div class="flex items-center justify-between pt-2 border-t border-gray-300 mb-3">
                    <span class="text-lg font-bold text-gray-800">TOTAL:</span>
                    <span class="text-2xl font-bold text-green-600">R$ <span x-text="cart.total.toFixed(2).replace('.', ',')"></span></span>
                </div>

                <!-- Observações Gerais -->
                <div class="mb-3">
                    <textarea 
                        x-model="cart.notes"
                        rows="2"
                        placeholder="Observações gerais do pedido..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    ></textarea>
                </div>

                <!-- Botões de Ação - SEMPRE VISÍVEIS -->
                <div class="grid grid-cols-2 gap-3">
                    <button
                        @click="savePending()"
                        :disabled="cart.items.length === 0"
                        :class="cart.items.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-yellow-600'"
                        class="bg-yellow-500 text-white py-3 rounded-lg font-bold transition-colors flex items-center justify-center"
                    >
                        <span class="mr-1">⏸️</span>
                        <span>Pendente</span>
                    </button>
                    <button
                        @click="finalizeSale()"
                        :disabled="cart.items.length === 0"
                        :class="cart.items.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700 shadow-lg'"
                        class="bg-green-600 text-white py-3 rounded-lg font-bold transition-colors flex items-center justify-center"
                    >
                        <span class="mr-1">✅</span>
                        <span>Finalizar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- COLUNA 3: Mesas e Pedidos Pendentes (25%) -->
        <div class="w-1/5 bg-gray-50 border-l border-gray-200 flex flex-col">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200">
                <button
                    @click="rightPanelTab = 'mesas'"
                    :class="rightPanelTab === 'mesas' ? 'bg-white text-pink-600 border-b-2 border-pink-600' : 'bg-gray-100 text-gray-600'"
                    class="flex-1 py-3 font-medium text-sm"
                >
                    🪑 Mesas
                </button>
                <button
                    @click="rightPanelTab = 'pedidos'"
                    :class="rightPanelTab === 'pedidos' ? 'bg-white text-pink-600 border-b-2 border-pink-600' : 'bg-gray-100 text-gray-600'"
                    class="flex-1 py-3 font-medium text-sm"
                >
                    📋 Pedidos
                </button>
            </div>

            <!-- Conteúdo das Mesas -->
            <div x-show="rightPanelTab === 'mesas'" class="flex-1 overflow-y-auto p-4">
                <!-- Mesas Disponíveis -->
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        Disponíveis ({{ $tables->count() }})
                    </h4>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($tables as $table)
                        <button
                            @click="selectTable({{ $table->id }}, '{{ $table->number }}')"
                            :class="cart.table_id === {{ $table->id }} ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:border-green-500'"
                            class="border-2 rounded-lg p-3 transition-all"
                        >
                            <div class="text-2xl mb-1">🪑</div>
                            <div class="font-bold text-sm">Mesa {{ $table->number }}</div>
                            <div class="text-xs opacity-75">{{ $table->capacity }} pessoas</div>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Mesas Ocupadas -->
                @if($occupiedTables->count() > 0)
                <div>
                    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        Ocupadas ({{ $occupiedTables->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($occupiedTables as $table)
                        @php
                            $sale = $table->sales->first();
                        @endphp
                        <button
                            @click="loadSale({{ $sale->id }})"
                            class="w-full bg-red-50 border-2 border-red-200 rounded-lg p-3 text-left hover:border-red-400 transition-all"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm">🪑 Mesa {{ $table->number }}</span>
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </div>
                            @if($sale->customer)
                            <div class="text-xs text-gray-600 mb-1">
                                👤 {{ $sale->customer->name }}
                            </div>
                            @endif
                            <div class="text-xs text-gray-600">
                                🧾 {{ $sale->items->count() }} itens - R$ {{ number_format($sale->total, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                ⏰ {{ $sale->created_at->diffForHumans() }}
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Conteúdo dos Pedidos Pendentes -->
            <div x-show="rightPanelTab === 'pedidos'" class="flex-1 overflow-y-auto p-4">
                @if($pendingSales->count() > 0)
                <div class="space-y-2">
                    @foreach($pendingSales as $sale)
                    <button
                        @click="loadSale({{ $sale->id }})"
                        class="w-full bg-white border-2 border-gray-200 rounded-lg p-3 text-left hover:border-pink-400 transition-all"
                    >
                        <!-- Header do Pedido -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm">
                                @if($sale->type === 'balcao')
                                    🏪 Balcão #{{ $sale->id }}
                                @elseif($sale->type === 'delivery')
                                    🏍️ Delivery #{{ $sale->id }}
                                @else
                                    📦 Encomenda #{{ $sale->id }}
                                @endif
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $sale->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $sale->status === 'em_preparo' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $sale->status === 'pronto' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $sale->status === 'saiu_entrega' ? 'bg-purple-100 text-purple-800' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $sale->status)) }}
                            </span>
                        </div>

                        <!-- Info do Cliente -->
                        @if($sale->customer)
                        <div class="text-xs text-gray-600 mb-1">
                            👤 {{ $sale->customer->name }}
                        </div>
                        @endif

                        <!-- Mesa (se aplicável) -->
                        @if($sale->table)
                        <div class="text-xs text-gray-600 mb-1">
                            🪑 Mesa {{ $sale->table->number }}
                        </div>
                        @endif

                        <!-- Resumo -->
                        <div class="text-xs text-gray-600 mb-2">
                            🧾 {{ $sale->items->count() }} itens - <span class="font-bold text-green-600">R$ {{ number_format($sale->total, 2, ',', '.') }}</span>
                        </div>

                        <!-- Produtos -->
                        <div class="text-xs text-gray-500 space-y-1">
                            @foreach($sale->items->take(3) as $item)
                            <div>• {{ $item->quantity }}x {{ $item->product->name }}</div>
                            @endforeach
                            @if($sale->items->count() > 3)
                            <div class="text-gray-400">+ {{ $sale->items->count() - 3 }} itens...</div>
                            @endif
                        </div>

                        <!-- Hora -->
                        <div class="text-xs text-gray-400 mt-2">
                            ⏰ {{ $sale->created_at->format('H:i') }}
                        </div>
                    </button>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 text-gray-400">
                    <div class="text-5xl mb-3">📋</div>
                    <p class="text-sm">Nenhum pedido pendente</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Finalização -->
    <div x-show="showFinalizationModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showFinalizationModal = false"
    >
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl" @click.stop>
            <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>💳</span>
                <span>Finalizar Venda</span>
            </h3>

            <!-- Resumo da Venda -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-bold">R$ <span x-text="cart.subtotal.toFixed(2).replace('.', ',')"></span></span>
                </div>
                <div x-show="cart.type === 'delivery'" class="flex justify-between mb-2">
                    <span class="text-gray-600">Taxa de Entrega:</span>
                    <span class="font-bold text-blue-600">R$ <span x-text="cart.delivery_fee.toFixed(2).replace('.', ',')"></span></span>
                </div>
                <div x-show="cart.discount > 0" class="flex justify-between mb-2">
                    <span class="text-gray-600">Desconto:</span>
                    <span class="font-bold text-red-600">- R$ <span x-text="cart.discount.toFixed(2).replace('.', ',')"></span></span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-300">
                    <span class="text-lg font-bold">TOTAL:</span>
                    <span class="text-2xl font-bold text-green-600">R$ <span x-text="cart.total.toFixed(2).replace('.', ',')"></span></span>
                </div>
            </div>

            <!-- Método de Pagamento -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-3">Método de Pagamento:</label>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        @click="cart.payment_method = 'dinheiro'"
                        :class="cart.payment_method === 'dinheiro' ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300'"
                        class="border-2 rounded-lg p-3 font-medium transition-all"
                    >
                        💵 Dinheiro
                    </button>
                    <button
                        @click="cart.payment_method = 'pix'"
                        :class="cart.payment_method === 'pix' ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300'"
                        class="border-2 rounded-lg p-3 font-medium transition-all"
                    >
                        📱 PIX
                    </button>
                    <button
                        @click="cart.payment_method = 'cartao_debito'"
                        :class="cart.payment_method === 'cartao_debito' ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300'"
                        class="border-2 rounded-lg p-3 font-medium transition-all"
                    >
                        💳 Débito
                    </button>
                    <button
                        @click="cart.payment_method = 'cartao_credito'"
                        :class="cart.payment_method === 'cartao_credito' ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300'"
                        class="border-2 rounded-lg p-3 font-medium transition-all"
                    >
                        💳 Crédito
                    </button>
                </div>
            </div>

            <!-- Botões -->
            <div class="grid grid-cols-2 gap-3">
                <button
                    @click="showFinalizationModal = false"
                    class="bg-gray-200 text-gray-700 py-3 rounded-lg font-bold hover:bg-gray-300 transition-colors"
                >
                    Cancelar
                </button>
                <button
                    @click="confirmFinalization()"
                    :disabled="!cart.payment_method"
                    :class="!cart.payment_method ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                    class="bg-green-600 text-white py-3 rounded-lg font-bold transition-colors"
                >
                    ✅ Confirmar
                </button>
            </div>
        </div>
    </div>
</main>

<script>
function posSystem() {
    return {
        // Estado
        searchQuery: '',
        selectedCategory: null,
        rightPanelTab: 'mesas',
        showFinalizationModal: false,
        
        // Carrinho
        cart: {
            type: 'balcao',
            customer_id: null,
            table_id: null,
            motoboy_id: null,
            payment_method: null,
            delivery_date: null,
            delivery_time: null,
            delivery_address: '',
            notes: '',
            items: [],
            subtotal: 0,
            discount: 0,
            delivery_fee: 0,
            total: 0
        },

        init() {
            console.log('POS System iniciado');
            this.calculateTotals();
        },

        // Adicionar item ao carrinho
        addItem(productId, name, price, category) {
            const existingItem = this.cart.items.find(item => item.product_id === productId);
            
            if (existingItem) {
                existingItem.quantity++;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                this.cart.items.push({
                    product_id: productId,
                    name: name,
                    price: price,
                    category: category,
                    quantity: 1,
                    subtotal: price,
                    notes: ''
                });
            }
            
            this.calculateTotals();
            this.showToast(`${name} adicionado ao carrinho!`, 'success');
        },

        // Remover item
        removeItem(index) {
            this.cart.items.splice(index, 1);
            this.calculateTotals();
        },

        // Atualizar quantidade
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

        // Calcular totais
        calculateTotals() {
            this.cart.subtotal = this.cart.items.reduce((sum, item) => sum + item.subtotal, 0);
            this.cart.delivery_fee = this.cart.type === 'delivery' ? 5.00 : 0;
            this.cart.total = this.cart.subtotal + this.cart.delivery_fee - this.cart.discount;
        },

        // Filtrar produtos
        filterProducts() {
            // A filtragem é feita via x-show no template
        },

        productMatchesSearch(productId, productName) {
            if (!this.searchQuery) return true;
            return productName.toLowerCase().includes(this.searchQuery.toLowerCase());
        },

        // Selecionar mesa
        selectTable(tableId, tableNumber) {
            this.cart.type = 'balcao';
            this.cart.table_id = tableId;
            this.showToast(`Mesa ${tableNumber} selecionada!`, 'success');
        },

        // Limpar carrinho
        clearCart() {
            if (confirm('Deseja realmente limpar o carrinho?')) {
                this.cart = {
                    type: 'balcao',
                    customer_id: null,
                    table_id: null,
                    motoboy_id: null,
                    payment_method: null,
                    delivery_date: null,
                    delivery_time: null,
                    delivery_address: '',
                    notes: '',
                    items: [],
                    subtotal: 0,
                    discount: 0,
                    delivery_fee: 0,
                    total: 0
                };
                this.showToast('Carrinho limpo!', 'info');
            }
        },

        // Salvar como pendente
        async savePending() {
            if (this.cart.items.length === 0) {
                this.showToast('Adicione itens ao carrinho!', 'error');
                return;
            }

            try {
                const response = await fetch('/sales', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.cart)
                });

                const data = await response.json();

                if (data.success) {
                    this.showToast('Pedido salvo como pendente!', 'success');
                    this.clearCart();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Erro ao salvar pedido');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao salvar pedido: ' + (error.message || 'Erro desconhecido'), 'error');
            }
        },

        // Finalizar venda
        finalizeSale() {
            if (this.cart.items.length === 0) {
                this.showToast('Adicione itens ao carrinho!', 'error');
                return;
            }

            this.showFinalizationModal = true;
        },

        // Confirmar finalização
        async confirmFinalization() {
            if (!this.cart.payment_method) {
                this.showToast('Selecione um método de pagamento!', 'error');
                return;
            }

            try {
                const response = await fetch('/sales', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ...this.cart,
                        status: 'finalizado'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showToast('Venda finalizada com sucesso!', 'success');
                    this.showFinalizationModal = false;
                    this.clearCart();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Erro desconhecido ao finalizar venda');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao finalizar venda: ' + (error.message || 'Erro desconhecido'), 'error');
            }
        },

        // Carregar venda existente
        async loadSale(saleId) {
            console.log('Carregando venda:', saleId);
            this.showToast('Carregando venda #' + saleId + '...', 'info');

            try {
                const response = await fetch(`/sales/${saleId}/pos-data`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.sale) {
                    const saleData = data.sale;

                    // Limpar carrinho atual
                    this.clearCart();

                    // Popular cart com dados da venda
                    this.cart = {
                        type: saleData.type,
                        customer_id: saleData.customer_id,
                        table_id: saleData.table_id,
                        motoboy_id: saleData.motoboy_id,
                        payment_method: saleData.payment_method,
                        delivery_date: saleData.delivery_date,
                        delivery_time: saleData.delivery_time,
                        delivery_address: saleData.delivery_address,
                        delivery_fee: parseFloat(saleData.delivery_fee || 0),
                        discount: parseFloat(saleData.discount || 0),
                        notes: saleData.notes || '',
                        items: saleData.items || [],
                        subtotal: parseFloat(saleData.subtotal || 0),
                        total: parseFloat(saleData.total || 0)
                    };

                    // Recalcular totais para garantir consistência
                    this.calculateTotals();
                    this.calculateTotals(); // Chamada dupla para garantir delivery_fee correta

                    console.log('Venda carregada com sucesso:', this.cart);
                    this.showToast('✅ Venda carregada!', 'success');
                } else {
                    throw new Error(data.message || 'Erro ao carregar venda');
                }
            } catch (error) {
                console.error('Erro ao carregar venda:', error);
                this.showToast('❌ Erro ao carregar venda: ' + (error.message || 'Erro desconhecido'), 'error');
            }
        },

        // Toast notification
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

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
