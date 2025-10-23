@extends('layouts.admin')

@section('title', 'Detalhes do Motoboy - ' . $motoboy->name . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">🏍️</span>Detalhes do Motoboy
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informações completas sobre o {{ $motoboy->name }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('motoboys.edit', $motoboy) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Motoboy
                    </a>

                    <a href="{{ route('motoboys.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Motoboy Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Motoboy Details Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Informações Pessoais</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nome</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->phone }}</dd>
                            </div>
                            @if($motoboy->cpf)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CPF</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->cpf }}</dd>
                            </div>
                            @endif
                            @if($motoboy->cnh)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CNH</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->cnh }}</dd>
                            </div>
                            @endif
                            @if($motoboy->placa_veiculo)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Placa do Veículo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->placa_veiculo }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $motoboy->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <span class="w-2 h-2 mr-1 rounded-full {{ $motoboy->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $motoboy->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->created_at->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $motoboy->updated_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">📊</span> Estatísticas de Entrega
                        </h3>
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de Entregas</dt>
                                <dd class="mt-1 text-3xl font-bold text-green-600">{{ $totalDeliveries }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Ganho</dt>
                                <dd class="mt-1 text-2xl font-bold text-blue-600">R$ {{ number_format($totalEarnings, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Média por Entrega</dt>
                                <dd class="mt-1 text-xl font-semibold text-purple-600">
                                    {{ $totalDeliveries > 0 ? 'R$ ' . number_format($totalEarnings / $totalDeliveries, 2, ',', '.') : 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Recent Deliveries Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6 flex items-center">
                            <span class="mr-2">🕒</span> Entregas Recentes
                        </h3>

                        @if($recentDeliveries->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($recentDeliveries as $delivery)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Pedido #{{ $delivery->id }}</p>
                                            <p class="text-xs text-gray-500">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-xs text-gray-600">{{ $delivery->type === 'delivery' ? 'Entrega' : 'Outro' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-green-600">R$ {{ number_format($delivery->total, 2, ',', '.') }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Concluída
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($totalDeliveries > 10)
                                <div class="mt-4 text-center">
                                    <p class="text-sm text-gray-500">
                                        Mostrando as últimas 10 entregas de um total de {{ $totalDeliveries }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">🚚</div>
                                <p class="text-sm text-gray-500">Nenhuma entrega realizada ainda</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
