@extends('layouts.admin')

@section('title', 'Detalhes da Categoria - ' . $category->name . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">{{ $category->emoji ?: '📂' }}</span>Detalhes da Categoria
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informações completas sobre a {{ $category->name }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Categoria
                    </a>

                    <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Category Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Category Details Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações da Categoria</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nome</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                            </div>
                            @if($category->emoji)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Emoji</dt>
                                <dd class="mt-1 text-2xl">{{ $category->emoji }}</dd>
                            </div>
                            @endif
                            @if($category->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Descrição</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->description }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $category->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <span class="w-2 h-2 mr-1 rounded-full {{ $category->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $category->active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">📊</span> Estatísticas dos Produtos
                        </h3>
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de Produtos</dt>
                                <dd class="mt-1 text-3xl font-bold text-green-600">{{ $productCount }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Produtos Ativos</dt>
                                <dd class="mt-1 text-2xl font-bold text-blue-600">{{ $activeProductCount }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Produtos Inativos</dt>
                                <dd class="mt-1 text-xl font-semibold text-gray-600">{{ $productCount - $activeProductCount }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Recent Products Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">🕒</span> Produtos Recentes
                        </h3>

                        @if($recentProducts->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($recentProducts as $product)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                        @if($product->category->emoji)
                                            <div class="text-2xl ml-2">{{ $product->category->emoji }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if($productCount > 10)
                                <div class="mt-4 text-center">
                                    <p class="text-sm text-gray-500">
                                        Mostrando os últimos 10 produtos de um total de {{ $productCount }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">{{ $category->emoji ?: '📦' }}</div>
                                <p class="text-sm text-gray-500">Nenhum produto nesta categoria ainda</p>
                                <a href="{{ route('products.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Adicionar primeiro produto →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
