@extends('layouts.admin')

@section('title', 'Encomendas - Doce Doce Brigaderia')

@section('admin-content')
<main class="flex-1 relative overflow-y-auto focus:outline-none" x-data="encomendasManager()">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        📦 Encomendas
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie pedidos futuros e encomendas especiais
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('encomendas.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nova Encomenda
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white shadow rounded-lg mb-6 p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="filters.status" @change="filterEncomendas()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Todos</option>
                            <option value="pendente">Pendente</option>
                            <option value="em_producao">Em Produção</option>
                            <option value="pronto">Pronto</option>
                            <option value="entregue">Entregue</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Entrega</label>
                        <input type="date" x-model="filters.delivery_date" @change="filterEncomendas()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                        <select x-model="filters.period" @change="filterEncomendas()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="todos">Todos</option>
                            <option value="hoje">Hoje</option>
                            <option value="amanha">Amanhã</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes">Este Mês</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <input type="text" x-model="filters.search" @input="filterEncomendas()" placeholder="Cliente, código..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-yellow-100 p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pendentes</dt>
                                <dd class="text-2xl font-semibold text-gray-900" x-text="stats.pendentes"></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Em Produção</dt>
                                <dd class="text-2xl font-semibold text-gray-900" x-text="stats.em_producao"></dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Para Hoje</dt>
                                <dd class="text-2xl font-semibold text-gray-900" x-text="stats.hoje"></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Valor Total</dt>
                                <dd class="text-2xl font-semibold text-gray-900">R$ <span x-text="stats.valor_total"></span></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline de Encomendas -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Encomendas por Data de Entrega</h3>
                </div>

                <div class="p-6">
                    <template x-if="groupedEncomendas.length === 0">
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📦</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma encomenda encontrada</h3>
                            <p class="text-gray-500">Adicione uma nova encomenda para começar</p>
                        </div>
                    </template>

                    <template x-for="(group, index) in groupedEncomendas" :key="index">
                        <div class="mb-8">
                            <!-- Header da Data -->
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-xl">📅</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-bold text-gray-900" x-text="group.date_formatted"></h4>
                                    <p class="text-sm text-gray-500">
                                        <span x-text="group.encomendas.length"></span> encomenda<span x-show="group.encomendas.length !== 1">s</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Lista de Encomendas -->
                            <div class="ml-16 space-y-3">
                                <template x-for="encomenda in group.encomendas" :key="encomenda.id">
                                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 hover:shadow-md transition-shadow cursor-pointer"
                                         :class="{
                                             'border-yellow-500': encomenda.status === 'pendente',
                                             'border-blue-500': encomenda.status === 'em_producao',
                                             'border-green-500': encomenda.status === 'pronto',
                                             'border-purple-500': encomenda.status === 'entregue',
                                             'border-red-500': encomenda.status === 'cancelado'
                                         }"
                                         @click="showEncomendaDetails(encomenda)">
                                        
                                        <div class="flex items-start justify-between">
                                            <!-- Info Principal -->
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="font-bold text-gray-900" x-text="'#' + encomenda.code"></span>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                                          :class="{
                                                              'bg-yellow-100 text-yellow-800': encomenda.status === 'pendente',
                                                              'bg-blue-100 text-blue-800': encomenda.status === 'em_producao',
                                                              'bg-green-100 text-green-800': encomenda.status === 'pronto',
                                                              'bg-purple-100 text-purple-800': encomenda.status === 'entregue',
                                                              'bg-red-100 text-red-800': encomenda.status === 'cancelado'
                                                          }"
                                                          x-text="getStatusLabel(encomenda.status)">
                                                    </span>
                                                </div>

                                                <!-- Cliente -->
                                                <div class="text-sm text-gray-600 mb-2">
                                                    <span class="font-medium">👤</span>
                                                    <span x-text="encomenda.customer_name || 'Cliente não identificado'"></span>
                                                </div>

                                                <!-- Produtos -->
                                                <div class="text-sm text-gray-700 mb-2">
                                                    <span class="font-medium">🧾 Produtos:</span>
                                                    <span x-text="encomenda.items_summary"></span>
                                                </div>

                                                <!-- Horário -->
                                                <div class="text-xs text-gray-500">
                                                    <span>⏰ Horário de entrega: </span>
                                                    <span class="font-medium" x-text="encomenda.delivery_time || 'Não especificado'"></span>
                                                </div>

                                                <!-- Observações -->
                                                <div x-show="encomenda.notes" class="mt-2 text-sm text-gray-600 italic">
                                                    <span>💬 </span>
                                                    <span x-text="encomenda.notes"></span>
                                                </div>
                                            </div>

                                            <!-- Valor e Ações -->
                                            <div class="ml-4 text-right">
                                                <div class="text-2xl font-bold text-green-600 mb-3">
                                                    R$ <span x-text="encomenda.total_formatted"></span>
                                                </div>

                                                <!-- Botões de Ação -->
                                                <div class="flex flex-col gap-2">
                                                    <button
                                                        @click.stop="updateStatus(encomenda.id, 'em_producao')"
                                                        x-show="encomenda.status === 'pendente'"
                                                        class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors"
                                                    >
                                                        🔄 Iniciar Produção
                                                    </button>
                                                    
                                                    <button
                                                        @click.stop="updateStatus(encomenda.id, 'pronto')"
                                                        x-show="encomenda.status === 'em_producao'"
                                                        class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition-colors"
                                                    >
                                                        ✅ Marcar Pronto
                                                    </button>
                                                    
                                                    <button
                                                        @click.stop="openPaymentModal(encomenda)"
                                                        x-show="encomenda.status === 'pronto'"
                                                        class="px-3 py-1 bg-purple-500 text-white text-xs rounded hover:bg-purple-600 transition-colors"
                                                    >
                                                        💰 Finalizar Entrega
                                                    </button>

                                                    <button
                                                        @click.stop="showEncomendaDetails(encomenda)"
                                                        class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600 transition-colors"
                                                    >
                                                        👁️ Detalhes
                                                    </button>

                                                    <a
                                                        :href="`/gestor/encomendas/${encomenda.id}/editar`"
                                                        class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 transition-colors text-center"
                                                    >
                                                        ✏️ Editar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showModal = false"
    >
        <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <template x-if="selectedEncomenda">
                <div>
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-2xl font-bold">Encomenda <span x-text="'#' + selectedEncomenda.code"></span></h3>
                            <button @click="showModal = false" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium"
                                  x-text="getStatusLabel(selectedEncomenda.status)">
                            </span>
                        </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="p-6">
                        <!-- Cliente -->
                        <div class="mb-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-3">👤 Cliente</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="font-medium" x-text="selectedEncomenda.customer_name || 'Cliente não identificado'"></p>
                                <p class="text-sm text-gray-600" x-show="selectedEncomenda.customer_phone" x-text="'📞 ' + selectedEncomenda.customer_phone"></p>
                            </div>
                        </div>

                        <!-- Entrega -->
                        <div class="mb-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-3">📅 Informações de Entrega</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p class="text-sm">
                                    <span class="font-medium">Data:</span>
                                    <span x-text="selectedEncomenda.delivery_date_formatted"></span>
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Horário:</span>
                                    <span x-text="selectedEncomenda.delivery_time || 'Não especificado'"></span>
                                </p>
                                <p class="text-sm" x-show="selectedEncomenda.delivery_address">
                                    <span class="font-medium">Endereço:</span>
                                    <span x-text="selectedEncomenda.delivery_address"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Produtos -->
                        <div class="mb-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-3">🧾 Produtos</h4>
                            <div class="space-y-2">
                                <template x-for="item in selectedEncomenda.items" :key="item.id">
                                    <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium" x-text="item.quantity + 'x ' + item.product_name"></p>
                                            <p class="text-sm text-gray-600" x-show="item.notes" x-text="'💬 ' + item.notes"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">R$ <span x-text="item.unit_price_formatted"></span> cada</p>
                                            <p class="font-bold text-green-600">R$ <span x-text="item.subtotal_formatted"></span></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Totais -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">Subtotal:</span>
                                <span class="font-medium">R$ <span x-text="selectedEncomenda.subtotal_formatted"></span></span>
                            </div>
                            <div class="flex justify-between mb-2" x-show="selectedEncomenda.discount > 0">
                                <span class="text-gray-700">Desconto:</span>
                                <span class="font-medium text-red-600">- R$ <span x-text="selectedEncomenda.discount_formatted"></span></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-300">
                                <span class="text-lg font-bold">TOTAL:</span>
                                <span class="text-2xl font-bold text-green-600">R$ <span x-text="selectedEncomenda.total_formatted"></span></span>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mb-6" x-show="selectedEncomenda.notes">
                            <h4 class="text-lg font-bold text-gray-800 mb-3">💬 Observações</h4>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-gray-700" x-text="selectedEncomenda.notes"></p>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex gap-3">
                            <a
                                :href="`/gestor/encomendas/${selectedEncomenda.id}/editar`"
                                class="flex-1 bg-yellow-500 text-white py-3 rounded-lg font-bold hover:bg-yellow-600 transition-colors text-center"
                            >
                                ✏️ Editar
                            </a>
                            <button
                                @click="printEncomenda()"
                                class="flex-1 bg-blue-500 text-white py-3 rounded-lg font-bold hover:bg-blue-600 transition-colors"
                            >
                                🖨️ Imprimir
                            </button>
                            <button
                                x-show="selectedEncomenda.status === 'pronto'"
                                @click="resetEncomendaPayment(); encomendaPayment.splits = [{method: '', value: selectedEncomenda.total, amountReceived: 0, changeAmount: 0}]; showPaymentModal = true; showModal = false"
                                class="flex-1 bg-green-500 text-white py-3 rounded-lg font-bold hover:bg-green-600 transition-colors"
                            >
                                💰 Finalizar
                            </button>
                            <button
                                @click="showModal = false"
                                class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-bold hover:bg-gray-400 transition-colors"
                            >
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal de Pagamento para Encomenda -->
    <div x-show="showPaymentModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-4"
         @click.self="showPaymentModal = false"
    >
        <div class="bg-white rounded-2xl max-w-lg w-full mx-4 shadow-2xl max-h-[95vh] overflow-y-auto" @click.stop>
            <template x-if="selectedEncomenda">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold">💳 Finalizar Encomenda</h3>
                        <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
                    </div>
                    
                    <p class="text-gray-600 mb-4">Encomenda: <span class="font-bold" x-text="'#' + selectedEncomenda.code"></span></p>

                    <!-- Resumo -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between pt-2 text-xl">
                            <span class="font-bold">TOTAL:</span>
                            <span class="font-bold text-green-600">R$ <span x-text="selectedEncomenda.total_formatted"></span></span>
                        </div>
                    </div>

                    <!-- Toggle Pagamento Dividido -->
                    <div class="flex items-center justify-between mb-4 p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">💰</span>
                            <span class="font-medium text-gray-700">Pagamento Dividido</span>
                        </div>
                        <button 
                            @click="encomendaPayment.isSplit = !encomendaPayment.isSplit; if (encomendaPayment.isSplit) { encomendaPayment.splits = [{method: '', value: selectedEncomenda.total, amountReceived: 0, changeAmount: 0}]; } else { encomendaPayment.splits = [{method: '', value: 0, amountReceived: 0, changeAmount: 0}]; }"
                            :class="encomendaPayment.isSplit ? 'bg-purple-600' : 'bg-gray-300'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                        >
                            <span 
                                :class="encomendaPayment.isSplit ? 'translate-x-6' : 'translate-x-1'"
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            ></span>
                        </button>
                    </div>

                    <!-- Pagamento Simples -->
                    <div x-show="!encomendaPayment.isSplit">
                        <label class="block text-sm font-bold mb-3">Forma de Pagamento:</label>
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <button
                                @click="encomendaPayment.method = 'dinheiro'"
                                :class="encomendaPayment.method === 'dinheiro' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                                class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                            >
                                💵 Dinheiro
                            </button>
                            <button
                                @click="encomendaPayment.method = 'pix'"
                                :class="encomendaPayment.method === 'pix' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                                class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                            >
                                📱 PIX
                            </button>
                            <button
                                @click="encomendaPayment.method = 'cartao_debito'"
                                :class="encomendaPayment.method === 'cartao_debito' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                                class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                            >
                                💳 Débito
                            </button>
                            <button
                                @click="encomendaPayment.method = 'cartao_credito'"
                                :class="encomendaPayment.method === 'cartao_credito' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-white border-2 border-gray-200 hover:border-green-300'"
                                class="py-3 rounded-lg font-medium transition-all duration-200 active:scale-95"
                            >
                                💳 Crédito
                            </button>
                        </div>

                        <!-- Valor Recebido e Troco (apenas para dinheiro) -->
                        <div x-show="encomendaPayment.method === 'dinheiro'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <label class="block text-sm font-bold text-yellow-800 mb-2">💵 Valor Recebido:</label>
                            <input 
                                type="number" 
                                x-model.number="encomendaPayment.amountReceived"
                                @input="encomendaPayment.changeAmount = encomendaPayment.amountReceived >= selectedEncomenda.total ? encomendaPayment.amountReceived - selectedEncomenda.total : 0"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-3 border-2 border-yellow-300 rounded-lg text-lg font-bold text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                            >
                            
                            <!-- Atalhos de valor -->
                            <div class="flex gap-2 mt-2 flex-wrap">
                                <button 
                                    @click="encomendaPayment.amountReceived = Math.ceil(selectedEncomenda.total / 10) * 10; encomendaPayment.changeAmount = encomendaPayment.amountReceived - selectedEncomenda.total"
                                    class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                                >
                                    R$ <span x-text="formatMoney(Math.ceil(selectedEncomenda.total / 10) * 10)"></span>
                                </button>
                                <button 
                                    @click="encomendaPayment.amountReceived = Math.ceil(selectedEncomenda.total / 50) * 50; encomendaPayment.changeAmount = encomendaPayment.amountReceived - selectedEncomenda.total"
                                    class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                                >
                                    R$ <span x-text="formatMoney(Math.ceil(selectedEncomenda.total / 50) * 50)"></span>
                                </button>
                                <button 
                                    @click="encomendaPayment.amountReceived = 100; encomendaPayment.changeAmount = 100 - selectedEncomenda.total"
                                    class="px-3 py-1 bg-yellow-200 hover:bg-yellow-300 rounded text-sm font-medium"
                                >
                                    R$ 100,00
                                </button>
                            </div>
                            
                            <!-- Troco -->
                            <div x-show="encomendaPayment.changeAmount > 0" class="mt-3 p-3 bg-green-100 border border-green-300 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-green-800">🔄 TROCO:</span>
                                    <span class="text-2xl font-bold text-green-700">R$ <span x-text="formatMoney(encomendaPayment.changeAmount)"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagamento Dividido -->
                    <div x-show="encomendaPayment.isSplit">
                        <label class="block text-sm font-bold mb-3">Formas de Pagamento:</label>
                        
                        <div class="space-y-3 mb-4">
                            <template x-for="(split, index) in encomendaPayment.splits" :key="index">
                                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                                    <select 
                                        x-model="split.method"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    >
                                        <option value="">Selecione...</option>
                                        <option value="dinheiro">💵 Dinheiro</option>
                                        <option value="pix">📱 PIX</option>
                                        <option value="cartao_debito">💳 Débito</option>
                                        <option value="cartao_credito">💳 Crédito</option>
                                    </select>
                                    <input 
                                        type="number" 
                                        x-model.number="split.value"
                                        step="0.01"
                                        min="0"
                                        placeholder="R$ 0,00"
                                        class="w-28 px-3 py-2 border border-gray-300 rounded-lg text-sm text-right"
                                    >
                                    <button 
                                        @click="encomendaPayment.splits.splice(index, 1)"
                                        x-show="encomendaPayment.splits.length > 1"
                                        class="p-2 text-red-500 hover:bg-red-100 rounded-lg"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        <button 
                            @click="encomendaPayment.splits.push({method: '', value: 0, amountReceived: 0, changeAmount: 0})"
                            class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-purple-400 hover:text-purple-600 font-medium mb-4"
                        >
                            + Adicionar forma de pagamento
                        </button>
                        
                        <!-- Resumo -->
                        <div class="bg-purple-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">Total informado:</span>
                                <span 
                                    class="font-bold"
                                    :class="getSplitTotal() >= selectedEncomenda.total ? 'text-green-600' : 'text-red-600'"
                                >
                                    R$ <span x-text="formatMoney(getSplitTotal())"></span>
                                </span>
                            </div>
                            <div x-show="getSplitTotal() < selectedEncomenda.total" class="text-red-600 text-sm">
                                ⚠️ Faltam R$ <span x-text="formatMoney(selectedEncomenda.total - getSplitTotal())"></span>
                            </div>
                        </div>
                        
                        <!-- Campos de troco para dinheiro no split -->
                        <template x-for="(split, index) in encomendaPayment.splits" :key="'cash-enc-' + index">
                            <div x-show="split.method === 'dinheiro' && split.value > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                                <label class="block text-sm font-bold text-yellow-800 mb-2">
                                    💵 Valor Recebido (Pagamento <span x-text="index + 1"></span>):
                                </label>
                                <input 
                                    type="number" 
                                    x-model.number="split.amountReceived"
                                    @input="split.changeAmount = split.amountReceived >= split.value ? split.amountReceived - split.value : 0"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-3 py-2 border-2 border-yellow-300 rounded-lg text-center font-bold"
                                >
                                <div x-show="split.changeAmount > 0" class="mt-2 p-2 bg-green-100 rounded flex justify-between items-center">
                                    <span class="text-green-800 font-medium">🔄 Troco:</span>
                                    <span class="font-bold text-green-700">R$ <span x-text="formatMoney(split.changeAmount)"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Botões -->
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <button
                            @click="showPaymentModal = false"
                            class="bg-gray-300 text-gray-700 py-3 rounded-lg font-bold hover:bg-gray-400 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="finalizarEncomendaComPagamento()"
                            :disabled="!isEncomendaPaymentValid()"
                            class="bg-green-600 hover:bg-green-700 active:scale-95 text-white py-3 rounded-lg font-bold disabled:opacity-50 transition-all duration-200"
                        >
                            ✅ Confirmar Entrega
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</main>

