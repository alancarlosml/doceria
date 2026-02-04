@extends('layouts.admin')

@section('title', 'Dashboard - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main Dashboard Content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Welcome Section -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-display font-semibold text-gray-900 mb-2">
                    👋 Controle Operacional
                </h1>
                <p class="mt-2 text-gray-600 text-base md:text-lg">
                    Gerencie pedidos pendentes, mesas ocupadas e entregas em tempo real.
                </p>
            </div>

            <!-- Operational Stats -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 md:mb-8">
                <!-- Vendas do Dia -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4 md:p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-green-600 truncate">
                                Vendas Hoje
                            </p>
                            <p class="text-xl md:text-2xl font-bold text-green-700 truncate">
                                R$ {{ number_format($todaySales, 2, ',', '.') }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">
                                {{ $todaySalesCount }} pedidos
                            </p>
                        </div>
                        <div class="p-2 md:p-3 bg-green-100 rounded-full flex-shrink-0 ml-2">
                            <span class="text-green-600 text-lg md:text-xl">💰</span>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Pendentes -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 md:p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-yellow-600 truncate">
                                Pedidos Pendentes
                            </p>
                            <p class="text-xl md:text-2xl font-bold text-yellow-700">
                                {{ $pendingSalesCount }}
                            </p>
                            <p class="text-xs text-yellow-600 mt-1">
                                Aguardando produção
                            </p>
                        </div>
                        <div class="p-2 md:p-3 bg-yellow-100 rounded-full flex-shrink-0 ml-2">
                            <span class="text-yellow-600 text-lg md:text-xl">⏳</span>
                        </div>
                    </div>
                </div>

                <!-- Mesas Ocupadas -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-4 md:p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-blue-600 truncate">
                                Mesas Ocupadas
                            </p>
                            <p class="text-xl md:text-2xl font-bold text-blue-700">
                                {{ $occupiedTablesCount }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Com pedidos ativos
                            </p>
                        </div>
                        <div class="p-2 md:p-3 bg-blue-100 rounded-full flex-shrink-0 ml-2">
                            <span class="text-blue-600 text-lg md:text-xl">🪑</span>
                        </div>
                    </div>
                </div>

                <!-- Encomendas Hoje -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-400 p-4 md:p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-purple-600 truncate">
                                Encomendas Hoje
                            </p>
                            <p class="text-xl md:text-2xl font-bold text-purple-700 truncate">
                                R$ {{ number_format($todayEncomendasTotal, 2, ',', '.') }}
                            </p>
                            <p class="text-xs text-purple-600 mt-1">
                                {{ $todayEncomendasCount }} finalizadas
                            </p>
                        </div>
                        <div class="p-2 md:p-3 bg-purple-100 rounded-full flex-shrink-0 ml-2">
                            <span class="text-purple-600 text-lg md:text-xl">📦</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estoque Baixo Alert -->
            @if($lowStockItems->count() > 0 || $criticalStockItems > 0)
            <div class="mb-8 {{ $criticalStockItems > 0 ? 'bg-red-50 border-l-4 border-red-400' : 'bg-yellow-50 border-l-4 border-yellow-400' }} p-6 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold {{ $criticalStockItems > 0 ? 'text-red-800' : 'text-yellow-800' }} mb-2">
                            {{ $criticalStockItems > 0 ? '🚨' : '⚠️' }} Atenção: Estoque Baixo
                        </h3>
                        <p class="text-sm {{ $criticalStockItems > 0 ? 'text-red-700' : 'text-yellow-700' }} mb-4">
                            {{ $criticalStockItems > 0 ? 'Existem itens com estoque crítico que precisam de reposição urgente!' : 'Alguns insumos estão com estoque baixo e precisam ser repostos.' }}
                        </p>
                        <div class="space-y-2">
                            @foreach($lowStockItems as $item)
                            <div class="flex items-center justify-between bg-white rounded p-3 {{ $item->isCriticalStock() ? 'border-2 border-red-300' : 'border border-yellow-300' }}">
                                <div class="flex-1">
                                    <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                    <span class="text-sm text-gray-600 ml-2">
                                        ({{ number_format($item->current_quantity, 2, ',', '.') }} {{ $item->unit }} / Mín: {{ number_format($item->min_quantity, 2, ',', '.') }} {{ $item->unit }})
                                    </span>
                                </div>
                                @if($item->isCriticalStock())
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Crítico</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Baixo</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if($lowStockItems->count() >= 5)
                        <p class="text-xs {{ $criticalStockItems > 0 ? 'text-red-600' : 'text-yellow-600' }} mt-2">
                            E mais {{ $lowStockItems->count() - 5 }} item(ns) com estoque baixo...
                        </p>
                        @endif
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('inventory.index', ['low_stock' => 1]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $criticalStockItems > 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $criticalStockItems > 0 ? 'red' : 'yellow' }}-500">
                            Ver Estoque
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Operational Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">

                <!-- Pedidos Pendentes -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                        <h3 class="text-lg font-display font-semibold text-white flex items-center">
                            <span class="mr-3">📋</span>
                            Pedidos Pendentes ({{ $pendingSalesCount }})
                        </h3>
                        <p class="text-yellow-100 text-sm">Aguardando produção ou mesa</p>
                    </div>
                    <div class="p-4 md:p-6">
                        @if($pendingSales->count() > 0)
                        <div class="space-y-3 md:space-y-4 max-h-96 overflow-y-auto">
                            @foreach($pendingSales as $sale)
                            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50 hover:bg-yellow-100 transition-colors cursor-pointer"
                                 onclick="window.location.href='{{ route('sales.pos') }}'">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-800">#{{ $sale->id }}</span>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $sale->status === \App\Enums\SaleStatus::PENDENTE ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $sale->status === \App\Enums\SaleStatus::EM_PREPARO ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $sale->status === \App\Enums\SaleStatus::PRONTO ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ $sale->status->label() }}
                                    </span>
                                </div>

                                <!-- Tipo e Cliente -->
                                <div class="text-sm text-gray-600 mb-2">
                                    @if($sale->type === 'balcao')
                                        🏪 Balcão
                                    @elseif($sale->type === 'delivery')
                                        🏍️ Delivery
                                    @else
                                        📦 Encomenda
                                    @endif

                                    @if($sale->customer)
                                        • {{ $sale->customer->name }}
                                    @endif

                                    @if($sale->table)
                                        • Mesa {{ $sale->table->number }}
                                    @endif
                                </div>

                                <!-- Produtos -->
                                <div class="space-y-1">
                                    @foreach($sale->items->take(2) as $item)
                                    <div class="text-xs text-gray-500">
                                        • {{ $item->quantity }}x {{ $item->product->name }}
                                    </div>
                                    @endforeach
                                    @if($sale->items->count() > 2)
                                    <div class="text-xs text-gray-400">
                                        + {{ $sale->items->count() - 2 }} itens...
                                    </div>
                                    @endif
                                </div>

                                <div class="mt-3 pt-2 border-t border-yellow-200">
                                    <span class="font-semibold text-green-600">
                                        R$ {{ number_format($sale->total, 2, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        {{ $sale->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-3">📋</div>
                            <p>Nenhum pedido pendente</p>
                            <p class="text-sm">Muito bem! Todos os pedidos estão em andamento.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Mesas Ocupadas -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                        <h3 class="text-lg font-display font-semibold text-white flex items-center">
                            <span class="mr-3">🪑</span>
                            Mesas Ocupadas ({{ $occupiedTablesCount }})
                        </h3>
                        <p class="text-blue-100 text-sm">Mesas com pedidos ativos</p>
                    </div>
                    <div class="p-4 md:p-6">
                        @if($occupiedTables->count() > 0)
                        <div class="space-y-3 md:space-y-4 max-h-96 overflow-y-auto">
                            @foreach($occupiedTables as $table)
                            @php
                                $activeSale = $table->sales->first();
                            @endphp
                            <div class="border border-blue-200 rounded-lg p-4 bg-blue-50 hover:bg-blue-100 transition-colors cursor-pointer"
                                 onclick="window.location.href='{{ route('sales.pos') }}'">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-semibold text-gray-800">Mesa {{ $table->number }}</span>
                                    <span class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full capitalize">
                                        {{ $activeSale ? $activeSale->status->label() : 'Disponível' }}
                                    </span>
                                </div>

                                @if($activeSale && $activeSale->customer)
                                <div class="text-sm text-gray-600 mb-3">
                                    👤 {{ $activeSale->customer->name }}
                                </div>
                                @endif

                                @if($activeSale)
                                <div class="text-sm font-medium text-gray-700 mb-2">
                                    📋 Pedido #{{ $activeSale->id }}
                                </div>

                                <!-- Últimos produtos do pedido -->
                                <div class="space-y-1 mb-3">
                                    @foreach($activeSale->items->take(2) as $item)
                                    <div class="text-xs text-gray-500">
                                        • {{ $item->quantity }}x {{ $item->product->name }}
                                    </div>
                                    @endforeach
                                    @if($activeSale->items->count() > 2)
                                    <div class="text-xs text-gray-400">
                                        + {{ $activeSale->items->count() - 2 }} itens...
                                    </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-blue-200">
                                    <span class="font-semibold text-green-600">
                                        R$ {{ number_format($activeSale->total, 2, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        {{ $activeSale->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-3">🪑</div>
                            <p>Todas as mesas livres!</p>
                            <p class="text-sm">Aguardando novos clientes.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Entregas e Agendamentos -->
                <div class="space-y-6">
                    <!-- Entregas em Andamento -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                            <h3 class="text-lg font-display font-semibold text-white flex items-center">
                                <span class="mr-3">🏍️</span>
                                Em Entrega ({{ $upcomingDeliveriesCount }})
                            </h3>
                            <p class="text-purple-100 text-sm">Pedidos já estão a caminho</p>
                        </div>
                        <div class="p-6">
                            @if($upcomingDeliveries->count() > 0)
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @foreach($upcomingDeliveries as $delivery)
                                <div class="border border-purple-200 rounded-lg p-3 bg-purple-50 hover:bg-purple-100 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-semibold text-gray-800 text-sm">#{{ $delivery->id }}</span>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $delivery->status === \App\Enums\SaleStatus::PRONTO ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $delivery->status->label() }}
                                        </span>
                                    </div>

                                    @if($delivery->customer)
                                    <div class="text-sm text-gray-600 mb-1">
                                        👤 {{ $delivery->customer->name }}
                                    </div>
                                    @endif

                                    @if($delivery->motoboy)
                                    <div class="text-sm text-gray-600 mb-1">
                                        🏍️ {{ $delivery->motoboy->name }}
                                    </div>
                                    @endif

                                    <div class="text-xs text-gray-500">
                                        📍 {{ $delivery->delivery_address ?? 'Endereço pendente' }}
                                    </div>

                                    <div class="mt-2 pt-2 border-t border-purple-200">
                                        <span class="font-semibold text-green-600 text-sm">
                                            R$ {{ number_format($delivery->total, 2, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-2">
                                            {{ $delivery->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-6 text-gray-500">
                                <div class="text-3xl mb-2">🏍️</div>
                                <p class="text-sm">Nenhuma entrega em andamento</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Encomendas Próximas -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                            <h3 class="text-lg font-display font-semibold text-white flex items-center">
                                <span class="mr-3">📅</span>
                                Encomendas ({{ $pendingEncomendasCount }})
                            </h3>
                            <p class="text-orange-100 text-sm">Agendadas para retirada</p>
                        </div>
                        <div class="p-6">
                            @if($pendingEncomendas->count() > 0)
                            <div class="space-y-3 max-h-48 overflow-y-auto">
                                @foreach($pendingEncomendas->sortBy('delivery_date') as $encomenda)
                                <div class="border border-orange-200 rounded-lg p-3 bg-orange-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-semibold text-gray-800 text-sm">#{{ $encomenda->id }}</span>
                                        <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full capitalize">
                                            {{ $encomenda instanceof \App\Models\Sale ? $encomenda->status->label() : ($encomenda->status_label ?? ucfirst($encomenda->status)) }}
                                        </span>
                                    </div>

                                    @if($encomenda->customer)
                                    <div class="text-sm text-gray-600 mb-1">
                                        👤 {{ $encomenda->customer->name }}
                                    </div>
                                    @endif

                                    <div class="text-sm text-gray-700 font-medium mb-1">
                                        📅 {{ $encomenda->delivery_date ? $encomenda->delivery_date->format('d/m') : 'Data não definida' }}
                                        @if($encomenda->delivery_time)
                                        às {{ is_string($encomenda->delivery_time) ? \Carbon\Carbon::parse($encomenda->delivery_time)->format('H:i') : $encomenda->delivery_time->format('H:i') }}
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-orange-200">
                                        <span class="font-semibold text-green-600 text-sm">
                                            R$ {{ number_format($encomenda->total, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-6 text-gray-500">
                                <div class="text-3xl mb-2">📅</div>
                                <p class="text-sm">Nenhuma encomenda agendada</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                <!-- Ações Rápidas -->
                <div class="bg-white shadow-lg rounded-lg p-4 md:p-6 hover:shadow-xl transition-shadow duration-300">
                    <h3 class="text-base md:text-lg font-display font-medium text-gray-900 mb-3 md:mb-4 flex items-center">
                        <span class="mr-2">🚀</span>
                        Ações Rápidas
                    </h3>
                    <div class="grid grid-cols-2 gap-2 md:gap-3">
                        <a href="{{ route('sales.pos') }}" class="flex items-center justify-center px-3 md:px-4 py-2 md:py-3 border border-transparent rounded-md shadow-sm text-xs md:text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            <span class="mr-1 md:mr-2">🛒</span>
                            <span class="hidden sm:inline">Novo Pedido</span>
                            <span class="sm:hidden">Pedido</span>
                        </a>

                        <a href="{{ route('encomendas.create') }}" class="flex items-center justify-center px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <span class="mr-1 md:mr-2">📝</span>
                            <span class="hidden sm:inline">Nova Encomenda</span>
                            <span class="sm:hidden">Encomenda</span>
                        </a>

                        <a href="{{ route('tables.index') }}" class="flex items-center justify-center px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <span class="mr-1 md:mr-2">🪑</span>
                            <span class="hidden sm:inline">Gerenciar Mesas</span>
                            <span class="sm:hidden">Mesas</span>
                        </a>

                        <a href="{{ route('motoboys.index') }}" class="flex items-center justify-center px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <span class="mr-1 md:mr-2">🏍️</span>
                            <span class="hidden sm:inline">Ver Motoboys</span>
                            <span class="sm:hidden">Motoboys</span>
                        </a>

                        <a href="{{ route('cash-registers.index') }}" class="flex items-center justify-center px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <span class="mr-1 md:mr-2">💰</span>
                            <span class="hidden sm:inline">Controle Caixas</span>
                            <span class="sm:hidden">Caixas</span>
                        </a>

                        <a href="{{ route('encomendas.index') }}" class="flex items-center justify-center px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <span class="mr-1 md:mr-2">📦</span>
                            <span class="hidden sm:inline">Encomendas</span>
                            <span class="sm:hidden">Lista</span>
                        </a>
                    </div>
                </div>

                <!-- Últimas Vendas Concluídas -->
                <div class="bg-white shadow-lg rounded-lg p-4 md:p-6 hover:shadow-xl transition-shadow duration-300">
                    <h3 class="text-base md:text-lg font-display font-medium text-gray-900 mb-3 md:mb-4 flex items-center">
                        <span class="mr-2">📈</span>
                        Últimas Vendas
                    </h3>
                    <div class="space-y-2 md:space-y-3">
                        @forelse($recentSales as $sale)
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center flex-1">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm">
                                        @if($sale->type === 'delivery') 🏍️
                                        @elseif($sale->type === 'balcao') 🏪
                                        @else 📦
                                        @endif
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">#{{ $sale->id }}</p>
                                    <p class="text-sm text-gray-500 truncate">
                                        @if($sale->customer)
                                            {{ $sale->customer->name }}
                                        @else
                                            Cliente não identificado
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-semibold text-green-600">
                                    R$ {{ number_format($sale->total, 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $sale->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6 text-gray-500">
                            <div class="text-3xl mb-2">📊</div>
                            <p class="text-sm">Nenhuma venda recente</p>
                            <p class="text-xs">As vendas aparecerão aqui após serem concluídas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
