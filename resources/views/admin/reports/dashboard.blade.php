@extends('layouts.admin')

@section('title', 'Dashboard de Relatórios - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">📊</span>Relatórios e Análises
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Métricas essenciais para tomada de decisões estratégicas da sua doceria.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <!-- Filtros de período -->
                    <form method="GET" action="{{ route('reports.dashboard') }}" class="flex space-x-2">
                        <div>
                            <label for="start_date" class="block text-xs font-medium text-gray-500">Data Inicial</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $period['start'] }}"
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-medium text-gray-500">Data Final</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $period['end'] }}"
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                📈 Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- KPIs Principais -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Receita Total -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                                <dd class="text-3xl font-semibold text-gray-900">R$ {{ number_format($kpis['total_revenue'], 2, ',', '.') }}</dd>
                                <dd class="text-sm text-gray-500">{{ $kpis['total_sales'] }} vendas realizadas</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Ticket Médio -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ticket Médio</dt>
                                <dd class="text-3xl font-semibold text-gray-900">R$ {{ number_format($kpis['avg_ticket'], 2, ',', '.') }}</dd>
                                <dd class="text-sm text-gray-500">Média por venda</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Lucro Líquido -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md {{ $kpis['net_profit'] >= 0 ? 'bg-green-100' : 'bg-red-100' }} p-3">
                                <svg class="h-8 w-8 {{ $kpis['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Lucro Líquido</dt>
                                <dd class="text-3xl font-semibold {{ $kpis['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    R$ {{ number_format($kpis['net_profit'], 2, ',', '.') }}
                                </dd>
                                <dd class="text-sm text-gray-500">Margem: {{ number_format($kpis['profit_margin'], 1) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Total Itens -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Itens Vendidos</dt>
                                <dd class="text-3xl font-semibold text-gray-900">{{ number_format($kpis['total_items']) }}</dd>
                                <dd class="text-sm text-gray-500">Produtos comercializados</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Análises -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Top Produtos -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">🏆 Top Produtos</h3>
                            <a href="{{ route('reports.products') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver detalhado →</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($topProducts->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($topProducts->take(5) as $index => $product)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="text-lg font-semibold text-blue-600 mr-3">#{{ $index + 1 }}</span>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $product->total_sold }} unidades vendidas</p>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-green-600">
                                            R$ {{ number_format($product->revenue, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Nenhuma venda encontrada no período selecionado.</p>
                        @endif
                    </div>
                </div>

                <!-- Top Funcionários -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">👨‍💼 Performance Atendentes</h3>
                            <a href="{{ route('reports.employees') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver detalhado →</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($salesByEmployee->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($salesByEmployee->take(5) as $index => $employee)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="text-lg font-semibold text-purple-600 mr-3">#{{ $index + 1 }}</span>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $employee->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $employee->total_sales }} vendas realizadas</p>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-green-600">
                                            R$ {{ number_format($employee->total_revenue, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Nenhuma venda encontrada no período selecionado.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Outros Relatórios -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📊 Outros Relatórios Disponíveis</h3>
                    <p class="mt-1 text-sm text-gray-500">Explore análises detalhadas de diferentes aspectos do negócio</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Vendas por Produto -->
                        <a href="{{ route('reports.products') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="rounded-full bg-blue-200 p-3 mr-3 group-hover:bg-blue-300 transition-colors">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 group-hover:text-blue-800">Vendas por Produto</h4>
                                    <p class="text-sm text-gray-600">Análise de performance de produtos</p>
                                </div>
                            </div>
                        </a>

                        <!-- Fluxo de Caixa -->
                        <a href="{{ route('reports.cash-flow') }}" class="group bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="rounded-full bg-green-200 p-3 mr-3 group-hover:bg-green-300 transition-colors">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 group-hover:text-green-800">Fluxo de Caixa</h4>
                                    <p class="text-sm text-gray-600">Receitas vs Despesas diárias</p>
                                </div>
                            </div>
                        </a>

                        <!-- Performance Atendentes -->
                        <a href="{{ route('reports.employees') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="rounded-full bg-purple-200 p-3 mr-3 group-hover:bg-purple-300 transition-colors">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 group-hover:text-purple-800">Performance Atendentes</h4>
                                    <p class="text-sm text-gray-600">Métricas de produtividade</p>
                                </div>
                            </div>
                        </a>

                        <!-- Clientes Frequentes -->
                        <a href="{{ route('reports.customers') }}" class="group bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="rounded-full bg-pink-200 p-3 mr-3 group-hover:bg-pink-300 transition-colors">
                                    <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 group-hover:text-pink-800">Clientes Frequentes</h4>
                                    <p class="text-sm text-gray-600">Análise RFM de fidelização</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">📥 Exportar Dados</h3>
                        <p class="text-sm text-gray-500">Baixe os relatórios em formato CSV para análise externa</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export-csv', ['type' => 'dashboard']) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exportar Dashboard
                        </a>
                        <a href="{{ route('reports.export-csv', ['type' => 'products']) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exportar Produtos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js for future graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reports Dashboard loaded successfully');

    // Aqui podemos adicionar gráficos via Chart.js no futuro
    // Por enquanto mantemos simples e informativo
});
</script>
@endsection
