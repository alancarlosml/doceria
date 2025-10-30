@extends('layouts.admin')

@section('title', 'Nova Encomenda - Doce Doce Brigaderia')

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
                        <h1 class="text-2xl font-bold text-gray-900">📝 Nova Encomenda</h1>
                        <p class="mt-1 text-sm text-gray-600">Cadastre uma nova encomenda personalizada</p>
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

            <form action="{{ route('encomendas.store') }}" method="POST" id="encomenda-form">
                @csrf

                <div class="bg-white shadow rounded-lg p-6">
                    <!-- Informações Gerais -->
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">📋 Informações Gerais</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Título -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título da Encomenda*</label>
                                <input type="text" name="title" id="title" required
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
                                            <option value="{{ $customer->id }}">
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
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('delivery_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Horário de Entrega -->
                            <div>
                                <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-1">Horário de Entrega</label>
                                <input type="time" name="delivery_time" id="delivery_time"
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
                                          placeholder="Rua, número, bairro, cidade..."></textarea>
                                @error('delivery_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes da Encomenda -->
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">🧾 Detalhes da Encomenda</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição Detalhada</label>
                                <textarea name="description" id="description" rows="4"
                                          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                          placeholder="Descreva em detalhes o que será encomendado (doces, bolo, quantidades, sabores, etc.)"></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subtotal -->
                            <div>
                                <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-1">Valor do Produto (R$)*</label>
                                <input type="number" name="subtotal" id="subtotal" step="0.01" min="0" required
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="0,00">
                                @error('subtotal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Taxa de Entrega -->
                            <div>
                                <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Taxa de Entrega (R$)</label>
                                <input type="number" name="delivery_fee" id="delivery_fee" step="0.01" min="0"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="0,00">
                                @error('delivery_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Custos Extras -->
                            <div>
                                <label for="custom_costs" class="block text-sm font-medium text-gray-700 mb-1">Custos Extras (R$)</label>
                                <input type="number" name="custom_costs" id="custom_costs" step="0.01" min="0"
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
                                <input type="number" name="discount" id="discount" step="0.01" min="0"
                                       class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                       placeholder="0,00">
                                @error('discount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-medium text-gray-900">TOTAL ESTIMADO:</span>
                                <span id="total-display" class="text-2xl font-bold text-green-600">R$ 0,00</span>
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
                                      placeholder="Instruções especiais, alergias, preferências de cor, etc."></textarea>
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
                            📝 Criar Encomenda
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
    // Função para cálculo automático do total
    function calcularTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal')?.value || 0);
        const deliveryFee = parseFloat(document.getElementById('delivery_fee')?.value || 0);
        const customCosts = parseFloat(document.getElementById('custom_costs')?.value || 0);
        const discount = parseFloat(document.getElementById('discount')?.value || 0);

        const total = subtotal + deliveryFee + customCosts - discount;

        const totalDisplay = document.getElementById('total-display');
        if (totalDisplay) {
            totalDisplay.textContent = 'R$ ' + total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // Adicionar eventos de mudança para os campos de valor
    ['subtotal', 'delivery_fee', 'custom_costs', 'discount'].forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.addEventListener('input', calcularTotal);
            element.addEventListener('change', calcularTotal);
        }
    });

    // Validação da data de entrega
    const form = document.getElementById('encomenda-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const deliveryDate = document.getElementById('delivery_date');
            if (deliveryDate && deliveryDate.value) {
                const selectedDate = new Date(deliveryDate.value + 'T00:00:00');
                const today = new Date();
                today.setDate(today.getDate() + 1); // Pelo menos amanhã
                today.setHours(0, 0, 0, 0);

                if (selectedDate < today) {
                    e.preventDefault();
                    alert('A data de entrega deve ser pelo menos amanhã!');
                    deliveryDate.focus();
                    return false;
                }
            }
        });
    }

    // Calcular total inicial
    calcularTotal();
});
</script>
@endpush
@endsection
