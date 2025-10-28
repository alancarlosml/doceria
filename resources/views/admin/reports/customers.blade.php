@extends('layouts.admin')

@section('title', 'Relatório - Clientes Frequentes - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">❤️</span>Relatório - Clientes Frequentes
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Análise RFM de fidelização e comportamento dos clientes.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <!-- Filtros de período -->
                    <form method="GET" action="{{ route('reports.customers') }}" class="flex space-x-2">
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

            <!-- Totais Gerais -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-pink-100 p-3">
                                <svg class="h-8 w-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Receita Total Clientes</dt>
                                <dd class="text-2xl font-semibold text-gray-900">R$ {{ number_format($totals['total_revenue'], 2, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Pedidos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($totals['total_orders']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

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
                                <dd class="text-2xl font-semibold text-gray-900">R$ {{ number_format($totals['avg_ticket'], 2, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Clientes Ativos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($totals['total_customers']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Clientes RFM -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Análise RFM de Clientes</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                ← Voltar ao Dashboard
                            </a>
                            <a href="{{ route('reports.export-csv', ['type' => 'customers']) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                📥 Exportar CSV
                            </a>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        <strong>RFM Analysis:</strong> Recência (R), Frequência (F), Monetário (M). Período: {{ \Carbon\Carbon::parse($period['start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period['end'])->format('d/m/Y') }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pedidos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total Gasto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Médio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Compra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dias Última</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segmento RFM</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($customers->count() > 0)
                                @foreach($customers as $customer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($customer->profile_photo)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $customer->profile_photo) }}" alt="{{ $customer->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $customer->email ?: $customer->phone ?: 'Sem contato' }}</div>
                                                    @if($customer->phone)
                                                        <div class="text-xs text-gray-400">{{ $customer->phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($customer->total_orders) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-green-600">R$ {{ number_format($customer->total_spent, 2, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">R$ {{ number_format($customer->avg_ticket, 2, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($customer->last_purchase)
                                                    {{ \Carbon\Carbon::parse($customer->last_purchase)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $daysSinceLast = $customer->recency ?? ($customer->last_purchase ? \Carbon\Carbon::parse($customer->last_purchase)->diffInDays(now()) : null);
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                                ($daysSinceLast <= 30) ? 'bg-green-100 text-green-800' :
                                                (($daysSinceLast <= 90) ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                            }}">
                                                @if($daysSinceLast !== null)
                                                    {{ $daysSinceLast }} dias
                                                @else
                                                    —
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $rfm = $customer->rfm_segment ?? '';
                                                $rfmClass = 'bg-gray-100 text-gray-800';
                                                $rfmDescription = '—';

                                                if ($rfm == 'AAA') {
                                                    $rfmClass = 'bg-green-100 text-green-800';
                                                    $rfmDescription = 'Cliente VIP';
                                                } elseif (str_contains($rfm, 'A')) {
                                                    $rfmClass = 'bg-blue-100 text-blue-800';
                                                    $rfmDescription = 'Cliente Ouro';
                                                } elseif (str_contains($rfm, 'B')) {
                                                    $rfmClass = 'bg-yellow-100 text-yellow-800';
                                                    $rfmDescription = 'Cliente Prata';
                                                } elseif (str_contains($rfm, 'C')) {
                                                    $rfmClass = 'bg-red-100 text-red-800';
                                                    $rfmDescription = 'Cliente Bronze';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rfmClass }}">
                                                {{ $rfm ?: '—' }}
                                            </span>
                                            @if($rfm)
                                                <div class="text-xs text-gray-500 mt-1">{{ $rfmDescription }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Linha de Totais -->
                                <tr class="bg-gray-50 font-semibold">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <strong>TOTAIS DO PERÍODO</strong>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($totals['total_orders']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        R$ {{ number_format($totals['total_revenue'], 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        R$ {{ number_format($totals['avg_ticket'], 2, ',', '.') }}
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Nenhum cliente encontrado no período selecionado.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Exibindo {{ $customers->firstItem() ?? 0 }} a {{ $customers->lastItem() ?? 0 }} de {{ $customers->total() }} clientes
                        </div>
                        <div class="flex space-x-2">
                            @if ($customers->hasPages())
                                {{ $customers->appends(request()->query())->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segmentos RFM -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">📊 Distribuição de Segmentos RFM</h3>
                    <div class="text-sm text-gray-600">
                        Classificação baseada nos últimos 90 dias
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 rounded-lg " style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="text-white">
                            <div class="text-2xl font-bold">{{ $totals['rfm_counts']['vip'] }}</div>
                            <div class="text-sm opacity-90">Clientes VIP</div>
                            <div class="text-xs mt-1 opacity-75">R: Recente, F: Frequente, M: Alto Valor</div>
                        </div>
                    </div>

                    <div class="text-center p-4 rounded-lg " style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <div class="text-white">
                            <div class="text-2xl font-bold">{{ $totals['rfm_counts']['gold'] }}</div>
                            <div class="text-sm opacity-90">Clientes Ouro</div>
                            <div class="text-xs mt-1 opacity-75">Clientes bons</div>
                        </div>
                    </div>

                    <div class="text-center p-4 rounded-lg " style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="text-white">
                            <div class="text-2xl font-bold">{{ $totals['rfm_counts']['silver'] }}</div>
                            <div class="text-sm opacity-90">Clientes Prata</div>
                            <div class="text-xs mt-1 opacity-75">Potencial crescimento</div>
                        </div>
                    </div>

                    <div class="text-center p-4 rounded-lg " style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <div class="text-white">
                            <div class="text-2xl font-bold">{{ $totals['rfm_counts']['bronze'] }}</div>
                            <div class="text-sm opacity-90">Clientes Bronze</div>
                            <div class="text-xs mt-1 opacity-75">Requer atenção</div>
                        </div>
                    </div>
                </div>

                <!-- Legenda -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Como interpretar RFM:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <strong>R (Recência):</strong> Dias desde a última compra<br>
                            A = ≤ 30 dias | B = 31-90 dias | C = > 90 dias
                        </div>
                        <div>
                            <strong>F (Frequência):</strong> Número total de pedidos<br>
                            A = ≥ 10 pedidos | B = 5-9 pedidos | C = < 5 pedidos
                        </div>
                        <div>
                            <strong>M (Monetário):</strong> Valor total gasto<br>
                            A = ≥ R$ 500 | B = R$ 200-499 | C = < R$ 200
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Customers Report loaded successfully');
});
</script>
@endsection
