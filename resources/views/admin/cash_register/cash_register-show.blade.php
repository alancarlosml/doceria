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
                                    R$ {{ $salesCount > 0 ? number_format($totalSalesOnly / $salesCount, 2, ',', '.') : '0,00' }}
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
                                    @if($paymentMethods)
                                        @php
                                            $paymentMethod = $paymentMethods->payment_method;
                                            // Tratar enum PaymentMethod
                                            if ($paymentMethod instanceof \App\Enums\PaymentMethod) {
                                                $methodLabel = $paymentMethod->label();
                                            } else {
                                                // Se for string, converter para enum e obter label
                                                $enumMethod = \App\Enums\PaymentMethod::tryFrom($paymentMethod);
                                                $methodLabel = $enumMethod ? $enumMethod->label() : ucfirst(str_replace('_', ' ', $paymentMethod));
                                            }
                                        @endphp
                                        {{ $paymentMethods->count }}x {{ $methodLabel }}
                                    @else
                                        Sem vendas
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dd class="mt-1 text-xl font-bold text-green-600">R$ {{ number_format($totalSalesOnly, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Encomendas Finalizadas</dt>
                                <dd class="mt-1 text-xl font-bold text-green-600">R$ {{ number_format($totalEncomendas, 2, ',', '.') }}</dd>
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

            <!-- Daily Summary for Cash Register -->
            @if(isset($dailySummary) && $dailySummary['sales_count'] > 0)
            <div class="mt-8 bg-gradient-to-r from-green-50 to-blue-50 shadow-lg rounded-lg overflow-hidden border-2 border-green-200 mb-8">
                <div class="px-6 py-4 bg-green-600 text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <span class="mr-3">📊</span>Resumo Operacional - {{ $cashRegister->isOpen() ? 'Caixa Atual' : 'Dia ' . $dailySummary['date'] }}
                    </h3>
                    <p class="mt-1 text-green-100">Resumo das vendas e pagamentos {{ $cashRegister->isOpen() ? 'do período atual' : 'do caixa completo' }}</p>
                </div>

                <div class="p-6">
                    <!-- Principais Métricas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Total de Vendas -->
                        <div class="bg-white rounded-lg p-4 shadow border">
                            <div class="flex items-center">
                                <div class="rounded-full bg-green-100 p-3 mr-4">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Total de Vendas</p>
                                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($dailySummary['total_sales'], 2, ',', '.') }}</p>
                                    <p class="text-xs text-gray-600">{{ $dailySummary['sales_count'] }} vendas</p>
                                </div>
                            </div>
                        </div>

                        <!-- Encomendas Pagas -->
                        <div class="bg-white rounded-lg p-4 shadow border">
                            <div class="flex items-center">
                                <div class="rounded-full bg-blue-100 p-3 mr-4">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Encomendas Pagas</p>
                                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($dailySummary['paid_orders_total'], 2, ',', '.') }}</p>
                                    <p class="text-xs text-gray-600">{{ $dailySummary['paid_orders_count'] }} encomendas finalizadas</p>
                                </div>
                            </div>
                        </div>

                        <!-- Saldo Operacional -->
                        <div class="bg-white rounded-lg p-4 shadow border">
                            <div class="flex items-center">
                                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">{{ $cashRegister->isOpen() ? 'Saldo Atual' : 'Resultado Final' }}</p>
                                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($cashRegister->isOpen() ? $dailySummary['current_expected'] : ($dailySummary['final_result'] ?? $dailySummary['current_expected']), 2, ',', '.') }}</p>
                                    <p class="text-xs text-gray-600">Saldo inicial: R$ {{ number_format($dailySummary['opening_balance'], 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Financeiras Adicionais -->
                    @if($cashRegister->isOpen() || ($totalExpenses > 0 || $totalRevenues > 0))
                    <div class="mt-6 mb-6 *:grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($totalExpenses > 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="rounded-full bg-red-100 p-2 mr-3">
                                    <span class="text-red-600 text-xs">💸</span>
                                </div>
                                <div>
                                    <p class="text-xs text-red-600 font-medium">Despesas</p>
                                    <p class="text-sm font-bold text-red-700">- R$ {{ number_format($totalExpenses, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($totalRevenues > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="rounded-full bg-blue-100 p-2 mr-3">
                                    <span class="text-blue-600 text-xs">➕</span>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 font-medium">Entradas Extras</p>
                                    <p class="text-sm font-bold text-blue-700">+ R$ {{ number_format($totalRevenues, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="rounded-full bg-indigo-100 p-2 mr-3">
                                    <span class="text-indigo-600 text-xs">📊</span>
                                </div>
                                <div>
                                    <p class="text-xs text-indigo-600 font-medium">Lucro Operacional</p>
                                    <p class="text-sm font-bold text-indigo-700">
                                        {{ $totalSalesOnly + $totalEncomendas - $totalExpenses + $totalRevenues >= 0 ? '+' : '-' }}
                                        R$ {{ number_format(abs($totalSalesOnly + $totalEncomendas - $totalExpenses + $totalRevenues), 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Formas de Pagamento e Motoboys -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Formas de Pagamento -->
                        <div class="bg-white rounded-lg p-4 shadow border">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="mr-2">💳</span>Formas de Pagamento
                            </h4>

                            <div class="space-y-3">
                                <!-- PIX -->
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded border">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                        <span class="font-medium text-gray-700">PIX</span>
                                    </div>
                                    <span class="font-bold text-green-700">R$ {{ number_format($dailySummary['payment_methods']['pix'], 2, ',', '.') }}</span>
                                </div>

                                <!-- Cartão -->
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded border">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="font-medium text-gray-700">Cartão</span>
                                    </div>
                                    <span class="font-bold text-blue-700">R$ {{ number_format($dailySummary['payment_methods']['cartao'], 2, ',', '.') }}</span>
                                </div>

                                <!-- Dinheiro -->
                                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded border">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                        <span class="font-medium text-gray-700">Dinheiro</span>
                                    </div>
                                    <span class="font-bold text-yellow-700">R$ {{ number_format($dailySummary['payment_methods']['dinheiro'], 2, ',', '.') }}</span>
                                </div>

                                @if($dailySummary['payment_methods']['outros'] > 0)
                                <!-- Outros -->
                                <div class="flex justify-between items-center p-3 bg-purple-50 rounded border">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                        <span class="font-medium text-gray-700">Outros</span>
                                    </div>
                                    <span class="font-bold text-purple-700">R$ {{ number_format($dailySummary['payment_methods']['outros'], 2, ',', '.') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Valores por Motoboy -->
                        <div class="bg-white rounded-lg p-4 shadow border">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="mr-2">🏍️</span>Delivery
                            </h4>

                            <div class="text-sm text-gray-600 mb-4">
                                <p>{{ $dailySummary['delivery_orders_count'] }} entregas realizadas</p>
                                <p class="font-medium text-gray-900">Total em entregas: R$ {{ number_format($dailySummary['delivery_orders_total'], 2, ',', '.') }}</p>
                            </div>

                            @if(count($dailySummary['motoboy_earnings']) > 0)
                                <div class="space-y-2">
                                    @foreach($dailySummary['motoboy_earnings'] as $earning)
                                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                        <div>
                                            <span class="font-medium text-gray-800">{{ $earning['name'] }}</span>
                                            <span class="text-xs text-gray-600 ml-2">({{ $earning['orders_count'] }} entregas)</span>
                                        </div>
                                        <span class="font-bold text-green-600">R$ {{ number_format($earning['total_value'], 2, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Nenhuma entrega realizada</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

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
