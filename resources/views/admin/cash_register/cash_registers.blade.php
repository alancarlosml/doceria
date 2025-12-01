@extends('layouts.admin')

@section('title', 'Caixas - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">💰</span>Caixas
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Controle os caixas abertos e fechados da doceria.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <!-- Show open/close button based on current status -->
                    @if($openRegister)
                        <a href="{{ route('cash-registers.close-form') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Fechar Caixa
                        </a>
                    @else
                        <a href="{{ route('cash-registers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Abrir Novo Caixa
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filtro de Período -->
            <div class="bg-white shadow rounded-lg p-4 mb-8">
                <form method="GET" action="{{ route('cash-registers.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                        <select name="periodo" id="periodo" onchange="toggleCustomDates(this.value)" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="mes_atual" {{ ($periodo ?? 'mes_atual') == 'mes_atual' ? 'selected' : '' }}>Mês Atual</option>
                            <option value="mes_anterior" {{ ($periodo ?? '') == 'mes_anterior' ? 'selected' : '' }}>Mês Anterior</option>
                            <option value="customizado" {{ ($periodo ?? '') == 'customizado' ? 'selected' : '' }}>Período Customizado</option>
                        </select>
                    </div>
                    
                    <div id="custom-dates" class="{{ ($periodo ?? 'mes_atual') == 'customizado' ? 'flex' : 'hidden' }} gap-4">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                            <input type="date" name="date_from" id="date_from" value="{{ $dateFrom ?? '' }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                            <input type="date" name="date_to" id="date_to" value="{{ $dateTo ?? '' }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <script>
                function toggleCustomDates(value) {
                    const customDates = document.getElementById('custom-dates');
                    if (value === 'customizado') {
                        customDates.classList.remove('hidden');
                        customDates.classList.add('flex');
                    } else {
                        customDates.classList.add('hidden');
                        customDates.classList.remove('flex');
                    }
                }
            </script>

            <!-- Current Status Alert -->
            @if($openRegister)
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Caixa Aberto!</strong> Há um caixa atualmente aberto.
                                <a href="{{ route('cash-registers.show', $openRegister) }}" class="font-medium underline text-green-700 hover:text-green-600">
                                    Ver detalhes →
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Caixa Fechado!</strong> Nenhum caixa está aberto no momento.
                                Abra um novo caixa para registrar vendas.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(Auth::user()->hasRole('admin'))
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Caixas</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\CashRegister::count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Caixas Abertos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\CashRegister::where('status', 'aberto')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-orange-100 p-3">
                                <span class="text-2xl">📦</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Encomendas no Período</dt>
                                <dd class="text-2xl font-semibold text-gray-900">R$ {{ number_format($totalEncomendasPeriodo, 2, ',', '.') }}</dd>
                                <dd class="text-xs text-gray-500">{{ $countEncomendasPeriodo }} finalizadas</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Vendas Totais</dt>
                                <dd class="text-2xl font-semibold text-gray-900">R$ {{ number_format(\App\Models\CashRegister::join('sales', 'cash_registers.id', '=', 'sales.cash_register_id')->whereNotIn('sales.status', ['cancelado'])->sum('sales.total'), 2, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cash Registers Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Histórico de Caixas</h3>
                    <p class="mt-1 text-sm text-gray-600">Gerencie todos os caixas abertos e fechados da doceria.</p>
                </div>

                <div class="border-t border-gray-200">
                    @if($cashRegisters->isEmpty())
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum caixa registrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece abrindo o primeiro caixa da doceria.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Informação do Caixa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Movimentações
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Saldos
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cashRegisters as $cashRegister)
                                    <tr class="hover:bg-gray-50">
                                        <!-- Cash Register Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br flex items-center justify-center
                                                        {{ $cashRegister->status === 'aberto' ? 'from-green-100 to-green-200' : 'from-gray-100 to-gray-200' }}">
                                                        <span class="text-lg">{{ $cashRegister->status === 'aberto' ? '🔓' : '🔒' }}</span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        Caixa #{{ $cashRegister->id }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        Aberto: {{ $cashRegister->opened_at->format('d/m/Y H:i') }}
                                                    </div>
                                                    @if($cashRegister->status === 'fechado')
                                                        <div class="text-xs text-gray-400">
                                                            Fechado: {{ $cashRegister->closed_at->format('d/m/Y H:i') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($cashRegister->status === 'aberto')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🔓 Aberto
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        🔒 Fechado
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ $cashRegister->user->name }}
                                            </div>
                                        </td>

                                        <!-- Movements -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="space-y-1">
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">{{ $cashRegister->sales()->whereNotIn('status', ['cancelado'])->count() }}</span> vendas
                                                </div>
                                                @php
                                                    $caixaDate = $cashRegister->opened_at->format('Y-m-d');
                                                    $finalizedOrdersCount = $cashRegister->encomendas()->count();
                                                @endphp
                                                @if($finalizedOrdersCount > 0)
                                                    <div class="text-sm text-gray-600">
                                                        <span class="font-medium">{{ $finalizedOrdersCount }}</span> encomendas finalizadas
                                                    </div>
                                                @endif
                                                @if($cashRegister->expenses()->where('type', 'saida')->count() > 0)
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">{{ $cashRegister->expenses()->where('type', 'saida')->count() }}</span> despesas
                                                </div>
                                                @endif
                                                @if($cashRegister->expenses()->where('type', 'entrada')->count() > 0)
                                                    <div class="text-sm text-gray-600">
                                                        <span class="font-medium">{{ $cashRegister->expenses()->where('type', 'entrada')->count() }}</span> entradas
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Balances -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="space-y-1">
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">Abertura:</span> R$ {{ number_format($cashRegister->opening_balance, 2, ',', '.') }}
                                                </div>
                                                @if($cashRegister->closing_balance)
                                                    <div class="text-sm text-gray-600">
                                                        <span class="font-medium">Fechamento:</span> R$ {{ number_format($cashRegister->closing_balance, 2, ',', '.') }}
                                                    </div>
                                                @endif
                                                <div class="
                                                    {{ $cashRegister->getTotalSales() > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                                    <span class="font-medium">Vendas:</span> R$ {{ number_format($cashRegister->getTotalSales(), 2, ',', '.') }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('cash-registers.show', $cashRegister) }}" class="text-green-600 hover:text-green-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('cash-registers.sales', $cashRegister) }}" class="inline-flex items-center px-3 py-1 border border-blue-300 text-sm leading-5 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h4l.5 2H21a2 2 0 011.062 3.5l-.3 2.7a2 2 0 01-2 1.8H6l-.5 2h16M7.5 21a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM18.5 21a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                                                    </svg>
                                                    Ver Vendas
                                                </a>
                                                @if($cashRegister->status === 'aberto')
                                                    <a href="{{ route('cash-registers.edit', $cashRegister) }}" class="text-blue-600 hover:text-blue-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                @if($cashRegisters->onFirstPage())
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md leading-5">
                                        Anterior
                                    </span>
                                @else
                                    <a href="{{ $cashRegisters->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md leading-5 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                        Anterior
                                    </a>
                                @endif
                                @if($cashRegisters->hasMorePages())
                                    <a href="{{ $cashRegisters->appends(request()->query())->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md leading-5 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                        Próximo
                                    </a>
                                @else
                                    <span class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md leading-5">
                                        Próximo
                                    </span>
                                @endif
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 leading-5">
                                        Mostrando
                                        <span class="font-medium">{{ $cashRegisters->firstItem() }}</span>
                                        a
                                        <span class="font-medium">{{ $cashRegisters->lastItem() }}</span>
                                        de
                                        <span class="font-medium">{{ $cashRegisters->total() }}</span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm">
                                        @if($cashRegisters->onFirstPage())
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <a href="{{ $cashRegisters->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @endif

                                        @foreach($cashRegisters->getUrlRange(1, $cashRegisters->lastPage()) as $page => $url)
                                            @if($page == $cashRegisters->currentPage())
                                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}?{{ http_build_query(request()->query()) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">{{ $page }}</a>
                                            @endif
                                        @endforeach

                                        @if($cashRegisters->hasMorePages())
                                            <a href="{{ $cashRegisters->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 111.414 1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 111.414 1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