<script>
function encomendasManager() {
    return {
        filters: {
            status: '',
            delivery_date: '',
            period: 'todos',
            search: ''
        },
        
        stats: {
            pendentes: 0,
            em_producao: 0,
            hoje: 0,
            valor_total: '0,00'
        },

        groupedEncomendas: [],
        showModal: false,
        showPaymentModal: false,
        selectedEncomenda: null,
        
        // Sistema de pagamento para encomendas
        encomendaPayment: {
            isSplit: false,
            method: null,
            amountReceived: 0,
            changeAmount: 0,
            splits: [{ method: '', value: 0, amountReceived: 0, changeAmount: 0 }]
        },

        init() {
            this.loadEncomendas();
            this.loadStats();
        },
        
        formatMoney(value) {
            return parseFloat(value || 0).toFixed(2).replace('.', ',');
        },
        
        getSplitTotal() {
            return this.encomendaPayment.splits.reduce((sum, split) => sum + (parseFloat(split.value) || 0), 0);
        },
        
        isEncomendaPaymentValid() {
            if (this.encomendaPayment.isSplit) {
                const allMethodsSelected = this.encomendaPayment.splits.every(split => split.method && split.value > 0);
                const totalCovers = this.getSplitTotal() >= this.selectedEncomenda.total;
                const cashValid = this.encomendaPayment.splits.every(split => {
                    if (split.method === 'dinheiro' && split.value > 0) {
                        return split.amountReceived >= split.value;
                    }
                    return true;
                });
                return allMethodsSelected && totalCovers && cashValid;
            } else {
                if (!this.encomendaPayment.method) return false;
                if (this.encomendaPayment.method === 'dinheiro') {
                    return this.encomendaPayment.amountReceived >= this.selectedEncomenda.total;
                }
                return true;
            }
        },
        
        async finalizarEncomendaComPagamento() {
            if (!this.isEncomendaPaymentValid()) {
                this.showToast('Complete os dados de pagamento!', 'error');
                return;
            }
            
            try {
                // Preparar dados de pagamento
                let paymentData = {};
                if (this.encomendaPayment.isSplit) {
                    paymentData = {
                        payment_method: 'split',
                        payment_methods_split: this.encomendaPayment.splits.map(split => ({
                            method: split.method,
                            value: split.value,
                            amount_received: split.method === 'dinheiro' ? split.amountReceived : null,
                            change_amount: split.method === 'dinheiro' ? split.changeAmount : null
                        })),
                        amount_received: null,
                        change_amount: null
                    };
                } else {
                    paymentData = {
                        payment_method: this.encomendaPayment.method,
                        payment_methods_split: null,
                        amount_received: this.encomendaPayment.method === 'dinheiro' ? this.encomendaPayment.amountReceived : null,
                        change_amount: this.encomendaPayment.method === 'dinheiro' ? this.encomendaPayment.changeAmount : null
                    };
                }
                
                const response = await fetch(`/gestor/encomendas/${this.selectedEncomenda.id}/finalizar-pagamento`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(paymentData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showToast('Encomenda finalizada com sucesso!', 'success');
                    this.showPaymentModal = false;
                    this.resetEncomendaPayment();
                    this.loadEncomendas();
                    this.loadStats();
                } else {
                    this.showToast(data.message || 'Erro ao finalizar encomenda', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao finalizar encomenda', 'error');
            }
        },
        
        resetEncomendaPayment() {
            this.encomendaPayment = {
                isSplit: false,
                method: null,
                amountReceived: 0,
                changeAmount: 0,
                splits: [{ method: '', value: 0, amountReceived: 0, changeAmount: 0 }]
            };
        },
        
        openPaymentModal(encomenda) {
            this.selectedEncomenda = encomenda;
            this.resetEncomendaPayment();
            // Inicializar o primeiro split com o valor total da encomenda
            this.encomendaPayment.splits = [{ method: '', value: encomenda.total, amountReceived: 0, changeAmount: 0 }];
            this.showPaymentModal = true;
        },

        async loadEncomendas() {
            try {
                const params = new URLSearchParams();
                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.delivery_date) params.append('delivery_date', this.filters.delivery_date);
                if (this.filters.period) params.append('period', this.filters.period);
                if (this.filters.search) params.append('search', this.filters.search);

                const response = await fetch(`/gestor/api/encomendas?${params.toString()}`);
                const data = await response.json();

                if (data.success) {
                    this.groupedEncomendas = data.data;
                } else {
                    this.showToast('Erro ao carregar encomendas', 'error');
                    this.groupedEncomendas = [];
                }
            } catch (error) {
                console.error('Erro ao carregar encomendas:', error);
                this.showToast('Erro ao carregar encomendas', 'error');
                this.groupedEncomendas = [];
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/gestor/api/encomendas-stats');
                const data = await response.json();

                if (data.success) {
                    this.stats = {
                        pendentes: data.pendentes,
                        em_producao: data.em_producao,
                        hoje: data.hoje,
                        valor_total: data.valor_total
                    };
                } else {
                    this.stats = {
                        pendentes: 0,
                        em_producao: 0,
                        hoje: 0,
                        valor_total: '0,00'
                    };
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
                this.stats = {
                    pendentes: 0,
                    em_producao: 0,
                    hoje: 0,
                    valor_total: '0,00'
                };
            }
        },

        filterEncomendas() {
            this.loadEncomendas();
        },

        getStatusLabel(status) {
            const labels = {
                'pendente': 'Pendente',
                'em_producao': 'Em Produção',
                'pronto': 'Pronto',
                'entregue': 'Entregue',
                'cancelado': 'Cancelado'
            };
            return labels[status] || status;
        },

        showEncomendaDetails(encomenda) {
            this.selectedEncomenda = encomenda;
            this.showModal = true;
        },

        async updateStatus(encomendaId, newStatus) {
            try {
                // Mostrar loading
                const button = event.target;
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = '🔄 Atualizando...';

                // Fazer chamada AJAX
                const response = await fetch(`/gestor/encomendas/${encomendaId}/atualizar-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();

                if (data.success) {
                    // Mostrar mensagem de sucesso
                    this.showToast('Status atualizado com sucesso!', 'success');
                    // Recarregar encomendas e stats
                    this.loadEncomendas();
                    this.loadStats();
                } else {
                    // Mostrar erro
                    this.showToast(data.message || 'Erro ao atualizar status', 'error');
                }

            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao atualizar status', 'error');
            } finally {
                // Restaurar botão
                const button = event.target;
                button.disabled = false;
                button.textContent = originalText;
            }
        },

        async printEncomenda() {
            try {
                const encomendaId = this.selectedEncomenda?.id;
                if (!encomendaId) {
                    this.showToast('Erro: Encomenda não encontrada', 'error');
                    return;
                }

                // Mostrar loading
                const button = event.target;
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = '🖨️ Imprimindo...';

                // Fazer chamada AJAX para imprimir
                const response = await fetch(`/gestor/encomendas/${encomendaId}/imprimir`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Mostrar mensagem de sucesso
                    this.showToast(data.message, 'success');
                } else {
                    // Mostrar erro
                    this.showToast(data.message || 'Erro ao imprimir encomenda', 'error');
                }

                // Fechar modal
                this.showModal = false;

            } catch (error) {
                console.error('Erro:', error);
                this.showToast('Erro ao imprimir encomenda', 'error');
                // Fechar modal mesmo em caso de erro
                this.showModal = false;
            } finally {
                // Restaurar botão
                const button = event.target;
                button.disabled = false;
                button.textContent = originalText;
            }
        },

        showToast(message, type = 'info') {
            // Criar notificação temporária
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white font-medium z-50 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            toast.textContent = message;

            document.body.appendChild(toast);

            // Remover após 3 segundos
            setTimeout(() => {
                toast.remove();
            }, 3000);

            // Animação de entrada
            requestAnimationFrame(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            });
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

@media print {
    body * {
        visibility: hidden;
    }
    [x-show="showModal"] * {
        visibility: visible;
    }
}
</style>
@endsection
