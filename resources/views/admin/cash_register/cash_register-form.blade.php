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
                        <span class="mr-3">{{ $isClosing ? '🔒' : ($isEditing ? '✏️' : '🆕') }}</span>
                        {{ $isClosing ? 'Fechar Caixa' : ($isEditing ? 'Editar Caixa Aberto' : 'Abrir Novo Caixa') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($isClosing)
                            Conte o dinheiro em mãos e registre o fechamento do caixa atual
                        @elseif($isEditing)
                            Ajuste as informações do caixa aberto se necessário
                        @else
                            Configure o saldo inicial e abra um novo caixa para vendas
                        @endif
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
            @if(!$isEditing && !$isClosing)
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
            
            @if(!$isClosing)
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
            @endif

            <!-- Additional Information -->
            @if($isClosing)
                <!-- Pending Orders Warning/Alerts -->
                @if(isset($pendingOrders) && $pendingOrders->count() > 0)
                <div class="mt-8 bg-yellow-50 shadow-lg rounded-lg overflow-hidden border-l-4 border-yellow-400">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-yellow-800 mb-4 flex items-center">
                            <span class="mr-3">⚠️</span>{{ $pendingOrders->count() }} Pedido(s) Pendente(s) Detectado(s)
                        </h3>
                        <p class="text-sm text-yellow-700 mb-4">
                            Antes de fechar o caixa, você deve decidir o que fazer com os pedidos que ainda estão pendentes:
                        </p>

                        <!-- Pending Orders List -->
                        <div class="bg-white border border-yellow-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Pedidos Pendentes:</h4>
                            <div class="space-y-2 max-h-32 overflow-y-auto">
                                @foreach($pendingOrders as $order)
                                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded">
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            #{{ $order->code }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $order->customer ? $order->customer->name : 'Cliente não informado' }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-medium text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                                        <span class="text-xs text-gray-500 block">{{ $order->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Total pendente:</span>
                                    <span class="text-lg font-bold text-yellow-800">R$ {{ number_format($pendingOrders->sum('total'), 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4">
                            <p class="text-sm text-yellow-900">
                                🎯 <strong>O que fazer com estes pedidos?</strong> Escolha uma opção abaixo ao fechar o caixa.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

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

                        <div class="mt-6">
                            <form method="POST" action="{{ route('cash-registers.close', $cashRegister) }}" onsubmit="return confirm('Tem certeza que deseja fechar este caixa?\n\nApós o fechamento, não será possível registrar novas vendas neste caixa.')">
                                @csrf

                                <!-- Hidden field for pending action default -->
                                <input type="hidden" name="pending_action" value="allow_close" class="default-action">

                                <!-- Pending Orders Handling Section -->
                                @if(isset($pendingOrders) && $pendingOrders->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                        <span class="mr-2">🛠️</span>O que fazer com {{ $pendingOrders->count() }} pedido(s) pendente(s)?
                                    </h4>

                                    <div class="space-y-3">
                                        <!-- Finalize All Option -->
                                        <label class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="radio" name="pending_action" value="finalize_all"
                                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                       onchange="updateFormFields()">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <span class="font-medium text-gray-700">✅ Finalizar todos pendentes</span>
                                                <p class="text-gray-500">Marcar como pagos e finalizar todos os pedidos pendentes</p>
                                            </div>
                                        </label>

                                        <!-- Cancel All Option -->
                                        <label class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="radio" name="pending_action" value="cancel_all"
                                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                       onchange="updateFormFields()">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <span class="font-medium text-gray-700">❌ Cancelar todos pendentes</span>
                                                <p class="text-gray-500">Cancelar todos os pedidos que não foram pagos</p>
                                            </div>
                                        </label>

                                        <!-- Transfer Option -->
                                        @if(isset($transferRegisters) && $transferRegisters->count() > 0)
                                        <label class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="radio" name="pending_action" value="transfer"
                                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                       onchange="updateFormFields()">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <span class="font-medium text-gray-700">🔄 Transferir para outro caixa</span>
                                                <p class="text-gray-500">Trocar o caixa responsável por estes pedidos</p>
                                                <select name="transfer_to_register" id="transfer_to_register"
                                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        disabled>
                                                    <option value="">Selecione um caixa aberto</option>
                                                    @foreach($transferRegisters as $register)
                                                    <option value="{{ $register->id }}">Caixa #{{ $register->id }} ({{ $register->user->name }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </label>
                                        @endif

                                        <!-- Allow Close Option -->
                                        <label class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="radio" name="pending_action" value="allow_close" checked
                                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                       onchange="updateFormFields()">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <span class="font-medium text-gray-700">⚠️ Fechar mesmo assim</span>
                                                <p class="text-gray-500">Fechar o caixa deixando os pedidos pendentes (não recomendado)</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endif

                                <!-- Balance Section -->
                                <div class="mb-6">
                                    <label for="closing_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                        Saldo Final (R$) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative max-w-xs">
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
                                    @if(isset($expectedTotal))
                                    <p class="mt-1 text-xs text-blue-600">
                                        Saldo esperado: R$ {{ number_format($expectedTotal, 2, ',', '.') }}
                                        <span class="text-gray-500">(considerando {{ isset($pendingOrders) ? $pendingOrders->count() : 0 }} pedidos pendentes)</span>
                                    </p>
                                    @endif
                                </div>

                                <!-- Notes Section -->
                                <div class="mb-6">
                                    <label for="closing_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Observações de Fechamento
                                    </label>
                                    <textarea
                                        id="closing_notes"
                                        name="closing_notes"
                                        rows="3"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                                        placeholder="Ex: Caixa fechado com dinheiro contado..."
                                    ></textarea>
                                </div>

                                <!-- Action Button -->
                                <div class="text-center">
                                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Fechar Caixa
                                    </button>
                                </div>
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

    // Handle pending orders radio buttons
    function updateFormFields() {
        const transferRadio = document.querySelector('input[name="pending_action"][value="transfer"]');
        const transferSelect = document.getElementById('transfer_to_register');

        if (transferRadio && transferSelect) {
            if (transferRadio.checked) {
                transferSelect.disabled = false;
                transferSelect.required = true;
            } else {
                transferSelect.disabled = true;
                transferSelect.required = false;
                transferSelect.value = ''; // Clear selection
            }
        }
    }

    // Initial call to set correct state
    updateFormFields();

    // Add event listeners to all pending action radios
    document.querySelectorAll('input[name="pending_action"]').forEach(function(radio) {
        radio.addEventListener('change', updateFormFields);
    });
});
</script>
@endsection
