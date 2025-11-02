@extends('layouts.admin')

@section('title', 'Mesa ' . $table->number . ' - Doce Doce Brigaderia')

@section('admin-content')
<main class="flex-1 relative overflow-y-auto focus:outline-none" x-data="tableActions()">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="md:flex-1 min-w-0">
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                        <span class="mr-3">🪑</span>
                        Mesa {{ $table->number }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Detalhes e histórico da mesa
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <a href="{{ route('tables.edit', $table) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <span class="mr-2">✏️</span>
                        Editar
                    </a>
                    <a href="{{ route('tables.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <span class="mr-2">⬅️</span>
                        Voltar
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Mesa Status Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white">Status Atual</h3>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Mesa Visual -->
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full mb-4">
                                    <span class="text-4xl">🪑</span>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">Mesa {{ $table->number }}</h4>
                                <p class="text-gray-600">{{ $table->capacity }} {{ $table->capacity === 1 ? 'pessoa' : 'pessoas' }}</p>
                            </div>

                            <!-- Status Badge -->
                            <div class="text-center mb-6">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                                    @if($table->status === 'disponivel') bg-green-100 text-green-800
                                    @elseif($table->status === 'ocupada') bg-red-100 text-red-800
                                    @elseif($table->status === 'reservada') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($table->status === 'disponivel')
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        Disponível
                                    @elseif($table->status === 'ocupada')
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        Ocupada
                                    @elseif($table->status === 'reservada')
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                        Reservada
                                    @else
                                        {{ ucfirst($table->status) }}
                                    @endif
                                </span>

                                @if($table->active)
                                    <p class="text-xs text-green-600 mt-2">✅ Mesa ativa</p>
                                @else
                                    <p class="text-xs text-red-600 mt-2">❌ Mesa inativa</p>
                                @endif
                            </div>

                            <!-- Quick Stats -->
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Criada em:</span>
                                    <span class="text-sm font-medium">{{ $table->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Última atualização:</span>
                                    <span class="text-sm font-medium">{{ $table->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <!-- Current Sale Info -->
                            @if($currentSale)
                            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <h5 class="font-medium text-red-800 mb-2">📋 Pedido Atual #{{ $currentSale->id }}</h5>
                                <div class="space-y-1 text-sm text-red-700">
                                    @if($currentSale->customer)
                                    <p>👤 {{ $currentSale->customer->name }}</p>
                                    @endif
                                    <p>💰 R$ {{ number_format($currentSale->total, 2, ',', '.') }}</p>
                                    <p>🕒 {{ $currentSale->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats & Sales History -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600">Pedidos Hoje</p>
                                    <p class="text-2xl font-bold text-green-700">{{ $table->sales()->whereDate('created_at', now()->format('Y-m-d'))->count() }}</p>
                                </div>
                                <div class="p-2 bg-green-100 rounded-full">
                                    <span class="text-green-600">📊</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Total de Pedidos</p>
                                    <p class="text-2xl font-bold text-blue-700">{{ $table->sales()->count() }}</p>
                                </div>
                                <div class="p-2 bg-blue-100 rounded-full">
                                    <span class="text-blue-600">🛒</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-400 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-600">Faturamento Total</p>
                                    <p class="text-2xl font-bold text-purple-700">R$ {{ number_format($table->sales()->sum('total'), 2, ',', '.') }}</p>
                                </div>
                                <div class="p-2 bg-purple-100 rounded-full">
                                    <span class="text-purple-600">💰</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales History -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-500 to-gray-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <span class="mr-3">📈</span>
                                Histórico de Pedidos
                            </h3>
                        </div>

                        <div class="p-6">
                            @php $sales = $table->sales()->with('customer')->orderBy('created_at', 'desc')->take(10)->get(); @endphp

                            @if($sales->count() > 0)
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($sales as $sale)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <span class="font-semibold text-gray-900">#{{ $sale->id }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($sale->status === 'finalizado') bg-green-100 text-green-800
                                                @elseif($sale->status === 'cancelado') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>

                                    @if($sale->customer)
                                    <div class="text-sm text-gray-600 mb-2">
                                        👤 {{ $sale->customer->name }}
                                    </div>
                                    @endif

                                    <!-- Items Preview -->
                                    @if($sale->items->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($sale->items->take(3) as $item)
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                            {{ $item->quantity }}x {{ $item->product->name }}
                                        </span>
                                        @endforeach
                                        @if($sale->items->count() > 3)
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">
                                            +{{ $sale->items->count() - 3 }} itens
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('sales.index') }}?table={{ $table->id }}"
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Ver todos os pedidos desta mesa →
                                </a>
                            </div>
                            @else
                            <div class="text-center py-12">
                                <div class="text-6xl mb-4">📋</div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h4>
                                <p class="text-gray-500">Esta mesa ainda não foi utilizada para nenhum pedido.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="mr-3">⚡</span>
                            Ações Rápidas
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <a href="{{ route('sales.pos') }}"
                               class="text-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="text-2xl mb-1">🛒</div>
                                <div class="text-sm font-medium">Novo Pedido</div>
                            </a>

                            @if($currentSale && $table->status === 'ocupada' && (Auth::user()->hasRole('admin') || Auth::user()->hasPermission('tables.change')))
                            <button @click="showChangeTableModal = true"
                                    class="text-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="text-2xl mb-1">🔄</div>
                                <div class="text-sm font-medium">Mudar Mesa</div>
                            </button>
                            @endif

                            @if($table->status !== 'reservada')
                            <form action="{{ route('tables.update-status', $table) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="reservada">
                                <button type="submit"
                                        class="w-full text-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                        onclick="return confirm('Marcar mesa como reservada?')">
                                    <div class="text-2xl mb-1">📅</div>
                                    <div class="text-sm font-medium">Reservar</div>
                                </button>
                            </form>
                            @endif

                            @if($table->status !== 'disponivel')
                            <form action="{{ route('tables.update-status', $table) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="disponivel">
                                <button type="submit"
                                        class="w-full text-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                        onclick="return confirm('Liberar mesa?')">
                                    <div class="text-2xl mb-1">✅</div>
                                    <div class="text-sm font-medium">Liberar</div>
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('tables.toggle-status', $table) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                        class="w-full text-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                        onclick="return confirm('{{ $table->active ? 'Inativar' : 'Ativar' }} mesa?')">
                                    <div class="text-2xl mb-1">{{ $table->active ? '❌' : '✅' }}</div>
                                    <div class="text-sm font-medium">{{ $table->active ? 'Inativar' : 'Ativar' }}</div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Mudança de Mesa -->
    @if($currentSale && $table->status === 'ocupada' && (Auth::user()->hasRole('admin') || Auth::user()->hasPermission('tables.change')))
    <div x-show="showChangeTableModal"
         x-cloak
         @click.away="showChangeTableModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 relative">
            <!-- Botão X para fechar -->
            <button
                @click="showChangeTableModal = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center"
                title="Fechar"
            >
                ✕
            </button>

            <h3 class="text-2xl font-bold mb-4 pr-8">🔄 Mudar Mesa</h3>
            <p class="text-gray-600 mb-4">Mova o cliente da <strong>Mesa {{ $table->number }}</strong> para outra mesa disponível.</p>

            <form @submit.prevent="changeTable">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selecione a Mesa de Destino <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-model="newTableId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Selecione uma mesa...</option>
                        @php
                            $availableTables = \App\Models\Table::where('active', true)
                                ->where('status', 'disponivel')
                                ->where('id', '!=', $table->id)
                                ->orderBy('number')
                                ->get();
                        @endphp
                        @foreach($availableTables as $availableTable)
                        <option value="{{ $availableTable->id }}">
                            Mesa {{ $availableTable->number }} ({{ $availableTable->capacity }} pessoas)
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="showChangeTableModal = false"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="!newTableId || isChanging"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!isChanging">Confirmar Mudança</span>
                        <span x-show="isChanging">Mudando...</span>
                    </button>
                </div>

                <div x-show="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <div x-show="successMessage" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm" x-text="successMessage"></div>
            </form>
        </div>
    </div>
    @endif
</main>

<script>
function tableActions() {
    return {
        showChangeTableModal: false,
        newTableId: '',
        isChanging: false,
        errorMessage: '',
        successMessage: '',

        async changeTable() {
            if (!this.newTableId) {
                this.errorMessage = 'Selecione uma mesa de destino!';
                return;
            }

            this.isChanging = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const response = await fetch('{{ route("tables.change-table", $table) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        new_table_id: this.newTableId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.successMessage = data.message || 'Mesa alterada com sucesso!';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.errorMessage = data.message || 'Erro ao mudar mesa!';
                }
            } catch (error) {
                this.errorMessage = 'Erro ao comunicar com o servidor: ' + error.message;
            } finally {
                this.isChanging = false;
            }
        }
    }
}
</script>
<style>
[x-cloak] { display: none !important; }
</style>
@endsection
