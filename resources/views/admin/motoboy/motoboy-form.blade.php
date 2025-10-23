@extends('layouts.admin')

@section('title', $isEditing ? 'Editar Motoboy - Doce Doce Brigaderia' : 'Novo Motoboy - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $isEditing ? '🏍️ Editar Motoboy' : '➕ Novo Motoboy' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isEditing ? 'Atualize as informações do motoboy' : 'Adicione um novo motoboy ao sistema de entregas' }}
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('motoboys.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="{{ $isEditing ? route('motoboys.update', $motoboy) : route('motoboys.store') }}">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $isEditing ? $motoboy->name : '') }}" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="sm:col-span-1">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $isEditing ? $motoboy->phone : '') }}" required placeholder="(11) 99999-9999" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- CPF -->
                            <div class="sm:col-span-1">
                                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                                <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $isEditing ? $motoboy->cpf : '') }}" placeholder="000.000.000-00" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('cpf')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- CNH -->
                            <div class="sm:col-span-1">
                                <label for="cnh" class="block text-sm font-medium text-gray-700">CNH</label>
                                <input type="text" id="cnh" name="cnh" value="{{ old('cnh', $isEditing ? $motoboy->cnh : '') }}" placeholder="000000000-0" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('cnh')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Placa do Veículo -->
                            <div class="sm:col-span-1">
                                <label for="placa_veiculo" class="block text-sm font-medium text-gray-700">Placa do Veículo</label>
                                <input type="text" id="placa_veiculo" name="placa_veiculo" value="{{ old('placa_veiculo', $isEditing ? $motoboy->placa_veiculo : '') }}" placeholder="ABC-1234" maxlength="10" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('placa_veiculo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Active Status -->
                            <div class="sm:col-span-2">
                                <div class="flex items-center">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" id="active" name="active" value="1" {{ old('active', $isEditing ? $motoboy->active : true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="active" class="ml-2 block text-sm text-gray-900">
                                        Motoboy ativo para entregas
                                    </label>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Marque esta opção se o motoboy estiver disponível para realizar entregas.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('motoboys.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEditing ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M5 13l4 4L19 7' }}"></path>
                            </svg>
                            {{ $isEditing ? 'Atualizar Motoboy' : 'Salvar Motoboy' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
