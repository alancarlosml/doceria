@extends('layouts.app')

@section('title', 'Login - Doce Doce Brigaderia')

@section('content')
<div class="min-h-screen bg-blue-light flex items-center justify-center px-4">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-white rounded-full shadow-lg flex items-center justify-center mb-6">
                <span class="text-3xl">🍰</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">
                Área do Gestor
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Entre com suas credenciais para acessar o painel administrativo
            </p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('gestor.login.submit') }}" class="bg-white py-8 px-6 shadow-lg rounded-lg space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-400">📧</span>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="seu@email.com"
                    >
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Senha
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-400">🔒</span>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="••••••••"
                    >
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 transform hover:scale-[1.02]"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        🔐
                    </span>
                    Entrar no Sistema
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>

            <!-- Back to Menu -->
            <div class="text-center">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar ao Cardápio
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
