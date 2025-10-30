@extends('layouts.admin')

@section('title', isset($table) ? 'Editar Mesa ' . $table->number : 'Nova Mesa' . ' - Doce Doce Brigaderia')

@section('admin-content')
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="md:flex-1 min-w-0">
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                        <span class="mr-3">{{ isset($table) ? '✏️' : '➕' }}</span>
                        {{ isset($table) ? 'Editar Mesa ' . $table->number : 'Nova Mesa' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ isset($table) ? 'Atualize as informações da mesa' : 'Configure uma nova mesa para o estabelecimento' }}
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('tables.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST"
                      action="{{ isset($table) ? route('tables.update', $table) : route('tables.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($table))
                        @method('PUT')
                    @endif

                    <div class="px-6 py-6 space-y-6">
                        <!-- Mesa Preview Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-2xl">🪑</span>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <div class="flex items-center">
                                        <h3 class="text-lg font-medium text-gray-900 mr-4">
                                            Mesa {{ old('number', isset($table) ? $table->number : '') ?: 'a ser definida' }}
                                        </h3>
                                        @if(isset($table))
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                @if($table->status === 'disponivel') bg-green-100 text-green-800
                                                @elseif($table->status === 'ocupada') bg-red-100 text-red-800
                                                @elseif($table->status === 'reservada') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($table->status) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-blue-600 mt-1">
                                        Capacidade: {{ old('capacity', isset($table) ? $table->capacity : 0) ?: 'a ser definida' }} pessoas
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Número da Mesa -->
                            <div>
                                <label for="number" class="block text-sm font-medium text-gray-700">
                                    Número da Mesa <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="number"
                                    id="number"
                                    value="{{ old('number', isset($table) ? $table->number : '') }}"
                                    placeholder="Ex: 01, 02, VIP-01..."
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    required
                                >
                                @error('number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Identificador único da mesa (números, letras, ou combinação)
                                </p>
                            </div>

                            <!-- Capacidade -->
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700">
                                    Capacidade <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="capacity"
                                    id="capacity"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    @for($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}"
                                                {{ old('capacity', isset($table) ? $table->capacity : null) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i === 1 ? 'pessoa' : 'pessoas' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('capacity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Número máximo de pessoas que podem ocupar a mesa
                                </p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-3">
                                Status Inicial <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="disponivel"
                                        {{ old('status', isset($table) ? $table->status : 'disponivel') === 'disponivel' ? 'checked' : '' }}
                                        class="sr-only peer"
                                    >
                                    <div class="w-full p-4 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50 transition-colors">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center mr-3">
                                                <span class="text-white">✅</span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">Disponível</div>
                                                <div class="text-sm text-gray-600">Livre para uso</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="ocupada"
                                        {{ old('status', isset($table) ? $table->status : null) === 'ocupada' ? 'checked' : '' }}
                                        class="sr-only peer"
                                    >
                                    <div class="w-full p-4 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 transition-colors">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center mr-3">
                                                <span class="text-white">🔴</span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">Ocupada</div>
                                                <div class="text-sm text-gray-600">Em atendimento</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="reservada"
                                        {{ old('status', isset($table) ? $table->status : null) === 'reservada' ? 'checked' : '' }}
                                        class="sr-only peer"
                                    >
                                    <div class="w-full p-4 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-colors">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center mr-3">
                                                <span class="text-white">📅</span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">Reservada</div>
                                                <div class="text-sm text-gray-600">Agendada</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Ativo -->
                        <div>
                            <label for="active" class="block text-sm font-medium text-gray-700 mb-3">
                                Status da Mesa
                            </label>
                            <div class="flex items-center">
                                <input
                                    id="active"
                                    name="active"
                                    type="checkbox"
                                    {{ old('active', isset($table) ? $table->active : true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="active" class="ml-2 block text-sm text-gray-900">
                                    Mesa ativa
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Desmarque para inativar a mesa (não será exibida para seleção)
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <a href="{{ route('tables.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="mr-2">{{ isset($table) ? '💾' : '➕' }}</span>
                            {{ isset($table) ? 'Atualizar Mesa' : 'Criar Mesa' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100">
                            <span class="text-blue-600">💡</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-blue-800">Dicas sobre mesas</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Use números ou códigos únicos para identificar mesas (ex: 01, 02, VIP-01)</li>
                                <li>A capacidade máxima de uma mesa pode ser ajustada conforme necessidade</li>
                                <li>Mesas desativadas não aparecem na seleção do PDV</li>
                                <li>O status inicial será o estado padrão da mesa quando criada</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection