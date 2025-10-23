@extends('layouts.admin')

@section('title', 'Detalhes do Produto - ' . $product->name . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">📦</span>Detalhes do Produto
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informações completas sobre {{ $product->name }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Produto
                    </a>

                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Product Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Product Details Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações do Produto</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nome</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Categoria</dt>
                                <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                    @if($product->category)
                                        <span class="mr-2">{{ $product->category->emoji ?: '📂' }}</span>
                                        {{ $product->category->name }}
                                    @else
                                        <span class="text-gray-400">Sem categoria</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Preço de Venda</dt>
                                <dd class="mt-1 text-lg font-semibold text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</dd>
                            </div>
                            @if($product->cost_price)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Preço de Custo</dt>
                                <dd class="mt-1 text-sm text-gray-900">R$ {{ number_format($product->cost_price, 2, ',', '.') }}</dd>
                            </div>
                            @endif
                            @if($product->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Descrição</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->description }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <span class="w-2 h-2 mr-1 rounded-full {{ $product->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $product->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Sales Statistics Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">📊</span> Estatísticas de Vendas
                        </h3>
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Vendido</dt>
                                <dd class="mt-1 text-3xl font-bold text-green-600">{{ $totalSales }} unidades</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Receita Total</dt>
                                <dd class="mt-1 text-2xl font-bold text-blue-600">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Vendas (pedidos)</dt>
                                <dd class="mt-1 text-xl font-semibold text-purple-600">{{ $totalOrders }} pedidos</dd>
                            </div>
                            @if($totalSales > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Valor Médio por Venda</dt>
                                <dd class="mt-1 text-lg font-semibold text-indigo-600">
                                    R$ {{ number_format($totalRevenue / $totalSales, 2, ',', '.') }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Similar Products Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">🔗</span> Produtos Similares
                        </h3>

                        @if($similarProducts->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($similarProducts as $similarProduct)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $similarProduct->name }}</p>
                                            <p class="text-xs text-gray-500">R$ {{ number_format($similarProduct->price, 2, ',', '.') }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $similarProduct->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $similarProduct->active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                        <a href="{{ route('products.show', $similarProduct) }}" class="ml-3 text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">📦</div>
                                <p class="text-sm text-gray-500">Nenhum produto similar encontrado</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
