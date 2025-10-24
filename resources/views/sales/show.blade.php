@extends('layouts.admin')

@section('title', 'Venda - ' . $sale->code . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">🛒</span>Venda {{ $sale->code }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Detalhes completos da venda realizada em {{ $sale->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>

                    @if(\App\Models\CashRegister::where('status', 'aberto')->exists() && !$sale->isCancelado() && !$sale->isFinalizado())
                        <button type="button" onclick="changeStatus('{{$sale->id}}', 'finalizado')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Finalizar Venda
                        </button>
                    @endif
                </div>
            </div>

            <!-- Sale Status Banner -->
            <div class="bg-white shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @php
                                    $statusConfig = \App\Models\Sale::getStatusConfig($sale->status);
                                @endphp
                                <div class="rounded-full p-2 {{ $statusConfig['bg'] }}">
                                    {!! $statusConfig['icon'] !!}
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Status: {{ $statusConfig['label'] }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $sale->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if(!$sale->isCancelado() && !$sale->isFinalizado())
                            <div class="flex space-x-2">
                                <select id="statusSelect" class="rounded-md text-sm border-gray-300 bg-transparent px-4 py-2.5">
                                    @foreach(\App\Models\Sale::getAvailableStatuses() as $status => $label)
                                        @if($status !== $sale->status)
                                            <option value="{{ $status }}">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="button" onclick="updateStatus('{{$sale->id}}')" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    Atualizar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sale Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Sale Details -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="mr-3">📋</span>Informações da Venda
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Código:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->code }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tipo:</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize">
                                @switch($sale->type)
                                    @case('balcao')
                                        Balcão
                                        @break
                                    @case('delivery')
                                        Delivery
                                        @break
                                    @case('encomenda')
                                        Encomenda
                                        @break
                                    @default
                                        {{ $sale->type }}
                                @endswitch
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Vendedor:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->user->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Caixa:</dt>
                            <dd class="text-sm font-medium text-gray-900">Caixa #{{ $sale->cashRegister->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Criado em:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        @if($sale->payment_method)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Pagamento:</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize">
                                @switch($sale->payment_method)
                                    @case('dinheiro')
                                        💵 Dinheiro
                                        @break
                                    @case('cartao_credito')
                                        💳 Crédito
                                        @break
                                    @case('cartao_debito')
                                        💳 Débito
                                        @break
                                    @case('pix')
                                        📱 PIX
                                        @break
                                    @case('transferencia')
                                        🏦 Transferência
                                        @break
                                    @default
                                        {{ $sale->payment_method }}
                                @endswitch
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Customer Information -->
                @if($sale->customer)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="mr-3">👤</span>Cliente
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Nome:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->customer->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Telefone:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->customer->phone ?? 'Não informado' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">E-mail:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->customer->email ?? 'Não informado' }}</dd>
                        </div>
                        @if($sale->delivery_address)
                        <div>
                            <dt class="text-sm text-gray-500">Endereço:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->delivery_address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @else
                <div class="bg-gray-50 shadow rounded-lg p-6 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 mt-2">Cliente não informado</p>
                    </div>
                </div>
                @endif

                <!-- Table/Delivery Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        @if($sale->type === 'delivery')
                            <span class="mr-3">🚚</span>Delivery
                        @elseif($sale->table)
                            <span class="mr-3">🍽️</span>Mesa
                        @else
                            <span class="mr-3">📋</span>Informações
                        @endif
                    </h3>
                    <dl class="space-y-3">
                        @if($sale->table)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Mesa:</dt>
                            <dd class="text-sm font-medium text-gray-900">Mesa {{ $sale->table->number }}</dd>
                        </div>
                        @endif

                        @if($sale->motoboy)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Motoboy:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $sale->motoboy->name }}</dd>
                        </div>
                        @endif

                        @if($sale->type === 'delivery')
                            @if($sale->delivery_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Taxa de entrega:</dt>
                                <dd class="text-sm font-medium text-gray-900">R$ {{ number_format($sale->delivery_fee, 2, ',', '.') }}</dd>
                            </div>
                            @endif
                        @endif

                        @if($sale->type === 'encomenda')
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Entrega:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($sale->delivery_date . ' ' . $sale->delivery_time)->format('d/m/Y H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="mr-3">📦</span>Itens do Pedido
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produto
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantidade
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Preço Unit.
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sale->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->product->name }}
                                        </div>
                                        @if($item->product->category)
                                            <div class="text-sm text-gray-500">
                                                {{ $item->product->category->name }}
                                            </div>
                                        @endif
                                        @if($item->notes)
                                            <div class="text-xs text-gray-400 italic">
                                                Observação: {{ $item->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="w-full max-w-sm">
                            <dl class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Subtotal:</dt>
                                    <dd class="font-medium text-gray-900">R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</dd>
                                </div>
                                @if($sale->discount > 0)
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Desconto:</dt>
                                    <dd class="font-medium text-red-600">-R$ {{ number_format($sale->discount, 2, ',', '.') }}</dd>
                                </div>
                                @endif
                                @if($sale->delivery_fee > 0)
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Taxa de entrega:</dt>
                                    <dd class="font-medium text-gray-900">R$ {{ number_format($sale->delivery_fee, 2, ',', '.') }}</dd>
                                </div>
                                @endif
                                <div class="flex justify-between pt-2 border-t border-gray-300">
                                    <dt class="text-base font-medium text-gray-900">Total:</dt>
                                    <dd class="text-base font-bold text-gray-900">R$ {{ number_format($sale->total, 2, ',', '.') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($sale->notes)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Observações:</strong> {{ $sale->notes }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</main>

<!-- Modal for Payment Method Selection -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" id="my-modal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Selecionar Método de Pagamento</h3>
            <form id="paymentForm">
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                        Método de Pagamento <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_method" name="payment_method" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecione um método</option>
                        <option value="dinheiro">💵 Dinheiro</option>
                        <option value="cartao_credito">💳 Cartão de Crédito</option>
                        <option value="cartao_debito">💳 Cartão de Débito</option>
                        <option value="pix">📱 PIX</option>
                        <option value="transferencia">🏦 Transferência</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePaymentModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(saleId) {
    const newStatus = document.getElementById('statusSelect').value;

    fetch(`{{ route('sales.update-status', ':id') }}`.replace(':id', saleId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status atualizado com sucesso!');
            location.reload();
        } else {
            alert('Erro ao atualizar status: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao processar requisição');
    });
}

function changeStatus(saleId, status) {
    if (status === 'finalizado') {
        // Show payment modal for finalization
        showPaymentModal(saleId);
    } else {
        // Update status normally
        updateStatus(saleId);
    }
}

function showPaymentModal(saleId) {
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentForm').onsubmit = function(e) {
        e.preventDefault();
        finalizeSale(saleId, document.getElementById('payment_method').value);
    };
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function finalizeSale(saleId, paymentMethod) {
    fetch(`{{ route('sales.finalize', ':id') }}`.replace(':id', saleId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_method: paymentMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Venda finalizada com sucesso!');
            location.reload();
        } else {
            alert('Erro ao finalizar venda: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao processar requisição');
    });
}
</script>
@endsection
