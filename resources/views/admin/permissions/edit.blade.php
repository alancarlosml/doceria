@extends('layouts.admin')

@section('title', 'Editar Permissão - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">✏️</span>Editar Permissão
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Atualize as informações da permissão
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('permissions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="{{ route('permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nome da Permissão <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $permission->name) }}" 
                                    required 
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-300 @enderror"
                                    placeholder="Ex: products.view"
                                >
                                <p class="mt-1 text-xs text-gray-500">Nome único da permissão (formato: module.action)</p>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Label -->
                            <div class="sm:col-span-2">
                                <label for="label" class="block text-sm font-medium text-gray-700">
                                    Rótulo <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="label" 
                                    name="label" 
                                    value="{{ old('label', $permission->label) }}" 
                                    required 
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('label') border-red-300 @enderror"
                                    placeholder="Ex: Visualizar Produtos"
                                >
                                <p class="mt-1 text-xs text-gray-500">Nome legível da permissão</p>
                                @error('label')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Module -->
                            <div>
                                <label for="module" class="block text-sm font-medium text-gray-700">
                                    Módulo <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="module" 
                                    name="module" 
                                    value="{{ old('module', $permission->module) }}" 
                                    required 
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('module') border-red-300 @enderror"
                                    placeholder="Ex: products"
                                >
                                <p class="mt-1 text-xs text-gray-500">Módulo ao qual a permissão pertence</p>
                                @error('module')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Action -->
                            <div>
                                <label for="action" class="block text-sm font-medium text-gray-700">
                                    Ação <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="action" 
                                    name="action" 
                                    value="{{ old('action', $permission->action) }}" 
                                    required 
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('action') border-red-300 @enderror"
                                    placeholder="Ex: view"
                                >
                                <p class="mt-1 text-xs text-gray-500">Ação da permissão (view, create, update, delete)</p>
                                @error('action')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Descrição
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="3"
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('description') border-red-300 @enderror"
                                    placeholder="Descrição opcional da permissão"
                                >{{ old('description', $permission->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Atualizar Permissão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
