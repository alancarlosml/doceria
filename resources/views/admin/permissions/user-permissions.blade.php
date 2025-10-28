@extends('layouts.admin')

@section('title', 'Permissões de ' . $user->name . ' - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">🔐</span>Permissões de {{ $user->name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie permissões individuais e roles do usuário
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

            @include('includes.flash-messages')

            <!-- User Info & Roles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- User Information -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">👤 Informações do Usuário</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <span class="text-2xl font-semibold text-blue-800">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                <div class="mt-1">
                                    @if($user->active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inativo</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Roles -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-medium text-gray-900">🏷️ Roles Atuais</h3>
                    </div>
                    <div class="p-6">
                        @if($userRoles->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($userRoles as $role)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $role->label }}</span>
                                            <p class="text-xs text-gray-600">{{ $role->description }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($role->name === 'admin') bg-red-100 text-red-800
                                            @elseif($role->name === 'gestor') bg-yellow-100 text-yellow-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ $role->name }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma role atribuída</h3>
                                <p class="mt-1 text-sm text-gray-500">Este usuário não tem roles atualmente</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permissions Management -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                    <h3 class="text-lg font-medium text-gray-900">🔒 Permissões Individuais</h3>
                    <p class="mt-1 text-sm text-gray-600">Selecionar permissões diretamente para este usuário</p>
                </div>

                <form id="permissionsForm" action="{{ route('users.permissions.assign', $user) }}" method="POST">
                    @csrf

                    <div class="p-6">
                        <!-- Role Permissions Alert -->
                        @if($userRoles->isNotEmpty())
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-800">
                                            <strong>Atenção:</strong> Este usuário também possui permissões através de roles. As permissões selecionadas abaixo serão adicionadas às permissões do(s) role(s) ou podem substituir permissões específicas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Permissions by Module -->
                        @php
                            $grantedByRoles = $user->roles->flatMap(fn($role) => $role->permissions)->unique('id')->pluck('id')->toArray();
                        @endphp

                        <div class="space-y-6">
                            @foreach($permissionsByModule as $module => $permissions)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                        <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                            {{ Str::title(str_replace(['_', '-'], ' ', $module)) }}
                                        </h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $permissions->count() }} permissões disponíveis
                                        </p>
                                    </div>

                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($permissions as $permission)
                                                <label class="inline-flex items-start cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        name="permission_ids[]"
                                                        value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}
                                                        class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 mt-0.5"
                                                    >
                                                    <div class="ml-2">
                                                        <span class="text-sm font-medium text-gray-900 block">{{ $permission->label }}</span>
                                                        <span class="text-xs text-gray-500 block">[{{ $permission->module }}.{{ $permission->action }}]</span>

                                                        @if(in_array($permission->id, $grantedByRoles))
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                                Via Role
                                                            </span>
                                                        @elseif(in_array($permission->id, $userPermissions))
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                                Direta
                                                            </span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                💾 Salvar Permissões
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Remove Permissions Section -->
            <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <h3 class="text-lg font-medium text-gray-900">🗑️ Remover Permissões Específicas</h3>
                    <p class="mt-1 text-sm text-gray-600">Para remover permissões diretas atribuídas especificamente a este usuário</p>
                </div>

                <div class="p-6">
                    @if($userPermissions)
                        <div class="space-y-2">
                            @foreach($userPermissions as $permissionId)
                                @php
                                    $permission = \App\Models\Permission::find($permissionId);
                                    $isGrantedByRole = in_array($permissionId, $grantedByRoles);
                                @endphp
                                @if($permission)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg {{ $isGrantedByRole ? 'opacity-60' : '' }}">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $permission->label }}</span>
                                            <span class="ml-2 text-xs text-gray-500">[{{ $permission->module }}.{{ $permission->action }}]</span>

                                            @if($isGrantedByRole)
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Via Role - Não Removível
                                                </span>
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Permissão Direta
                                                </span>
                                            @endif
                                        </div>

                                        @if(!$isGrantedByRole)
                                            <button
                                                onclick="removePermission({{ $permission->id }}, '{{ $permission->label }}')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remover
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma permissão direta atribuída</h3>
                            <p class="mt-1 text-sm text-gray-500">Este usuário recebe permissões apenas através das roles atribuídas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Permission Assignment Form
    const form = document.getElementById('permissionsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Permissões salvas com sucesso!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Erro ao salvar permissões', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao processar requisição', 'error');
            });
        });
    }

    // Remove specific permissions
    window.removePermission = function(permissionId, permissionLabel) {
        if (confirm(`Tem certeza que deseja remover a permissão "${permissionLabel}"?\n\nEsta ação afetará apenas este usuário.`)) {
            const formData = new FormData();
            formData.append('permission_id', permissionId);

            fetch(`/permissions/users/${{ $user->id }}/permissions/remove`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Permissão removida com sucesso!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Erro ao remover permissão', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao processar requisição', 'error');
            });
        }
    };

    // Notification helper
    function showNotification(message, type) {
        // Simple alert for now - could be enhanced with toast notifications
        alert(message);
    }
});
</script>
@endsection
