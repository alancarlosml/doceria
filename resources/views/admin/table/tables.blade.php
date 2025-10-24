@extends('layouts.admin')

@section('title', 'Mesas - Doce Doce Brigaderia')

@section('admin-content')
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="md:flex-1 min-w-0">
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                        <span class="mr-3">🪑</span>
                        Mesas
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Gerencie as mesas do estabelecimento
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('tables.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="mr-2">➕</span>
                        Nova Mesa
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Total de Mesas -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-6 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 truncate">
                                Total de Mesas
                            </p>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ $tables->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <span class="text-blue-600">🪑</span>
                        </div>
                    </div>
                </div>

                <!-- Mesas Disponíveis -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-6 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 truncate">
                                Disponíveis
                            </p>
                            <p class="text-2xl font-bold text-green-700">
                                {{ $tables->where('status', 'disponivel')->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <span class="text-green-600">✅</span>
                        </div>
                    </div>
                </div>

                <!-- Mesas Ocupadas -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 p-6 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600 truncate">
                                Ocupadas
                            </p>
                            <p class="text-2xl font-bold text-red-700">
                                {{ $tables->where('status', 'ocupada')->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <span class="text-red-600">🟠</span>
                        </div>
                    </div>
                </div>

                <!-- Mesas Reservadas -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-6 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600 truncate">
                                Reservadas
                            </p>
                            <p class="text-2xl font-bold text-yellow-700">
                                {{ $tables->where('status', 'reservada')->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <span class="text-yellow-600">📅</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:p-6">
                    @if($tables->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($tables as $table)
                        <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <!-- Header da Mesa -->
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">Mesa {{ $table->number }}</h3>
                                    <div class="flex items-center space-x-2">
                                        <!-- Status Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($table->status === 'disponivel') bg-green-100 text-green-800
                                            @elseif($table->status === 'ocupada') bg-red-100 text-red-800
                                            @elseif($table->status === 'reservada') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($table->status === 'disponivel') Disponível
                                            @elseif($table->status === 'ocupada') Ocupada
                                            @elseif($table->status === 'reservada') Reservada
                                            @else {{ ucfirst($table->status) }}
                                            @endif
                                        </span>

                                        <!-- Active Status -->
                                        @if($table->active)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700">
                                                ✅ Ativa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                ❌ Inativa
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Capacity -->
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">{{ $table->capacity }}</span> pessoas
                                </div>
                            </div>

                            <!-- Mesa Preview -->
                            <div class="p-4">
                                <div class="aspect-square bg-gray-50 rounded-lg flex items-center justify-center mb-4">
                                    <div class="text-center">
                                        <div class="text-4xl mb-2">🪑</div>
                                        <div class="text-sm font-medium text-gray-700">{{ $table->number }}</div>
                                        <div class="text-xs text-gray-500">{{ $table->capacity }} pessoas</div>
                                    </div>
                                </div>

                                <!-- Venda Atual (se existir) -->
                                @if($table->status === 'ocupada')
                                    @php $currentSale = $table->sales()->where('status', '!=', 'finalizado')->where('status', '!=', 'cancelado')->first(); @endphp
                                    @if($currentSale)
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-red-800">📋 Pedido #{{ $currentSale->id }}</span>
                                            <span class="text-xs text-red-600 capitalize">{{ $currentSale->status }}</span>
                                        </div>

                                        @if($currentSale->customer)
                                        <div class="text-xs text-red-700 mb-2">
                                            👤 {{ $currentSale->customer->name }}
                                        </div>
                                        @endif

                                        <div class="text-sm font-semibold text-red-800">
                                            R$ {{ number_format($currentSale->total, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="px-4 pb-4">
                                <div class="flex items-center justify-between space-x-3">
                                    <a href="{{ route('tables.show', $table) }}"
                                       class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        👁️ Detalhes
                                    </a>
                                    <a href="{{ route('tables.edit', $table) }}"
                                       class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        ✏️ Editar
                                    </a>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-3 flex items-center justify-between">
                                    <form method="POST" action="{{ route('tables.toggle-status', $table) }}" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="text-xs px-2 py-1 rounded {{ $table->active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}"
                                                onclick="return confirm('{{ $table->active ? 'Desativar' : 'Ativar' }} mesa {{ $table->number }}?')">
                                            {{ $table->active ? '❌ Desativar' : '✅ Ativar' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('tables.destroy', $table) }}" class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir a mesa {{ $table->number }}? Esta ação não pode ser desfeita!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">
                                            🗑️ Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🪑</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma mesa cadastrada</h3>
                        <p class="text-gray-500 mb-6">Comece adicionando as mesas do seu estabelecimento.</p>
                        <a href="{{ route('tables.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <span class="mr-2">➕</span>
                            Criar Primeira Mesa
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
