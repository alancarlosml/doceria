@extends('layouts.admin')

@section('title', isset($user) ? 'Editar Usuário - ' . $user->name : 'Novo Usuário - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">{{ isset($user) ? '✏️' : '➕' }}</span>{{ isset($user) ? 'Editar Usuário' : 'Novo Usuário' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ isset($user) ? 'Atualize as informações e permissões do usuário.' : 'Cadastre um novo usuário no sistema da doceria.' }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">
                    @if(isset($user))
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Personal Information Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informações Pessoais</h3>

                                <!-- Name -->
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nome Completo <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $user->name ?? '') }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-300 @enderror"
                                        placeholder="Digite o nome completo"
                                        required
                                    >
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        E-mail <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $user->email ?? '') }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-300 @enderror"
                                        placeholder="usuario@exemplo.com"
                                        required
                                    >
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                    {{ isset($user) ? 'Alterar Senha' : 'Senha de Acesso' }}
                                    @if(isset($user))
                                        <span class="text-sm text-gray-500 font-normal">(deixe em branco para manter a atual)</span>
                                    @endif
                                </h3>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Senha <span class="text-red-500">*</span>
                                        @if(isset($user))
                                            <span class="text-gray-500">(apenas se quiser alterar)</span>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('password') border-red-300 @enderror"
                                        placeholder="Digite a senha"
                                        {{ isset($user) ? '' : 'required' }}
                                    >
                                    <p class="mt-1 text-sm text-gray-500">Mínimo 8 caracteres</p>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                        Confirmar Senha <span class="text-red-500">*</span>
                                        @if(isset($user))
                                            <span class="text-gray-500">(apenas se quiser alterar)</span>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('password_confirmation') border-red-300 @enderror"
                                        placeholder="Confirme a senha"
                                        {{ isset($user) ? '' : 'required' }}
                                    >
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Roles and Permissions Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Funções e Permissões</h3>

                                <!-- Roles Selection -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Funções <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @foreach($roles as $role)
                                            <label class="relative flex items-center">
                                                <input
                                                    type="checkbox"
                                                    name="roles[]"
                                                    value="{{ $role->id }}"
                                                    class="sr-only peer"
                                                    @if(isset($user) && $user->hasRole($role->name)) checked @endif
                                                >
                                                <div class="w-5 h-5 bg-gray-200 border-2 border-gray-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center peer-checked:border-2">
                                                    <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <span class="ml-3 text-sm font-medium text-gray-700 capitalize">
                                                    @switch($role->name)
                                                        @case('admin')
                                                            👑 Administrador
                                                            @break
                                                        @case('gestor')
                                                            👔 Gestor
                                                            @break
                                                        @case('atendente')
                                                            👨‍💼 Atendente
                                                            @break
                                                        @default
                                                            {{ ucfirst($role->name) }}
                                                    @endswitch
                                                    
                                                    <span class="block text-xs text-gray-500 font-normal">
                                                        @switch($role->name)
                                                            @case('admin')
                                                                Acesso total ao sistema
                                                                @break
                                                            @case('gestor')
                                                                Controle operacional
                                                                @break
                                                            @case('atendente')
                                                                Vendas e atendimento
                                                                @break
                                                        @endswitch
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Selecione uma ou mais funções para o usuário</p>
                                    @error('roles')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Account Status -->
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Status da Conta</h3>

                                <!-- Active Status -->
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="active"
                                        name="active"
                                        value="1"
                                        class="sr-only peer"
                                        {{ old('active', $user->active ?? true) ? 'checked' : '' }}
                                    >
                                    <label for="active" class="relative inline-flex items-center cursor-pointer">
                                        <div class="w-11 h-6 bg-gray-200 border-2 border-gray-300 rounded-full peer-checked:bg-green-600 peer-checked:border-green-600 transition-colors duration-200">
                                            <div class="w-5 h-5 bg-white rounded-full shadow-md transform translate-x-0 peer-checked:translate-x-5 transition-transform duration-200"></div>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">
                                            Conta Ativa
                                        </span>
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Usuários ativos podem acessar o sistema normalmente. Desative para bloquear o acesso.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ isset($user) ? 'Salvar Alterações' : 'Criar Usuário' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Role Permissions Info -->
            @if(count($roles) > 0)
            <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Sobre as funções do sistema:
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul role="list" class="list-disc pl-5 space-y-1">
                                <li><strong>Administrador:</strong> Acesso total ao sistema, incluindo criação de usuários, alteração de permissões e todas as funcionalidades.</li>
                                <li><strong>Gestor:</strong> Controle operacional da doceria, vendas, produtos, mesas, clientes e relatórios.</li>
                                <li><strong>Atendente:</strong> Funcionalidades básicas de vendas, atendimento ao cliente e operações do dia a dia.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</main>

<!-- JavaScript for form enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility functionality could be added here
    console.log('User form loaded successfully');
});
</script>
@endsection
