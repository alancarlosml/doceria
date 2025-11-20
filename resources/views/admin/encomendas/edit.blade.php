@extends('layouts.admin')

@section('title', 'Editar Encomenda - Doce Doce Brigaderia')

@push('styles')
<style>
    .customer-search {
        max-height: 200px;
        overflow-y: auto;
    }
    .customer-option {
        padding: 8px 12px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .customer-option:hover {
        background-color: #f9fafb;
    }
    .selected-customer {
        background-color: #e0f2fe;
        border-color: #0ea5e9;
    }
    .item-row {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        background-color: #f9fafb;
    }
</style>
@endpush

@section('admin-content')
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">✏️ Editar Encomenda #{{ $encomenda->code }}</h1>
                        <p class="mt-1 text-sm text-gray-600">Atualize as informações da encomenda</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('encomendas.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('encomendas.update', $encomenda) }}" method="POST" id="encomenda-form">
                @csrf
                @method('PUT')

                <div class="bg-white shadow rounded-lg p-6">
                    <!-- Informações Gerais -->
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">📋 Informações Gerais</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Título -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título da Encomenda*</label>
                                <input type="text" name="title" id="title" required
                                       value="{{ old('title', $encomenda->title) }}"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="Ex: Docinhos para casamento da Maria">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cliente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                                <div class="relative">
                                    <select name="customer_id" id="customer-select"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                        <option value="">-- Selecione um cliente (opcional) --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', $encomenda->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="mt-2">
                                        <a href="{{ route('customers.create') }}"
                                           target="_blank"
                                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                           ➕ Criar novo cliente
                                        </a>
                                    </div>
                                </div>

                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Data de Entrega -->
                            <div>
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Entrega*</label>
                                <input type="date" name="delivery_date" id="delivery_date" required
                                       value="{{ old('delivery_date', $encomenda->delivery_date->format('Y-m-d')) }}"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('delivery_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Horário de Entrega -->
                            <div>
                                <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-1">Horário de Entrega</label>
                                <input type="time" name="delivery_time" id="delivery_time"
                                       value="{{ old('delivery_time', $encomenda->delivery_time) }}"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('delivery_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Endereço de Entrega -->
                            <div>
                                <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Endereço de Entrega</label>
                                <textarea name="delivery_address" id="delivery_address" rows="3"
                                          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                          placeholder="Rua, número, bairro, cidade...">{{ old('delivery_address', $encomenda->delivery_address) }}</textarea>
                                @error('delivery_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes da Encomenda -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900">🧾 Itens da Encomenda</h2>
                            <button type="button" id="add-item-btn" class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                ➕ Adicionar Item
                            </button>
                        </div>

                        <!-- Container de Itens -->
                        <div id="items-container">
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                    <div class="item-row" data-item-index="{{ $index }}">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                            <div class="md:col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Produto*</label>
                                                <select name="items[{{ $index }}][product_id]" class="item-product-select w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                    <option value="">-- Produto personalizado --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ (isset($item['product_id']) && $item['product_id'] == $product->id) ? 'selected' : '' }}>
                                                            {{ $product->name }} - R$ {{ number_format($product->price, 2, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="md:col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto*</label>
                                                <input type="text" name="items[{{ $index }}][product_name]" required
                                                       value="{{ $item['product_name'] ?? '' }}"
                                                       class="item-product-name w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       placeholder="Nome do produto">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade*</label>
                                                <input type="number" name="items[{{ $index }}][quantity]" required min="1"
                                                       value="{{ $item['quantity'] ?? 1 }}"
                                                       class="item-quantity w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)*</label>
                                                <input type="text" name="items[{{ $index }}][unit_price]" required
                                                       value="{{ isset($item['unit_price']) ? number_format($item['unit_price'], 2, ',', '.') : '' }}"
                                                       class="item-unit-price w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       placeholder="0,00">
                                            </div>
                                            <div class="md:col-span-7">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações do Item</label>
                                                <input type="text" name="items[{{ $index }}][notes]"
                                                       value="{{ $item['notes'] ?? '' }}"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       placeholder="Observações específicas deste item">
                                            </div>
                                            <div class="md:col-span-2 flex items-end">
                                                <button type="button" class="remove-item-btn w-full px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                                    🗑️ Remover
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach($encomenda->items as $index => $item)
                                    <div class="item-row" data-item-index="{{ $index }}">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                            <div class="md:col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                                                <select name="items[{{ $index }}][product_id]" class="item-product-select w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                    <option value="">-- Produto personalizado --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }} - R$ {{ number_format($product->price, 2, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="md:col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto*</label>
                                                <input type="text" name="items[{{ $index }}][product_name]" required
                                                       value="{{ $item->product_name }}"
                                                       class="item-product-name w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       placeholder="Nome do produto">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade*</label>
                                                <input type="number" name="items[{{ $index }}][quantity]" required min="1"
                                                       value="{{ $item->quantity }}"
                                                       class="item-quantity w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)*</label>
                                                <input type="text" name="items[{{ $index }}][unit_price]" required
                                                       value="{{ number_format($item->unit_price, 2, ',', '.') }}"
                                                       class="item-unit-price w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       placeholder="0,00">
                                            </div>
                                            <div class="md:col-span-7">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações do Item</label>
                                                <input type="text" name="items[{{ $index }}][notes]"
                                                       value="{{ $item->notes }}"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       placeholder="Observações específicas deste item">
                                            </div>
                                            <div class="md:col-span-2 flex items-end">
                                                <button type="button" class="remove-item-btn w-full px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                                    🗑️ Remover
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Valores Adicionais -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Taxa de Entrega -->
                            <div>
                                <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Taxa de Entrega (R$)</label>
                                <input type="text" name="delivery_fee" id="delivery_fee" inputmode="decimal" min="0"
                                       value="{{ old('delivery_fee', number_format($encomenda->delivery_fee, 2, ',', '.')) }}"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="0,00">
                                @error('delivery_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Custos Extras -->
                            <div>
                                <label for="custom_costs" class="block text-sm font-medium text-gray-700 mb-1">Custos Extras (R$)</label>
                                <input type="text" name="custom_costs" id="custom_costs" inputmode="decimal" min="0"
                                       value="{{ old('custom_costs', number_format($encomenda->custom_costs, 2, ',', '.')) }}"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="0,00"
                                       title="Ex: custos de ingredientes especiais, embalagem personalizada, etc.">
                                @error('custom_costs')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Desconto -->
                            <div>
                                <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Desconto (R$)</label>
                                <input type="text" name="discount" id="discount" inputmode="decimal" min="0"
                                       value="{{ old('discount', number_format($encomenda->discount, 2, ',', '.')) }}"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="0,00">
                                @error('discount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição Detalhada</label>
                            <textarea name="description" id="description" rows="4"
                                      class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                      placeholder="Descreva em detalhes o que será encomendado (doces, bolo, quantidades, sabores, etc.)">{{ old('description', $encomenda->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-medium text-gray-900">TOTAL ESTIMADO:</span>
                                <span id="total-display" class="text-2xl font-bold text-green-600">R$ {{ number_format($encomenda->total, 2, ',', '.') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Valor calculado automaticamente</p>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">💬 Observações</h2>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações Adicionais</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                      placeholder="Instruções especiais, alergias, preferências de cor, etc.">{{ old('notes', $encomenda->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('encomendas.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            💾 Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ old('items') ? count(old('items')) : $encomenda->items->count() }};
    const products = @json($products->map(function($p) { return ['id' => $p->id, 'name' => $p->name, 'price' => $p->price]; }));

    // Função para aplicar máscara monetária brasileira
    function aplicarMascaraMonetaria(input) {
        let value = input.value;
        
        if (value.includes(',') && value.match(/^\d{1,3}(\.\d{3})*,\d{2}$/)) {
            return removerMascaraMonetaria(value);
        }
        
        value = value.replace(/\D/g, '');
        
        if (value === '') {
            input.value = '';
            return 0;
        }
        
        const number = parseFloat(value) / 100;
        
        input.value = number.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        return number;
    }

    function removerMascaraMonetaria(value) {
        if (!value) return 0;
        const cleanValue = value.replace(/\./g, '').replace(',', '.');
        return parseFloat(cleanValue) || 0;
    }

    // Adicionar novo item
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = document.createElement('div');
        newItem.className = 'item-row';
        newItem.setAttribute('data-item-index', itemIndex);
        
        newItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                    <select name="items[${itemIndex}][product_id]" class="item-product-select w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">-- Produto personalizado --</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} - R$ ${p.price.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</option>`).join('')}
                    </select>
                </div>
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto*</label>
                    <input type="text" name="items[${itemIndex}][product_name]" required
                           class="item-product-name w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                           placeholder="Nome do produto">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade*</label>
                    <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1"
                           class="item-quantity w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)*</label>
                    <input type="text" name="items[${itemIndex}][unit_price]" required
                           class="item-unit-price w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                           placeholder="0,00">
                </div>
                <div class="md:col-span-7">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações do Item</label>
                    <input type="text" name="items[${itemIndex}][notes]"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                           placeholder="Observações específicas deste item">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="button" class="remove-item-btn w-full px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                        🗑️ Remover
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(newItem);
        attachItemEvents(newItem);
        itemIndex++;
    });

    // Remover item
    function attachItemEvents(itemRow) {
        const removeBtn = itemRow.querySelector('.remove-item-btn');
        const productSelect = itemRow.querySelector('.item-product-select');
        const productNameInput = itemRow.querySelector('.item-product-name');
        const unitPriceInput = itemRow.querySelector('.item-unit-price');
        const quantityInput = itemRow.querySelector('.item-quantity');

        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('.item-row').length > 1) {
                itemRow.remove();
                calcularTotal();
            } else {
                alert('É necessário ter pelo menos um item na encomenda.');
            }
        });

        // Quando selecionar um produto, preencher nome e preço
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                productNameInput.value = selectedOption.text.split(' - ')[0];
                const price = parseFloat(selectedOption.dataset.price);
                unitPriceInput.value = price.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        });

        // Aplicar máscara monetária no preço unitário
        unitPriceInput.addEventListener('input', function() {
            aplicarMascaraMonetaria(this);
            calcularTotal();
        });

        unitPriceInput.addEventListener('blur', function() {
            aplicarMascaraMonetaria(this);
        });

        // Calcular total quando quantidade mudar
        quantityInput.addEventListener('input', function() {
            calcularTotal();
        });
    }

    // Anexar eventos aos itens existentes
    document.querySelectorAll('.item-row').forEach(itemRow => {
        attachItemEvents(itemRow);
    });

    // Aplicar máscara nos campos monetários existentes
    const camposMonetarios = ['delivery_fee', 'custom_costs', 'discount'];
    
    camposMonetarios.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.addEventListener('input', function() {
                aplicarMascaraMonetaria(this);
                calcularTotal();
            });
            element.addEventListener('blur', function() {
                aplicarMascaraMonetaria(this);
            });
        }
    });

    // Calcular total
    function calcularTotal() {
        let subtotal = 0;
        
        document.querySelectorAll('.item-row').forEach(itemRow => {
            const quantity = parseFloat(itemRow.querySelector('.item-quantity').value) || 0;
            const unitPrice = removerMascaraMonetaria(itemRow.querySelector('.item-unit-price').value);
            subtotal += quantity * unitPrice;
        });

        const deliveryFee = removerMascaraMonetaria(document.getElementById('delivery_fee')?.value || 0);
        const customCosts = removerMascaraMonetaria(document.getElementById('custom_costs')?.value || 0);
        const discount = removerMascaraMonetaria(document.getElementById('discount')?.value || 0);

        const total = subtotal + deliveryFee + customCosts - discount;

        const totalDisplay = document.getElementById('total-display');
        if (totalDisplay) {
            totalDisplay.textContent = 'R$ ' + total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // Converter valores monetários antes de enviar o formulário
    const form = document.getElementById('encomenda-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Converter campos monetários para formato numérico
            camposMonetarios.forEach(fieldId => {
                const element = document.getElementById(fieldId);
                if (element && element.value) {
                    const numericValue = removerMascaraMonetaria(element.value);
                    element.value = numericValue.toFixed(2);
                }
            });

            // Converter preços unitários dos itens
            document.querySelectorAll('.item-unit-price').forEach(input => {
                if (input.value) {
                    const numericValue = removerMascaraMonetaria(input.value);
                    input.value = numericValue.toFixed(2);
                }
            });
        });
    }

    // Calcular total inicial
    calcularTotal();
});
</script>
@endpush
@endsection

