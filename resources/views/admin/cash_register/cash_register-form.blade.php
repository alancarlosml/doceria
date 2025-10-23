@extends('layouts.admin')

@section('title', $isEditing ? 'Editar Caixa - ' . $cashRegister->id : 'Abrir Novo Caixa - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">{{ $isEditing ? '✏️' : '💰' }}</span>{{ $isEditing ? 'Editar Caixa Aberto' : 'Abrir Novo Caixa' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isEditing ? 'Ajuste as informações do caixa aberto' : 'Configure o saldo inicial e abra um novo caixa para vendas' }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('cash-registers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Warning Alert for Open Register -->
            @if(!$isEditing)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Atenção!</strong> Este caixa permitirá registrar vendas e controlar movimentações financeiras da doceria.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form method="POST" action="{{ $isEditing ? route('cash-registers.update', $cashRegister) : route('cash-registers.store') }}">
                    @if($isEditing)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Opening Balance Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Configuração do Caixa</h3>

                                <!-- Opening Balance -->
                                <div>
                                    <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-1">
                                        Saldo Inicial (R$) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">R$</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="opening_balance"
                                            name="opening_balance"
                                            value="{{ $isEditing ? old('opening_balance', $cashRegister->opening_balance) : old('opening_balance', 0) }}"
                                            step="0.01"
                                            min="0"
                                            class="pl-12 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('opening_balance') border-red-300 @enderror"
                                            placeholder="0,00"
                                            required
                                        >
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Valor em dinheiro que está no caixa no momento da abertura</p>
                                    @error('opening_balance')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Observações</h3>

                                <!-- Opening Notes -->
                                <div>
                                    <label for="opening_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Observações de Abertura
                                    </label>
                                    <textarea
                                        id="opening_notes"
                                        name="opening_notes"
                                        rows="4"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('opening_notes') border-red-300 @enderror"
                                        placeholder="Ex: Caixa aberto com dinheiro contado e verificado..."
                                    >{{ $isEditing ? old('opening_notes', $cashRegister->opening_notes) : old('opening_notes') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Observações sobre a abertura do caixa (opcional)</p>
                                    @error('opening_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('cash-registers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $isEditing ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($isEditing)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @endif
                                </svg>
                                {{ $isEditing ? 'Salvar Alterações' : 'Abrir Caixa' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Additional Information -->
            @if($isEditing)
                <!-- Close Register Section -->
                <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 text-red-700 flex items-center">
                            <span class="mr-3">🔒</span>Fechar Caixa
                        </h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Quando terminar o expediente, feche o caixa informando o saldo final encontrado.
                        </p>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        <strong>Importante!</strong> Feche o caixa apenas no final do expediente. Após o fechamento, novas vendas não poderão ser registradas.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <form method="POST" action="{{ route('cash-registers.close', $cashRegister) }}" onsubmit="return confirm('Tem certeza que deseja fechar este caixa?\n\nApós o fechamento, não será possível registrar novas vendas neste caixa.')">
                                @csrf

                                <div class="mb-4">
                                    <label for="closing_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                        Saldo Final (R$)
                                    </label>
                                    <div class="relative max-w-xs mx-auto">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">R$</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="closing_balance"
                                            name="closing_balance"
                                            step="0.01"
                                            min="0"
                                            class="pl-12 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                                            placeholder="0,00"
                                            required
                                        >
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Valor contado no momento do fechamento</p>
                                </div>

                                <div class="mb-4">
                                    <label for="closing_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Observações de Fechamento
                                    </label>
                                    <textarea
                                        id="closing_notes"
                                        name="closing_notes"
                                        rows="3"
                                        class="w-full max-w-lg mx-auto rounded-lg border border-gray-300 px-4 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                                        placeholder="Ex: Caixa fechado com dinheiro contado..."
                                    ></textarea>
                                </div>

                                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Fechar Caixa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- JavaScript for form enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format currency input
    const currencyInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    currencyInputs.forEach(function(input) {
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                const value = parseFloat(e.target.value);
                if (!isNaN(value)) {
                    e.target.value = value.toFixed(2);
                }
            }
        });

        input.addEventListener('input', function(e) {
            // Allow only numbers and decimal point
            let value = e.target.value.replace(/[^\d.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            e.target.value = value;
        });
    });
});
</script>
@endsection
