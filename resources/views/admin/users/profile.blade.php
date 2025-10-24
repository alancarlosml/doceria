@extends('layouts.admin')

@section('title', 'Meu Perfil - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">👤</span>Meu Perfil
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie suas informações pessoais e de acesso.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('gestor.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Current User Info -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                                    <span class="mr-3">👤</span>Informações do Usuário
                                </h3>

                                <div class="flex items-center mb-4">
                                    <div class="h-16 w-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                        <span class="text-2xl font-bold text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
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
                            </div>

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
                                        value="{{ old('name', $user->name) }}"
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
                                        value="{{ old('email', $user->email) }}"
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
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Alterar Senha</h3>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                <strong>Importante:</strong> Deixe os campos de senha em branco se não quiser alterar a senha atual.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nova Senha
                                        @if(Auth::user()->hasRole('admin'))
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('password') border-red-300 @enderror"
                                        placeholder="Digite a nova senha"
                                        {{ Auth::user()->hasRole('admin') ? 'required' : '' }}
                                        minlength="8"
                                    >
                                    <p class="mt-1 text-sm text-gray-500">Mínimo 8 caracteres</p>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                        Confirmar Nova Senha
                                        @if(Auth::user()->hasRole('admin'))
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('password_confirmation') border-red-300 @enderror"
                                        placeholder="Confirme a nova senha"
                                        {{ Auth::user()->hasRole('admin') ? 'required' : '' }}
                                        minlength="8"
                                    >
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Administrator Settings -->
                            @if(Auth::user()->hasRole('admin'))
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                                    <span class="mr-3">🔐</span>Configurações Avançadas (Administrador)
                                </h3>

                                <!-- Roles Selection -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Seus Papéis/Funções
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @foreach($roles as $role)
                                            <label class="relative flex items-center">
                                                <input
                                                    type="checkbox"
                                                    name="roles[]"
                                                    value="{{ $role->id }}"
                                                    class="sr-only peer"
                                                    @if($user->hasRole($role->name)) checked @endif
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
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Selecione uma ou mais funções para sua conta</p>
                                    @error('roles')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Account Status (Admin Only) -->
                                <div>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="active"
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('active', $user->active) ? 'checked' : '' }}
                                        >
                                        <div class="w-5 h-5 bg-gray-200 border-2 border-gray-300 rounded peer-checked:bg-green-600 transition-colors duration-200"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">
                                            Conta Ativa
                                        </span>
                                    </label>
                                    <p class="mt-1 text-sm text-gray-500">Desative sua conta apenas se necessário (isso bloqueará seu acesso)</p>
                                </div>
                            </div>
                            @else
                                <!-- Non-Admin Users -->
                                <div class="pb-4 border-b border-gray-200">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                                        <span class="mr-3">ℹ️</span>Sobre suas Permissões
                                    </h3>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center mb-3">
                                            <span class="text-2xl mr-3">{{ $user->roles->first() ? ($user->hasRole('gestor') ? '👔' : '👨‍💼') : '🙋‍♂️' }}</span>
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">
                                                    Você é um {{ $user->hasRole('gestor') ? 'Gestor' : 'Atendente' }}
                                                </h4>
                                                <p class="text-sm text-gray-600">
                                                    {{ $user->hasRole('gestor') ? 'Você tem acesso a vendas, produtos, mesas e controle operacional.' : 'Você tem acesso básico para atendimento e vendas.' }}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            Somente administradores podem alterar suas permissões ou outras configurações avançadas.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('gestor.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript for animations and enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any client-side validation or enhancements here
    console.log('Profile form loaded successfully');

    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    if (password && passwordConfirmation) {
        passwordConfirmation.addEventListener('input', function() {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('As senhas não coincidem');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        });

        password.addEventListener('input', function() {
            if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('As senhas não coincidem');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        });
    }
});
</script>
@endsection
