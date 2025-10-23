@extends('layouts.admin')

@section('title', $isEditing ? 'Editar Cliente - ' . $customer->name : 'Novo Cliente - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">{{ $isEditing ? '✏️' : '👥' }}</span>{{ $isEditing ? 'Editar Cliente' : 'Novo Cliente' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isEditing ? 'Atualize as informações do cliente' : 'Adicione um novo cliente ao sistema da doceria' }}
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form method="POST" action="{{ $isEditing ? route('customers.update', $customer) : route('customers.store') }}">
                    @if($isEditing)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Personal Information Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informações Pessoais</h3>

                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nome Completo <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ $isEditing ? old('name', $customer->name) : old('name') }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-300 @enderror"
                                        placeholder="Ex: João Silva"
                                        required
                                    >
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informações de Contato</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Phone -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                            Telefone
                                        </label>
                                        <input
                                            type="text"
                                            id="phone"
                                            name="phone"
                                            value="{{ $isEditing ? old('phone', $customer->phone) : old('phone') }}"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('phone') border-red-300 @enderror"
                                            placeholder="Ex: (11) 99999-9999"
                                        >
                                        @error('phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                            E-mail
                                        </label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ $isEditing ? old('email', $customer->email) : old('email') }}"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-300 @enderror"
                                            placeholder="Ex: joao@email.com"
                                        >
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Section -->
                            <div class="pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Documentos</h3>

                                <!-- CPF -->
                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">
                                        CPF
                                    </label>
                                    <input
                                        type="text"
                                        id="cpf"
                                        name="cpf"
                                        value="{{ $isEditing ? old('cpf', $customer->cpf) : old('cpf') }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('cpf') border-red-300 @enderror"
                                        placeholder="Ex: 123.456.789-00"
                                    >
                                    @error('cpf')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address Information Section -->
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informações de Endereço</h3>

                                <!-- Address -->
                                <div class="mb-6">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Endereço
                                    </label>
                                    <input
                                        type="text"
                                        id="address"
                                        name="address"
                                        value="{{ $isEditing ? old('address', $customer->address) : old('address') }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('address') border-red-300 @enderror"
                                        placeholder="Ex: Rua das Flores, 123"
                                    >
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <!-- Neighborhood -->
                                    <div>
                                        <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-1">
                                            Bairro
                                        </label>
                                        <input
                                            type="text"
                                            id="neighborhood"
                                            name="neighborhood"
                                            value="{{ $isEditing ? old('neighborhood', $customer->neighborhood) : old('neighborhood') }}"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('neighborhood') border-red-300 @enderror"
                                            placeholder="Ex: Centro"
                                        >
                                        @error('neighborhood')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- City -->
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cidade
                                        </label>
                                        <input
                                            type="text"
                                            id="city"
                                            name="city"
                                            value="{{ $isEditing ? old('city', $customer->city) : old('city') }}"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('city') border-red-300 @enderror"
                                            placeholder="Ex: São Paulo"
                                        >
                                        @error('city')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- State -->
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                                            Estado
                                        </label>
                                        <select
                                            id="state"
                                            name="state"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('state') border-red-300 @enderror"
                                        >
                                            <option value="">Selecione...</option>
                                            <option value="AC" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'AC' ? 'selected' : '' }}>Acre</option>
                                            <option value="AL" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                            <option value="AP" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                            <option value="AM" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                            <option value="BA" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                            <option value="CE" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                            <option value="DF" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                            <option value="ES" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                            <option value="GO" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                            <option value="MA" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                            <option value="MT" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                            <option value="MS" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                            <option value="MG" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                            <option value="PA" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'PA' ? 'selected' : '' }}>Pará</option>
                                            <option value="PB" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                            <option value="PR" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                            <option value="PE" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                            <option value="PI" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                            <option value="RJ" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                            <option value="RN" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                            <option value="RS" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                            <option value="RO" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                            <option value="RR" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                            <option value="SC" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                            <option value="SP" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                            <option value="SE" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                            <option value="TO" {{ ($isEditing ? old('state', $customer->state) : old('state')) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                        </select>
                                        @error('state')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- ZIP Code -->
                                    <div>
                                        <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-1">
                                            CEP
                                        </label>
                                        <input
                                            type="text"
                                            id="zipcode"
                                            name="zipcode"
                                            value="{{ $isEditing ? old('zipcode', $customer->zipcode) : old('zipcode') }}"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('zipcode') border-red-300 @enderror"
                                            placeholder="Ex: 01234-567"
                                        >
                                        @error('zipcode')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $isEditing ? 'Atualizar Cliente' : 'Criar Cliente' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript for form enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Phone mask
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 2) {
                    value = value;
                } else if (value.length <= 6) {
                    value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
                } else if (value.length <= 10) {
                    value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 6) + '-' + value.slice(6);
                } else {
                    value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
                }
                e.target.value = value;
            }
        });
    }

    // CPF mask
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + '.' + value.slice(3);
                } else if (value.length <= 9) {
                    value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6);
                } else {
                    value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6, 9) + '-' + value.slice(9);
                }
                e.target.value = value;
            }
        });
    }

    // ZIP Code mask
    const zipcodeInput = document.getElementById('zipcode');
    if (zipcodeInput) {
        zipcodeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                if (value.length <= 5) {
                    value = value;
                } else {
                    value = value.slice(0, 5) + '-' + value.slice(5);
                }
                e.target.value = value;
            }
        });
    }
});
</script>
@endsection
