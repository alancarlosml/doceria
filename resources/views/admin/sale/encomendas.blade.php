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
                    <a href="{{ route('sales.pos') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
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
                                                        @click.stop="updateStatus(encomenda.id, 'entregue')"
                                                        x-show="encomenda.status === 'pronto'"
                                                        class="px-3 py-1 bg-purple-500 text-white text-xs rounded hover:bg-purple-600 transition-colors"
                                                    >
                                                        📦 Marcar Entregue
                                                    </button>

                                                    <button
                                                        @click.stop="showEncomendaDetails(encomenda)"
                                                        class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600 transition-colors"
                                                    >
                                                        👁️ Detalhes
                                                    </button>
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
                            <button
                                @click="printEncomenda()"
                                class="flex-1 bg-blue-500 text-white py-3 rounded-lg font-bold hover:bg-blue-600 transition-colors"
                            >
                                🖨️ Imprimir
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
        selectedEncomenda: null,

        init() {
            this.loadEncomendas();
            this.loadStats();
        },

        async loadEncomendas() {
            // TODO: Implementar busca real
            // Dados de exemplo
            this.groupedEncomendas = [
                {
                    date: '2024-01-15',
                    date_formatted: 'Segunda-feira, 15 de Janeiro de 2024',
                    encomendas: [
                        {
                            id: 1,
                            code: 'ENC-001',
                            status: 'pendente',
                            customer_name: 'Maria Silva',
                            customer_phone: '(98) 98765-4321',
                            delivery_time: '14:00',
                            delivery_address: 'Rua das Flores, 123',
                            items_summary: '2x Bolo de Chocolate, 1x Brigadeiro Gourmet (50un)',
                            total: 150.00,
                            total_formatted: '150,00',
                            notes: 'Cliente prefere chocolate meio amargo',
                            items: [
                                { id: 1, product_name: 'Bolo de Chocolate', quantity: 2, unit_price: 50, unit_price_formatted: '50,00', subtotal: 100, subtotal_formatted: '100,00', notes: '' },
                                { id: 2, product_name: 'Brigadeiro Gourmet (50un)', quantity: 1, unit_price: 50, unit_price_formatted: '50,00', subtotal: 50, subtotal_formatted: '50,00', notes: '' }
                            ],
                            subtotal: 150,
                            subtotal_formatted: '150,00',
                            discount: 0,
                            discount_formatted: '0,00',
                            delivery_date_formatted: '15/01/2024'
                        }
                    ]
                }
            ];
        },

        async loadStats() {
            // TODO: Implementar busca real de estatísticas
            this.stats = {
                pendentes: 5,
                em_producao: 2,
                hoje: 3,
                valor_total: '1.250,00'
            };
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
            // TODO: Implementar atualização real
            console.log('Atualizando encomenda', encomendaId, 'para status', newStatus);
            this.loadEncomendas();
        },

        printEncomenda() {
            window.print();
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