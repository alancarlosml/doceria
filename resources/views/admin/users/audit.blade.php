@extends('layouts.admin')

@section('title', 'Auditoria de Permissões - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">📋</span>Auditoria de Permissões
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Histórico de todas as alterações de permissões e roles.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar para Usuários
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="action_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ação</label>
                        <select id="action_type" name="action_type" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Todos</option>
                            <option value="permission_granted">Permissão Concedida</option>
                            <option value="permission_revoked">Permissão Revogada</option>
                            <option value="role_assigned">Role Atribuída</option>
                            <option value="role_removed">Role Removida</option>
                        </select>
                    </div>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                        <select id="user_id" name="user_id" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Todos os usuários</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                        <input
                            type="date"
                            id="date_from"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                        <input
                            type="date"
                            id="date_to"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div class="flex items-end space-x-2 md:col-span-4">
                        <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            🔄 Filtrar
                        </button>
                        @if(request()->hasAny(['action_type', 'user_id', 'date_from', 'date_to']))
                            <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                🗑️ Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Audit Log -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Log de Auditoria</h3>
                    <p class="mt-1 text-sm text-gray-600">Histórico de alterações de permissões e roles</p>
                </div>

                <div class="border-t border-gray-200">
                    @if($audits->isEmpty())
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum registro de auditoria</h3>
                            <p class="mt-1 text-sm text-gray-500">Ainda não foram realizadas alterações de permissões.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data/Hora
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ação
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usuário
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alterado Por
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Detalhes
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($audits as $audit)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $audit->created_at->format('d/m/Y H:i') }}
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($audit->action_type)
                                                    @case('permission_granted')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            ✅ Permissão Concedida
                                                        </span>
                                                    @break
                                                    @case('permission_revoked')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            ❌ Permissão Revogada
                                                        </span>
                                                    @break
                                                    @case('role_assigned')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            ➕ Role Atribuída
                                                        </span>
                                                    @break
                                                    @case('role_removed')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            ➖ Role Removida
                                                        </span>
                                                    @break
                                                @endswitch
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $audit->user->name }}
                                                <div class="text-xs text-gray-500">{{ $audit->user->email }}</div>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($audit->performedBy)
                                                    {{ $audit->performedBy->name }}
                                                    <div class="text-xs text-gray-500">{{ $audit->performedBy->email }}</div>
                                                @else
                                                    <span class="text-gray-500 italic">Sistema</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @if($audit->permission)
                                                    <div>
                                                        <span class="font-medium">{{ $audit->permission->label }}</span>
                                                        <div class="text-xs text-gray-500">{{ $audit->permission->name }}</div>
                                                    </div>
                                                @elseif($audit->role)
                                                    <div>
                                                        <span class="font-medium">{{ $audit->role->label }}</span>
                                                        <div class="text-xs text-gray-500">{{ $audit->role->name }}</div>
                                                    </div>
                                                @else
                                                    @if($audit->details)
                                                        <span class="text-xs text-gray-500">{{ is_array($audit->details) ? json_encode($audit->details) : $audit->details }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <p class="text-sm text-gray-700 leading-5">
                                Mostrando
                                <span class="font-medium">{{ $audits->firstItem() }}</span>
                                a
                                <span class="font-medium">{{ $audits->lastItem() }}</span>
                                de
                                <span class="font-medium">{{ $audits->total() }}</span>
                                resultados
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
