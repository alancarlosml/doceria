@extends('layouts.admin')

@section('title', $isEditing ? 'Editar Registro - Doceria Delícia' : 'Novo Registro Financeiro - Doceria Delícia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $isEditing ? '✏️ Editar Registro' : '➕ Novo Registro Financeiro' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isEditing ? 'Atualize as informações do registro financeiro' : 'Registre entradas ou saídas da doceria' }}
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="{{ $isEditing ? route('expenses.update', $expense) : route('expenses.store') }}">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipo de Movimento</label>
                                <select id="type" name="type" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                    <option value="">Selecione um tipo</option>
                                    <option value="entrada" {{ old('type', $isEditing ? $expense->type : '') === 'entrada' ? 'selected' : '' }}>
                                        💲 Entrada (Receita)
                                    </option>
                                    <option value="saida" {{ old('type', $isEditing ? $expense->type : '') === 'saida' ? 'selected' : '' }}>
                                        💸 Saída (Despesa)
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Selecione se é entrada ou saída de dinheiro</p>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date -->
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Data</label>
                                <input type="date" id="date" name="date" value="{{ old('date', $isEditing ? $expense->date->format('Y-m-d') : date('Y-m-d')) }}" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
                                <input type="text" inputmode="decimal" id="amount" name="amount" value="{{ old('amount', $isEditing ? number_format($expense->amount, 2, ',', '.') : '') }}" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2" placeholder="0,00">
                                <p class="mt-1 text-xs text-gray-500">Digite o valor e ele será formatado automaticamente</p>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                                <select id="payment_method" name="payment_method" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                    <option value="">Selecione um método</option>
                                    <option value="dinheiro" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'dinheiro' ? 'selected' : '' }}>💵 Dinheiro</option>
                                    <option value="cartao_credito" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'cartao_credito' ? 'selected' : '' }}>💳 Cartão Crédito</option>
                                    <option value="cartao_debito" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'cartao_debito' ? 'selected' : '' }}>💳 Cartão Débito</option>
                                    <option value="pix" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'pix' ? 'selected' : '' }}>📱 PIX</option>
                                    <option value="transferencia" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'transferencia' ? 'selected' : '' }}>🏦 Transferência</option>
                                    <option value="boleto" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'boleto' ? 'selected' : '' }}>📄 Boleto</option>
                                    <option value="outro" {{ old('payment_method', $isEditing ? $expense->payment_method : '') === 'outro' ? 'selected' : '' }}>❓ Outro</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Como o valor foi recebido/pago</p>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                                <input type="text" id="description" name="description" value="{{ old('description', $isEditing ? $expense->description : '') }}" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2" placeholder="Ex: Venda de brownies, Salário funcionário...">
                                <p class="mt-1 text-xs text-gray-500">Descrição detalhada do movimento financeiro</p>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="sm:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Observações (opcional)</label>
                                <textarea id="notes" name="notes" rows="3" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2" placeholder="Detalhes adicionais...">{{ old('notes', $isEditing ? $expense->notes : '') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Informações extras sobre este movimento</p>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview Info -->
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">💡 Preview do Registro:</h4>
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span>
                                        Tipo: <strong class="text-blue-600">
                                            <span id="preview-type">{{ $isEditing ? ($expense->type === 'entrada' ? 'Entrada' : 'Saída') : 'Selecione...' }}</span>
                                        </strong>
                                    </span>
                                    <span>•</span>
                                    <span>
                                        Valor: <strong class="text-green-600">
                                            <span id="preview-amount">R$ 0,00</span>
                                        </strong>
                                    </span>
                                    <span>•</span>
                                    <span>
                                        Data: <strong class="text-purple-600">
                                            <span id="preview-date">{{ $isEditing ? $expense->date->format('d/m/Y') : date('d/m/Y') }}</span>
                                        </strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Script for Live Preview and Currency Mask -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Função para aplicar máscara monetária brasileira
                                function aplicarMascaraMonetaria(input) {
                                    let value = input.value;
                                    
                                    // Se já está formatado (tem vírgula), não reformata
                                    if (value.includes(',') && value.match(/^\d{1,3}(\.\d{3})*,\d{2}$/)) {
                                        return removerMascaraMonetaria(value);
                                    }
                                    
                                    // Remove tudo que não é número
                                    value = value.replace(/\D/g, '');
                                    
                                    // Converte para número e divide por 100 para ter centavos
                                    if (value === '') {
                                        input.value = '';
                                        return 0;
                                    }
                                    
                                    const number = parseFloat(value) / 100;
                                    
                                    // Formata como moeda brasileira
                                    input.value = number.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    
                                    return number;
                                }

                                // Função para remover máscara e retornar valor numérico
                                function removerMascaraMonetaria(value) {
                                    if (!value) return 0;
                                    // Remove pontos e substitui vírgula por ponto
                                    const cleanValue = value.replace(/\./g, '').replace(',', '.');
                                    return parseFloat(cleanValue) || 0;
                                }

                                const typeSelect = document.getElementById('type');
                                const amountInput = document.getElementById('amount');
                                const dateInput = document.getElementById('date');
                                const previewType = document.getElementById('preview-type');
                                const previewAmount = document.getElementById('preview-amount');
                                const previewDate = document.getElementById('preview-date');

                                // Aplicar máscara monetária no campo amount
                                if (amountInput) {
                                    // Aplicar máscara ao digitar
                                    amountInput.addEventListener('input', function() {
                                        aplicarMascaraMonetaria(this);
                                        updatePreview();
                                    });

                                    // Aplicar máscara ao perder o foco
                                    amountInput.addEventListener('blur', function() {
                                        aplicarMascaraMonetaria(this);
                                    });

                                    // Aplicar máscara no valor inicial se existir
                                    if (amountInput.value) {
                                        aplicarMascaraMonetaria(amountInput);
                                    }
                                }

                                function updatePreview() {
                                    // Update type
                                    const typeValue = typeSelect.value;
                                    previewType.textContent = typeValue === 'entrada' ? 'Entrada' : typeValue === 'saida' ? 'Saída' : 'Selecione...';

                                    // Update amount
                                    const amountValue = removerMascaraMonetaria(amountInput.value || 0);
                                    previewAmount.textContent = 'R$ ' + amountValue.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });

                                    // Update date
                                    if (dateInput.value) {
                                        const dateObj = new Date(dateInput.value);
                                        previewDate.textContent = dateObj.toLocaleDateString('pt-BR');
                                    }
                                }

                                typeSelect.addEventListener('change', updatePreview);
                                dateInput.addEventListener('change', updatePreview);

                                // Converter valor monetário antes de enviar o formulário
                                const form = document.querySelector('form[method="POST"]');
                                if (form) {
                                    form.addEventListener('submit', function(e) {
                                        if (amountInput && amountInput.value) {
                                            const numericValue = removerMascaraMonetaria(amountInput.value);
                                            amountInput.value = numericValue.toFixed(2);
                                        }
                                    });
                                }

                                // Initial update
                                updatePreview();
                            });
                        </script>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEditing ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M5 13l4 4L19 7' }}"></path>
                            </svg>
                            {{ $isEditing ? 'Atualizar Registro' : 'Salvar Registro' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
