@extends('layouts.admin')

@section('title', 'Caixa #' . $cashRegister->id . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">
                            {{ $cashRegister->isOpen() ? '🔓' : '🔒' }}
                        </span>Caixa #{{ $cashRegister->id }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $cashRegister->isOpen() ? 'Caixa atualmente aberto' : 'Caixa fechado' }}
                        - Aberto em {{ $cashRegister->opened_at->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    @if($cashRegister->isOpen())
                        <a href="{{ route('cash-registers.edit', $cashRegister) }}" class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Caixa
                        </a>
                    @endif
                    <a href="{{ route('cash-registers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Status Alert -->
            @if($cashRegister->isOpen())
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Caixa Aberto!</strong> Este caixa está ativo e aceitando vendas e registros.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border-l-4 border-gray-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">
                                <strong>Caixa Fechado!</strong> Este caixa foi finalizado em {{ $cashRegister->closed_at->format('d/m/Y \à\s H:i') }}.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cash Register Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Opening Information -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações de Abertura</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Saldo Inicial</dt>
                                <dd class="mt-1 text-lg font-semibold text-green-600">R$ {{ number_format($cashRegister->opening_balance, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data/Hora Abertura</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashRegister->opened_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Responsável</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashRegister->user->name }}</dd>
                            </div>
                            @if($cashRegister->opening_notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Observações</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashRegister->opening_notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Current Balances -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Balanço Atual</h3>
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Vendas Realizadas</dt>
                                <dd class="mt-1 text-xl font-bold text-green-600">R$ {{ number_format($totalSales, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Despesas</dt>
                                <dd class="mt-1 text-lg font-semibold text-red-600">- R$ {{ number_format($totalExpenses, 2, ',', '.') }}</dd>
                            </div>
                            @if($totalRevenues > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Entradas Extras</dt>
                                <dd class="mt-1 text-lg font-semibold text-blue-600">+ R$ {{ number_format($totalRevenues, 2, ',', '.') }}</dd>
                            </div>
                            @endif
                            <div class="pt-4 border-t">
                                <dt class="text-sm font-medium text-gray-500">Saldo Esperado</dt>
                                <dd class="mt-1 text-2xl font-bold
                                    {{ $expectedBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $expectedBalance >= 0 ? '+' : '-' }} R$ {{ number_format(abs($expectedBalance), 2, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Closing Information -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações de Fechamento</h3>
                        @if($cashRegister->isClosed())
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-8">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Saldo Final</dt>
                                    <dd class="mt-1 text-lg font-semibold text-blue-600">R$ {{ number_format($cashRegister->closing_balance, 2, ',', '.') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Data/Hora Fechamento</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $cashRegister->closed_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div class="pt-4 border-t">
                                    <dt class="text-sm font-medium text-gray-500">Diferença</dt>
                                    <dd class="mt-1">
                                        @php
                                            $difference = ($cashRegister->closing_balance ?? 0) - $expectedBalance;
                                        @endphp
                                        <span class="text-lg font-bold
                                            {{ $difference == 0 ? 'text-green-600' : ($difference > 0 ? 'text-blue-600' : 'text-red-600') }}">
                                            {{ $difference == 0 ? '✓' : ($difference > 0 ? '+' : '-') }}
                                            R$ {{ number_format(abs($difference), 2, ',', '.') }}
                                        </span>
                                    </dd>
                                </div>
                                @if($cashRegister->closing_notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Observações</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $cashRegister->closing_notes }}</dd>
                                </div>
                                @endif
                            </dl>
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">⏳</div>
                                <p class="text-sm text-gray-500">Caixa ainda não foi fechado</p>
                                <p class="text-xs text-gray-400 mt-2">Aguardando fechamento do expediente</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sales Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M7 11h10a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Vendas</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $salesCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ticket Médio</dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    R$ {{ $salesCount > 0 ? number_format($totalSales / $salesCount, 2, ',', '.') : '0,00' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-yellow-100 p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2m0 0V9a2 2 0 01-2 2H9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Despesas</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $expensesCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Método Mais Usado</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    {{ $paymentMethods ? ($paymentMethods->count . 'x ' . ucfirst(str_replace('_', ' ', $paymentMethods->payment_method))) : 'Sem vendas' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales & Expenses -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Sales -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">🛒</span>Vendas Realizadas ({{ $cashRegister->sales->whereNotIn('status', ['cancelado'])->count() }})
                        </h3>

                        @if($cashRegister->sales->whereNotIn('status', ['cancelado'])->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($cashRegister->sales->whereNotIn('status', ['cancelado'])->sortByDesc('created_at')->take(5) as $sale)
                                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    Pedido #{{ $sale->id }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $sale->customer->name ?? 'Cliente não identificado' }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-semibold text-green-600">
                                                    R$ {{ number_format($sale->total, 2, ',', '.') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $sale->created_at->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-600">
                                            {{ $sale->items->count() }} produto{{ $sale->items->count() !== 1 ? 's' : '' }}
                                            @if($sale->items->first())
                                                - {{ Str::limit($sale->items->first()->product->name, 30) }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">🛒</div>
                                <p class="text-sm text-gray-500">Nenhuma venda realizada neste caixa</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Expenses -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">💸</span>Despesas Registradas ({{ $cashRegister->expenses->where('type', 'saida')->count() }})
                        </h3>

                        @if($cashRegister->expenses->where('type', 'saida')->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($cashRegister->expenses->where('type', 'saida')->sortByDesc('created_at')->take(5) as $expense)
                                    <div class="border border-red-200 bg-red-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ Str::limit($expense->description, 40) }}
                                                </div>
                                                <div class="text-xs text-red-600">
                                                    {{ $expense->user->name ?? 'Sistema' }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-semibold text-red-600">
                                                    - R$ {{ number_format($expense->amount, 2, ',', '.') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $expense->date->format('d/m H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">💸</div>
                                <p class="text-sm text-gray-500">Nenhuma despesa registrada neste caixa</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
