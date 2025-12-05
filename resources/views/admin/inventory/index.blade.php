@extends('layouts.admin')

@section('title', 'Controle de Estoque - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none" x-data="inventoryManager()">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        📦 Controle de Estoque
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie os insumos da doceria e atualize as quantidades rapidamente.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('inventory.inspection') }}" class="inline-flex items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Fazer Vistoria
                    </a>
                    <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Novo Insumo
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <span class="text-white text-2xl">📦</span>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Insumos</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalItems }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <span class="text-white text-2xl">⚠️</span>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Estoque Baixo</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $lowStockItems }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <span class="text-white text-2xl">🚨</span>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Estoque Crítico</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $criticalStockItems }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Input -->
            <div class="bg-white shadow rounded-lg mb-6 p-4">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="window.inventorySearchQuery = $event.target.value"
                    placeholder="🔍 Buscar insumos..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                >
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg mb-6 p-4">
                <div class="flex flex-wrap gap-3 items-center">
                    <span class="text-sm font-medium text-gray-700">Filtros:</span>
                    <a
                        href="{{ route('inventory.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request()->input('low_stock') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        Todos
                    </a>
                    <a
                        href="{{ route('inventory.index', ['low_stock' => 1]) }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->input('low_stock') == '1' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        ⚠️ Estoque Baixo
                    </a>
                    <span class="text-sm text-gray-500 ml-auto">
                        Mostrando {{ $items->count() }} de {{ $items->total() }} itens
                    </span>
                </div>
            </div>

            <!-- Inventory Items Grid -->
            @if($items->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($items as $item)
                        @php
                            $isLow = $item->isLowStock();
                            $isCritical = $item->isCriticalStock();
                            $stockClass = $isCritical ? 'border-red-300 bg-red-50' : ($isLow ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200');
                        @endphp
                        <div 
                            x-data="inventoryItem({{ $item->id }}, {{ $item->current_quantity }}, {{ $item->min_quantity }}, '{{ $item->unit }}', {{ $isLow ? 'true' : 'false' }}, {{ $isCritical ? 'true' : 'false' }}, '{{ addslashes($item->name) }}')"
                            x-show="matchesSearch()"
                            class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border-2 {{ $stockClass }}"
                        >
                            <!-- Header -->
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-sm font-bold text-gray-900 line-clamp-2 flex-1">
                                        {{ $item->name }}
                                    </h3>
                                    <div class="ml-2 flex-shrink-0">
                                        @if($isCritical)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">🚨</span>
                                        @elseif($isLow)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">⚠️</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✅</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Mín: {{ number_format($item->min_quantity, 2, ',', '.') }} {{ $item->unit }}
                                </p>
                            </div>

                            <!-- Quantity Section -->
                            <div class="p-4">
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Quantidade Atual
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            x-model="currentQuantity"
                                            @blur="updateQuantity()"
                                            @keyup.enter="updateQuantity()"
                                            :class="isUpdating ? 'opacity-50 cursor-wait' : ''"
                                            class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                            :disabled="isUpdating"
                                        >
                                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ $item->unit }}</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            :class="isCritical ? 'bg-red-600' : (isLow ? 'bg-yellow-600' : 'bg-green-600')"
                                            class="h-2 rounded-full transition-all duration-300"
                                            :style="'width: ' + Math.min(100, (currentQuantity / minQuantity) * 100) + '%'"
                                        ></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 text-center">
                                        <span x-text="Math.round((currentQuantity / minQuantity) * 100)"></span>% do mínimo
                                    </p>
                                </div>

                                <!-- Status Message -->
                                <div x-show="isCritical || isLow" class="mb-3">
                                    <p class="text-xs font-medium" :class="isCritical ? 'text-red-700' : 'text-yellow-700'">
                                        <span x-show="isCritical">🚨 Estoque crítico! Reposição urgente necessária.</span>
                                        <span x-show="!isCritical && isLow">⚠️ Estoque abaixo do mínimo.</span>
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a
                                        href="{{ route('inventory.show', $item) }}"
                                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-green-600 rounded-lg text-xs font-medium text-green-600 hover:bg-green-50 transition-colors"
                                        title="Ver detalhes"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver
                                    </a>

                                    <a
                                        href="{{ route('inventory.edit', $item) }}"
                                        class="flex-1 inline-flex items-center justify-center px-2 py-1.5 border border-blue-600 rounded-lg text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors"
                                        title="Editar completo"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </a>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    @if($item->lastUpdatedBy)
                                        Atualizado por {{ $item->lastUpdatedBy->name }}
                                        <br>
                                        <span class="text-gray-400">{{ $item->updated_at->diffForHumans() }}</span>
                                    @else
                                        <span class="text-gray-400">Nunca atualizado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $items->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum insumo cadastrado</h3>
                    <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro insumo.</p>
                    <div class="mt-6">
                        <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Novo Insumo
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Alpine.js Inventory Manager -->
<script>
// Global search variable
window.inventorySearchQuery = '';

