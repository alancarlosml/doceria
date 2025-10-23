@extends('layouts.admin')

@section('title', 'Detalhes da Despesa - ' . $expense->description . ' - Doce Doce Brigaderia')

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
                            @if($expense->type === 'entrada')
                                📈
                            @else
                                📉
                            @endif
                        </span>Detalhes da Despesa
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informações completas sobre "{{ $expense->description }}"
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Despesa
                    </a>

                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Expense Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Expense Details Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações da Despesa</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Descrição</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $expense->description }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $expense->type === 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <span class="w-2 h-2 mr-1 rounded-full
                                            {{ $expense->type === 'entrada' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $expense->type === 'entrada' ? 'Entrada' : 'Saída' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Valor</dt>
                                <dd class="mt-1">
                                    <span class="text-lg font-bold
                                        {{ $expense->type === 'entrada' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $expense->type === 'entrada' ? '+' : '-' }}R$ {{ number_format($expense->amount, 2, ',', '.') }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $expense->date->format('d/m/Y') }}</dd>
                            </div>
                            @if($expense->payment_method)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Método de Pagamento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</dd>
                            </div>
                            @endif
                            @if($expense->user)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Responsável</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $expense->user->name }}</dd>
                            </div>
                            @endif
                            @if($expense->notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Observações</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $expense->notes }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $expense->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @if($expense->updated_at != $expense->created_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $expense->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Monthly Statistics Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">📊</span> Estatísticas Mensais
                        </h3>
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de Entradas (mês)</dt>
                                <dd class="mt-1 text-2xl font-bold text-green-600">R$ {{ number_format($totalMonthlyRevenues, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de Saídas (mês)</dt>
                                <dd class="mt-1 text-xl font-bold text-red-600">R$ {{ number_format($totalMonthlyExpenses, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Saldo Atual (mês)</dt>
                                <dd class="mt-1 text-3xl font-bold
                                    {{ ($totalMonthlyRevenues - $totalMonthlyExpenses) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($totalMonthlyRevenues - $totalMonthlyExpenses) >= 0 ? '+' : '-' }}
                                    R$ {{ number_format(abs($totalMonthlyRevenues - $totalMonthlyExpenses), 2, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Related Activity Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">📋</span> Atividades Relacionadas
                        </h3>

                        <div class="space-y-4">
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">
                                    @if($expense->type === 'entrada')
                                        💰
                                    @else
                                        💸
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">
                                    @if($expense->type === 'entrada')
                                        Esta é uma entrada de recursos
                                    @else
                                        Esta é uma saída de recursos
                                    @endif
                                </p>
                                <div class="mt-4 text-xs text-gray-400">
                                    ID da transação: #{{ $expense->id }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 border-t pt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Status do Registro</p>
                                    <p class="text-sm font-medium text-green-600">Ativo</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Valor</p>
                                    <p class="text-lg font-bold
                                        {{ $expense->type === 'entrada' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $expense->type === 'entrada' ? '+' : '-' }}R$ {{ number_format($expense->amount, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
