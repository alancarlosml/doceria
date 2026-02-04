@extends('layouts.admin')

@section('title', 'Gerenciar Permissões - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">🔐</span>Gerenciar Permissões
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Configure permissões por role para controlar o acesso ao sistema.
                    </p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Permissões</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $permissions->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Roles</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $roles->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Usuários com Acesso</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\User::where('active', true)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions by Module -->
            @foreach($permissionsByModule as $module => $modulePermissions)
                <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            📁 {{ Str::title(str_replace(['_', '-'], ' ', $module)) }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $modulePermissions->count() }} permissões neste módulo
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Permissão
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Admin
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gestor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Atendente
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ação
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($modulePermissions as $permission)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $permission->label }}</div>
                                                <div class="text-xs text-gray-500">{{ $permission->name }}</div>
                                            </div>
                                        </td>

                                        <!-- Admin Role -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center items-center">
                                                <button onclick="toggleRolePermission({{ $roles->firstWhere('name', 'admin')->id }}, {{ $permission->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $permission->roles->contains($roles->firstWhere('name', 'admin')) ? 'bg-green-600' : 'bg-gray-200' }}" type="button">
                                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $permission->roles->contains($roles->firstWhere('name', 'admin')) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Gestor Role -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center items-center">
                                                <button onclick="toggleRolePermission({{ $roles->firstWhere('name', 'gestor')->id }}, {{ $permission->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $permission->roles->contains($roles->firstWhere('name', 'gestor')) ? 'bg-green-600' : 'bg-gray-200' }}" type="button">
                                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $permission->roles->contains($roles->firstWhere('name', 'gestor')) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Atendente Role -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center items-center">
                                                <button onclick="toggleRolePermission({{ $roles->firstWhere('name', 'atendente')->id }}, {{ $permission->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $permission->roles->contains($roles->firstWhere('name', 'atendente')) ? 'bg-green-600' : 'bg-gray-200' }}" type="button">
                                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $permission->roles->contains($roles->firstWhere('name', 'atendente')) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <a href="{{ route('permissions.show', $permission) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                Ver detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <!-- Role Summary -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Resumo por Role</h3>
                    <p class="mt-1 text-sm text-gray-600">Total de permissões atribuídas a cada role</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                                        @if($role->name === 'admin') bg-red-100
                                        @elseif($role->name === 'gestor') bg-yellow-100
                                        @else bg-blue-100
                                        @endif">
                                        <span class="text-lg">
                                            @if($role->name === 'admin') 👑
                                            @elseif($role->name === 'gestor') 👔
                                            @else 👤
                                            @endif
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $role->label }}</h4>
                                        <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold text-gray-900">{{ $role->permissions->count() }}</span> permissões
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
async function toggleRolePermission(roleId, permissionId) {
    try {
        // Get current permissions for this role
        const rolePermissionsUrl = '{{ route("permissions.role-permissions", ["role" => "PLACEHOLDER"]) }}'.replace('PLACEHOLDER', roleId);
        const response = await fetch(rolePermissionsUrl, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();
        const currentPermissions = data.permissions || [];
        const hasPermission = currentPermissions.includes(permissionId);

        // Toggle the permission
        const newPermissions = hasPermission
            ? currentPermissions.filter(p => p !== permissionId)
            : [...currentPermissions, permissionId];

        // Update the role's permissions
        const updateResponse = await fetch('{{ route('permissions.assign-to-role') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                role_id: roleId,
                permission_ids: newPermissions
            })
        });

        const result = await updateResponse.json();

        if (result.success) {
            location.reload();
        } else {
            alert('Erro ao atualizar permissões: ' + (result.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erro ao processar requisição');
    }
}
</script>
@endsection