function inventoryManager() {
    return {
        searchQuery: '',
        
        init() {
            // Sync with global variable
            this.$watch('searchQuery', value => {
                window.inventorySearchQuery = value;
                // Dispatch event to update all cards
                window.dispatchEvent(new CustomEvent('inventory-search-updated', { detail: value }));
            });
        }
    }
}

function inventoryItem(itemId, initialQuantity, minQuantity, unit, isLow, isCritical, itemName) {
    return {
        itemId: itemId,
        currentQuantity: initialQuantity,
        minQuantity: minQuantity,
        unit: unit,
        isLow: isLow,
        isCritical: isCritical,
        isUpdating: false,
        originalQuantity: initialQuantity,
        itemName: itemName,
        searchQuery: window.inventorySearchQuery || '',

        init() {
            // Listen for search updates
            const updateSearch = (e) => {
                this.searchQuery = e.detail || '';
            };
            window.addEventListener('inventory-search-updated', updateSearch);
            
            // Cleanup on destroy
            this.$el.addEventListener('alpine:destroy', () => {
                window.removeEventListener('inventory-search-updated', updateSearch);
            });
        },

        matchesSearch() {
            const query = this.searchQuery || window.inventorySearchQuery || '';
            if (!query) return true;
            return this.itemName.toLowerCase().includes(query.toLowerCase());
        },

        async updateQuantity() {
            // Don't update if value hasn't changed
            if (this.currentQuantity == this.originalQuantity) {
                return;
            }

            // Validate
            if (this.currentQuantity < 0) {
                this.showToast('Quantidade não pode ser negativa', 'error');
                this.currentQuantity = this.originalQuantity;
                return;
            }

            this.isUpdating = true;

            try {
                const response = await fetch(`/gestor/estoque/${this.itemId}/update-quantity`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        current_quantity: parseFloat(this.currentQuantity)
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    this.originalQuantity = parseFloat(this.currentQuantity);
                    
                    // Update status from response
                    if (data.item) {
                        this.isLow = data.item.is_low_stock;
                        this.isCritical = data.item.is_critical_stock;
                    } else {
                        // Fallback calculation
                        this.isLow = this.currentQuantity <= this.minQuantity;
                        this.isCritical = this.currentQuantity < (this.minQuantity * 0.5);
                    }
                    
                    this.showToast(data.message || 'Quantidade atualizada com sucesso!', 'success');
                    
                    // Reload page after a short delay to update all stats
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    const error = await response.json();
                    throw new Error(error.message || 'Erro ao atualizar quantidade');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao atualizar quantidade', 'error');
                this.currentQuantity = this.originalQuantity;
            } finally {
                this.isUpdating = false;
            }
        },

        showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <span class="text-lg mr-2">${type === 'success' ? '✅' : '❌'}</span>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}
</script>
@endsection
