@extends('layouts.admin')

@section('title', 'Detalhes da Permissão - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">🔍</span>Detalhes da Permissão
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Informações completas sobre a permissão
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('permissions.edit', $permission) }}" class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('permissions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Permission Details -->
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <!-- Name -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome da Permissão</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $permission->name }}</dd>
                        </div>

                        <!-- Label -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rótulo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $permission->label }}</dd>
                        </div>

                        <!-- Module -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Módulo</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ Str::title(str_replace(['_', '-'], ' ', $permission->module)) }}
                                </span>
                            </dd>
                        </div>

                        <!-- Action -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ação</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ Str::title($permission->action) }}
                                </span>
                            </dd>
                        </div>

                        <!-- Description -->
                        @if($permission->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Descrição</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $permission->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Roles with this Permission -->
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Roles com esta Permissão</h3>
                    @if($permission->roles->count() > 0)
                        <div class="space-y-2">
                            @foreach($permission->roles as $role)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $role->label ?? Str::title($role->name) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $role->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Nenhuma role possui esta permissão.</p>
                    @endif
                </div>
            </div>

            <!-- Users with this Permission -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Usuários com esta Permissão</h3>
                    @if($permission->users->count() > 0)
                        <div class="space-y-2">
                            @foreach($permission->users as $user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                        <span class="ml-2 text-sm text-gray-500">({{ $user->email }})</span>
                                    </div>
                                    @if($user->active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Nenhum usuário possui esta permissão diretamente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
