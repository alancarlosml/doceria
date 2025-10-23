@extends('layouts.admin')

@section('title', 'Detalhes do Cliente - ' . $customer->name . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">
                            @if($totalSales > 0)
                                👑
                            @else
                                👤
                            @endif
                        </span>Detalhes do Cliente
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informações completas sobre {{ $customer->name }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Cliente
                    </a>

                    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Customer Details Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações do Cliente</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nome</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $customer->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($customer->phone)
                                        📱 {{ $customer->phone }}
                                    @else
                                        <span class="text-gray-400">Não informado</span>
                                    @endif
                                </dd>
                            </div>
                            @if($customer->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900">✉️ {{ $customer->email }}</dd>
                            </div>
                            @endif
                            @if($customer->cpf)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CPF</dt>
                                <dd class="mt-1 text-sm text-gray-900">🆔 {{ $customer->cpf }}</dd>
                            </div>
                            @endif
                            @if($customer->address || $customer->city)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($customer->address)
                                        📍 {{ $customer->address }}
                                        @if($customer->neighborhood)
                                            - {{ $customer->neighborhood }}
                                        @endif
                                        @if($customer->city)
                                            <br>{{ $customer->city }}
                                            @if($customer->state)
                                                /{{ $customer->state }}
                                            @endif
                                        @endif
                                    @else
                                        📍 {{ $customer->city }}
                                        @if($customer->state)
                                            /{{ $customer->state }}
                                        @endif
                                    @endif
                                </dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->created_at->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->updated_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">📊</span> Estatísticas de Compras
                        </h3>
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de Pedidos</dt>
                                <dd class="mt-1 text-3xl font-bold text-green-600">{{ $totalSales }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Gasto</dt>
                                <dd class="mt-1 text-2xl font-bold text-blue-600">R$ {{ number_format($totalOrders, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de Produtos</dt>
                                <dd class="mt-1 text-xl font-semibold text-purple-600">{{ $totalProducts }} produtos</dd>
                            </div>
                            @if($totalSales > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ticket Médio</dt>
                                <dd class="mt-1 text-lg font-semibold text-indigo-600">
                                    R$ {{ number_format($totalOrders / $totalSales, 2, ',', '.') }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Favorite Product & Recent Sales Cards -->
                <div class="space-y-6">
                    <!-- Favorite Product Card -->
                    @if($favoriteProduct)
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                                <span class="mr-2">⭐</span> Produto Favorito
                            </h3>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-pink-100 to-green-100 flex items-center justify-center">
                                        @if($favoriteProduct->category)
                                            <span class="text-lg">{{ $favoriteProduct->category->emoji ?: '🍰' }}</span>
                                        @else
                                            <span class="text-lg">🍰</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $favoriteProduct->name }}</p>
                                    <p class="text-xs text-gray-500">R$ {{ number_format($favoriteProduct->price, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Status Card -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                                <span class="mr-2">🏷️</span> Status do Cliente
                            </h3>
                            <div class="text-center">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($totalSales > 10)
                                        bg-purple-100 text-purple-800
                                    @elseif($totalSales > 5)
                                        bg-blue-100 text-blue-800
                                    @elseif($totalSales > 0)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    @if($totalSales > 10)
                                        👑 Cliente VIP
                                    @elseif($totalSales > 5)
                                        🌟 Cliente Frequente
                                    @elseif($totalSales > 0)
                                        ✅ Cliente Ativo
                                    @else
                                        🆕 Cliente Novo
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    @if($totalSales > 10)
                                        Cliente premium com mais de 10 pedidos
                                    @elseif($totalSales > 5)
                                        Cliente frequente com pedidos recorrentes
                                    @elseif($totalSales > 0)
                                        Cliente ativo com histórico de compras
                                    @else
                                        Novo cliente sem pedidos realizados
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales Section -->
            @if($recentSales->isNotEmpty())
            <div class="mt-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">🕒</span> Pedidos Recentes
                        </h3>

                        <div class="space-y-4">
                            @foreach($recentSales as $sale)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                Pedido #{{ $sale->id }}
                                            </div>
                                            @if($sale->type === 'delivery')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    🚚 Entrega
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    🏪 Balcão
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-lg font-bold text-green-600">
                                            R$ {{ number_format($sale->total, 2, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="text-sm text-gray-600 mb-2">
                                        📅 {{ $sale->created_at->format('d/m/Y \à\s H:i') }}
                                    </div>

                                    <!-- Sale Items -->
                                    <div class="space-y-1">
                                        @foreach($sale->items as $item)
                                            <div class="flex justify-between text-sm text-gray-600">
                                                <span>
                                                    {{ $item->quantity }}x {{ $item->product->name }}
                                                    @if($item->product->category)
                                                        ({{ $item->product->category->emoji }})
                                                    @endif
                                                </span>
                                                <span>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($sale->notes)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-xs text-gray-500">
                                                📝 {{ $sale->notes }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($totalSales > 10)
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-500">
                                    Mostrando os últimos 10 pedidos de um total de {{ $totalSales }}
                                </p>
                                <a href="#" class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver todos os pedidos →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="mt-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6 text-center">
                        <div class="text-6xl mb-4">🛒</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido realizado</h3>
                        <p class="text-sm text-gray-500">Este cliente ainda não fez nenhum pedido na doceria.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection
