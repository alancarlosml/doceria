@extends('layouts.admin')

@section('title', 'Produtos - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none" x-data="productsManager()">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        📦 Produtos
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie todos os produtos disponíveis na doceria organizados por categoria.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Gerenciar Categorias
                    </a>
                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Novo Produto
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Produtos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::count() }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Produtos Ativos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::where('active', true)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-yellow-100 p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Categorias</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\Category::count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-red-100 p-3">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Produtos Inativos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::where('active', false)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white shadow rounded-lg mb-6 p-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request()->input('search') }}"
                            placeholder="Buscar produtos..."
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <select
                            id="status"
                            name="status"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">Todos os Status</option>
                            <option value="active" {{ request()->input('status') === 'active' ? 'selected' : '' }}>Apenas Ativos</option>
                            <option value="inactive" {{ request()->input('status') === 'inactive' ? 'selected' : '' }}>Apenas Inativos</option>
                        </select>
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

            <!-- Products by Category -->
            @php
                $productsPaginator = $products; // Guardar o paginator completo
                $productsByCategory = collect($products->items())
                    ->groupBy(function($product) {
                        return $product->category->name ?? 'Sem categoria';
                    })
                    ->sortKeys();

                $hasPages = $productsPaginator->hasPages();
            @endphp

            @if($productsByCategory->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto cadastrado</h3>
                    <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro produto.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Novo Produto
                        </a>
                    </div>
                </div>
            @else
                @foreach($productsByCategory as $categoryName => $products)
                    @php
                        $category = $products->first()->category;
                        $categoryId = $category->id ?? null;
                    @endphp
                    <div class="mb-12" x-data="{ categoryId: {{ $categoryId }}, categoryActive: {{ $category->active ? 'true' : 'false' }} }">
                        <!-- Category Header -->
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 mb-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-2xl shadow">
                                        {{ $category->emoji ?? '📦' }}
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">{{ $categoryName }}</h3>
                                        <p class="text-sm text-gray-600">{{ $products->count() }} produtos nesta categoria</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <!-- Category Toggle -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-700">Categoria:</span>
                                        <button
                                            type="button"
                                            @click="toggleCategory(categoryId)"
                                            :class="categoryActive ? 'bg-green-600' : 'bg-gray-200'"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        >
                                            <span
                                                :class="categoryActive ? 'translate-x-5' : 'translate-x-0'"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            ></span>
                                        </button>
                                        <span 
                                            class="text-xs font-medium"
                                            :class="categoryActive ? 'text-green-600' : 'text-gray-500'"
                                            x-text="categoryActive ? 'Ativa' : 'Inativa'"
                                        ></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($products as $product)
                                <div 
                                    class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200"
                                    x-data="{ productActive: {{ $product->active ? 'true' : 'false' }} }"
                                >
                                    <!-- Product Image -->
                                    <div class="h-48 bg-gradient-to-br from-pink-100 to-green-100 flex items-center justify-center relative">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="text-6xl opacity-70">
                                                {{ $product->category->emoji ?? '🍰' }}
                                            </div>
                                        @endif
                                        
                                        <!-- Status Badge -->
                                        <div class="absolute top-2 right-2">
                                            <span 
                                                :class="productActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                <span x-show="productActive">✅ Ativo</span>
                                                <span x-show="!productActive">❌ Inativo</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-5">
                                        <h4 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">
                                            {{ $product->name }}
                                        </h4>
                                        
                                        @if($product->description)
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2 min-h-[2.5rem]">
                                                {{ $product->description }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400 italic mb-3 min-h-[2.5rem]">
                                                Sem descrição
                                            </p>
                                        @endif

                                        <!-- Price -->
                                        <div class="mb-4">
                                            <div class="text-2xl font-bold text-green-600">
                                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                            </div>
                                            @if($product->cost_price)
                                                <div class="text-xs text-gray-500">
                                                    Custo: R$ {{ number_format($product->cost_price, 2, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Toggle -->
                                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                                            <span class="text-sm font-medium text-gray-700">Status do Produto:</span>
                                            <button
                                                type="button"
                                                @click="toggleProduct({{ $product->id }})"
                                                :class="productActive ? 'bg-green-600' : 'bg-gray-200'"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            >
                                                <span
                                                    :class="productActive ? 'translate-x-5' : 'translate-x-0'"
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                ></span>
                                            </button>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2">
                                            <a
                                                href="{{ route('products.show', $product) }}"
                                                class="inline-flex items-center justify-center p-2 border border-green-600 rounded-lg text-green-600 hover:bg-green-50 transition-colors"
                                                title="Ver detalhes"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <a
                                                href="{{ route('products.edit', $product) }}"
                                                class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('products.destroy', $product) }}"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')"
                                                class="flex-shrink-0"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center p-2 border border-red-600 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                                    title="Excluir produto"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Pagination -->
            @if($hasPages)
                <div class="mt-8 mb-8">
                    <div class="flex items-center justify-between bg-white border-t border-gray-200 px-4 py-3 sm:px-6">
                        <div class="flex flex-1 justify-between sm:hidden">
                            {!! $productsPaginator->links() !!}
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ $productsPaginator->firstItem() }}</span>
                                    a
                                    <span class="font-medium">{{ $productsPaginator->lastItem() }}</span>
                                    de
                                    <span class="font-medium">{{ $productsPaginator->total() }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                {!! $productsPaginator->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Alpine.js Products Manager -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filter-btn');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');

    // Função para aplicar filtros
    function applyFilters() {
        const search = document.getElementById('search').value;
        const status = document.getElementById('status').value;

        // Construir URL com parâmetros
        let url = '{{ route("products.index") }}?';
        const params = [];

        if (search) params.push('search=' + encodeURIComponent(search));
        if (status) params.push('status=' + encodeURIComponent(status));

        url += params.join('&');

        // Redirecionar para aplicar filtros
        window.location.href = url;
    }

    // Função para limpar filtros
    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('status').value = '';

        // Voltar para URL sem filtros
        window.location.href = '{{ route("products.index") }}';
    }

    // Event listeners
    if (filterBtn) {
        filterBtn.addEventListener('click', applyFilters);
    }

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }

    // Permitir filtrar pressionando Enter no campo de busca
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }

    // Permitir filtrar automaticamente ao mudar status
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            setTimeout(applyFilters, 100);
        });
    }
});

function productsManager() {
    return {
        searchQuery: '',
        statusFilter: 'all',

        init() {
            console.log('Products Manager initialized');
        },

        async toggleProduct(productId) {
            try {
                const response = await fetch(`/products/${productId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.showToast(data.message, 'success');
                    
                    // Recarregar a página após 500ms
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    throw new Error('Falha ao atualizar status');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao atualizar status do produto', 'error');
            }
        },

        async toggleCategory(categoryId) {
            try {
                const response = await fetch(`/categories/${categoryId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.showToast(data.message, 'success');
                    
                    // Recarregar a página após 500ms
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    throw new Error('Falha ao atualizar status');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao atualizar status da categoria', 'error');
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
