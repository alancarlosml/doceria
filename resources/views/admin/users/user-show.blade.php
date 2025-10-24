@extends('layouts.admin')

@section('title', 'Usuário: ' . $user->name . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">{{ $user->active ? '👤' : '🚫' }}</span>Usuário: {{ $user->name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Detalhes completos do usuário e suas permissões no sistema.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>

                    @if(auth()->user()->hasPermission('users.view'))
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
                    @endif
                </div>
            </div>

            <!-- Status Banner -->
            <div class="bg-white shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                <span class="text-xl font-semibold text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    @if($user->active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Conta Ativa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Conta Inativa
                                        </span>
                                    @endif

                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($role->name === 'admin') bg-red-100 text-red-800
                                            @elseif($role->name === 'gestor') bg-yellow-100 text-yellow-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-gray-500">Membro desde</p>
                            <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Basic Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="mr-3">📋</span>Informações Básicas
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">ID:</dt>
                            <dd class="text-sm font-medium text-gray-900">#{{ $user->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Nome:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">E-mail:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status:</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $user->active ? 'Ativo' : 'Inativo' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Criado em:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Última atualização:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Roles and Permissions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="mr-3">🔐</span>Permissões
                    </h3>

                    <!-- Roles -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Funções:</h4>
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($role->name === 'admin') bg-red-100 text-red-800
                                    @elseif($role->name === 'gestor') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-500">Nenhuma função atribuída</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Direct Permissions -->
                    @if($user->permissions->count() > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Permissões Específicas:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->permissions as $permission)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- All Permissions -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Permissões Totais:
                            <span class="text-xs text-gray-500 font-normal">({{ $user->hasPermission('products.view') ? 'Vendas' : '' }}{{ $user->hasPermission('users.view') ? ', Usuários' : '' }}{{ $user->hasPermission('cash_registers.view') ? ', Caixas' : '' }}{{ $user->isAdminOrGestor() ? ', Administrativo' : '' }})</span>
                        </h4>
                        <div class="text-sm">
                            @if($user->isAdminOrGestor())
                                <span class="text-green-600 font-medium">✅ Acesso Administrativo Completo</span>
                            @else
                                <span class="text-blue-600 font-medium">✅ Acesso Básico ao Sistema</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Sales Activity -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Vendas Realizadas</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $user->sales()->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Cash Registers Opened -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Caixas Abertos</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $user->cashRegisters()->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                                <dd class="text-2xl font-semibold text-gray-900">R$ {{ number_format($user->sales()->whereNotIn('status', ['cancelado'])->sum('total'), 2, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            @if($user->sales->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Últimas Vendas</h3>
                    <p class="mt-1 text-sm text-gray-600">Histórico recente de vendas realizadas por este usuário.</p>
                </div>

                <div class="border-t border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Código
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($user->sales()->latest()->limit(10)->get() as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $sale->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                        {{ $sale->type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        R$ {{ number_format($sale->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($sale->status)
                                                @case('pendente') bg-yellow-100 text-yellow-800 @break
                                                @case('em_preparo') bg-orange-100 text-orange-800 @break
                                                @case('pronto') bg-blue-100 text-blue-800 @break
                                                @case('finalizado') bg-green-100 text-green-800 @break
                                                @case('cancelado') bg-red-100 text-red-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            @switch($sale->status)
                                                @case('pendente') Pendente @break
                                                @case('em_preparo') Em Preparo @break
                                                @case('pronto') Pronto @break
                                                @case('saiu_entrega') Saiu para Entrega @break
                                                @case('entregue') Entregue @break
                                                @case('finalizado') Finalizado @break
                                                @case('cancelado') Cancelado @break
                                                @default {{ $sale->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sale->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma venda realizada</h3>
                <p class="mt-1 text-sm text-gray-500">Este usuário ainda não realizou nenhuma venda.</p>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection
