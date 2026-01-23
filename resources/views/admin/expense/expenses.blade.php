@extends('layouts.admin')

@section('title', 'Entradas/Saídas - Doceria Delícia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        💲 Entradas/Saídas
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Controle financeiro - registre entradas e saídas da doceria.
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Novo Registro
                    </a>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <!-- Type Filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select id="type" name="type" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                <option value="">Todos</option>
                                <option value="entrada" {{ request()->input('type') === 'entrada' ? 'selected' : '' }}>Receitas (Entradas)</option>
                                <option value="saida" {{ request()->input('type') === 'saida' ? 'selected' : '' }}>Despesas (Saídas)</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" id="search" name="search" value="{{ request()->input('search') }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Descrição...">
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request()->input('date_from') }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request()->input('date_to') }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        </div>

                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <button type="button"
                                    id="filter-btn"
                                    class="inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                🔄 Filtrar
                            </button>
                            <button type="button"
                                    id="clear-filters-btn"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                🗑️ Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">
                <!-- Total Entradas -->
                <div class="bg-white shadow rounded-lg overflow-hidden {{ request()->input('type') === 'entrada' || !request()->input('type') ? '' : 'opacity-60' }}">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Entradas</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-green-600">
                                            R$ {{ number_format($totalEntradas, 2, ',', '.') }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Saídas -->
                <div class="bg-white shadow rounded-lg overflow-hidden {{ request()->input('type') === 'saida' || !request()->input('type') ? '' : 'opacity-60' }}">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Saídas</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-red-600">
                                            R$ {{ number_format($totalSaidas, 2, ',', '.') }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saldo Líquido -->
                <div class="bg-white shadow rounded-lg overflow-hidden border-2 {{ $saldoLiquido >= 0 ? 'border-green-300' : 'border-red-300' }}">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 {{ $saldoLiquido >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-md p-3">
                                <svg class="h-6 w-6 {{ $saldoLiquido >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Saldo Líquido</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold {{ $saldoLiquido >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $saldoLiquido >= 0 ? '+' : '' }}R$ {{ number_format($saldoLiquido, 2, ',', '.') }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Registros Financeiros</h3>
                        <div class="text-sm text-gray-500">
                            <span id="expenses-count">{{ $expenses->total() }}</span> registros encontrados
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Movimento
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Método
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($expenses as $expense)
                                <tr class="hover:bg-gray-50">
                                    <!-- Description -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br {{ $expense->type === 'entrada' ? 'from-green-100 to-green-200' : 'from-red-100 to-red-200' }} flex items-center justify-center">
                                                    <span class="text-lg">{{ $expense->type === 'entrada' ? '💰' : '💸' }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($expense->description, 40) }}</div>
                                                <div class="text-sm text-gray-500">
                                                    Por: {{ $expense->user->name ?? 'Sistema' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $expense->date->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $expense->date->diffForHumans() }}</div>
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold {{ $expense->type === 'entrada' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $expense->type === 'entrada' ? '+' : '-' }} R$ {{ number_format($expense->amount, 2, ',', '.') }}
                                        </div>
                                    </td>

                                    <!-- Type -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($expense->type === 'entrada')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✅ Entrada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                ❌ Saída
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Payment Method -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            @switch($expense->payment_method)
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
                                                @case('boleto')
                                                    📄 Boleto
                                                    @break
                                                @default
                                                    ❓ {{ $expense->payment_method }}
                                            @endswitch
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('expenses.show', $expense) }}" class="text-green-600 hover:text-green-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <a href="{{ route('expenses.edit', $expense) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este registro financeiro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                    <div class="hidden sm:block">
                        <div class="text-sm text-gray-700">
                            Mostrando <span class="font-medium">{{ $expenses->firstItem() }}</span> a <span class="font-medium">{{ $expenses->lastItem() }}</span> de <span class="font-medium">{{ $expenses->total() }}</span> resultados
                        </div>
                    </div>
                        <div class="flex space-x-1">
                            <!-- Pagination basic -->
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                Anterior
                            </span>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-blue-600">
                                1
                            </span>
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                Próximo
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filter-btn');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');

    // Função para aplicar filtros
    function applyFilters() {
        const type = document.getElementById('type').value;
        const search = document.getElementById('search').value;
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;

        // Construir URL com parâmetros
        let url = '{{ route("expenses.index") }}?';
        const params = [];

        if (type) params.push('type=' + encodeURIComponent(type));
        if (search) params.push('search=' + encodeURIComponent(search));
        if (dateFrom) params.push('date_from=' + encodeURIComponent(dateFrom));
        if (dateTo) params.push('date_to=' + encodeURIComponent(dateTo));

        url += params.join('&');

        // Redirecionar para aplicar filtros
        window.location.href = url;
    }

    // Função para limpar filtros
    function clearFilters() {
        document.getElementById('type').value = '';
        document.getElementById('search').value = '';
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';

        // Voltar para URL sem filtros
        window.location.href = '{{ route("expenses.index") }}';
    }

    // Event listeners
    if (filterBtn) {
        filterBtn.addEventListener('click', applyFilters);
    }

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }

    // Permitir filtrar pressionando Enter nos campos
    const inputs = ['search', 'date_from', 'date_to'];
    inputs.forEach(inputId => {
        const element = document.getElementById(inputId);
        if (element) {
            element.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });
        }
    });

    // Permitir filtrar quando mudar o select de tipo
    const typeSelect = document.getElementById('type');
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            // Aplicar filtro automaticamente ao mudar o tipo
            setTimeout(applyFilters, 100);
        });
    }
});
</script>
@endsection
