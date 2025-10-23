@extends('layouts.admin')

@section('title', 'Dashboard - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main Dashboard Content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">
                    👋 Bem-vindo, {{ Auth::user()->name }}!
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Aqui está um resumo das atividades de hoje na Doce Doce Brigaderia.
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- vendas de hoje -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-md flex items-center justify-center">
                                    <span class="text-white">💰</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Vendas de Hoje
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        R$ 2.450,00
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Pendentes -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-md flex items-center justify-center">
                                    <span class="text-white">🛒</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Pedidos Pendentes
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        12
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clientes Hoje -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-md flex items-center justify-center">
                                    <span class="text-white">👥</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Clientes Hoje
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        8
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtos em Baixo Estoque -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-md flex items-center justify-center">
                                    <span class="text-white">⚠️</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Produtos com Baixo Estoque
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        3
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Sales -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        📈 Vendas Recentes
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm">🍰</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pedido #1234</p>
                                    <p class="text-sm text-gray-500">João Silva • R$ 45,00</p>
                                </div>
                            </div>
                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                Concluído
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        🚀 Ações Rápidas
                    </h3>
                    <div class="grid grid-cols-1 gap-3">
                        <button class="flex items-center justify-center w-full px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <span class="mr-2">➕</span>
                            Nova Venda
                        </button>

                        <a href="{{ route('products.index') }}" class="flex items-center justify-center w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <span class="mr-2">📦</span>
                            Gerenciar Produtos
                        </a>

                        <button class="flex items-center justify-center w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <span class="mr-2">👥</span>
                            Ver Clientes
                        </button>

                        <button class="flex items-center justify-center w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <span class="mr-2">📊</span>
                            Relatórios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
