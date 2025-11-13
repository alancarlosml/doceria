@extends('layouts.admin')

@section('title', $isEditing ? 'Editar Produto - Doce Doce Brigaderia' : 'Novo Produto - Doce Doce Brigaderia')

@section('admin-content')
@php
    // Determine form action and method
    $formAction = $isEditing && $product ? route('products.update', $product) : route('products.store');
    $formMethod = ($isEditing && $product) ? 'POST' : 'POST'; // Always POST, but add @method('PUT') when needed
    $usePutMethod = $isEditing && $product;
@endphp

<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $isEditing ? '✏️ Editar Produto' : '➕ Novo Produto' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isEditing ? 'Atualize as informações do produto' : 'Adicione um novo produto ao catálogo' }}
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
                    @csrf
                    @if($usePutMethod)
                        @method('PUT')
                    @endif

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $isEditing ? $product->name : '') }}" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Categoria</label>
                                <select id="category_id" name="category_id" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2">
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $isEditing ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
                                <input type="text" inputmode="decimal" id="price" name="price" value="{{ old('price', $isEditing && $product->price ? number_format($product->price, 2, ',', '.') : '') }}" required class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2" placeholder="0,00">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cost Price -->
                            <div class="opacity-50" title="Preço de custo (opcional)">
                                <label for="cost_price" class="block text-sm font-medium text-gray-700">Preço de Custo (R$)</label>
                                <input type="text" inputmode="decimal" id="cost_price" name="cost_price" value="{{ old('cost_price', $isEditing && $product->cost_price ? number_format($product->cost_price, 2, ',', '.') : '') }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2" placeholder="0,00" tabindex="-1">
                                @error('cost_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Active Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="active" value="1" {{ old('active', $isEditing ? $product->active : true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Produto ativo</span>
                                    </label>
                                </div>
                                @error('active')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                                <textarea id="description" name="description" rows="3" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2" placeholder="Descrição detalhada do produto...">{{ old('description', $isEditing ? $product->description : '') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image Upload -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Imagem do Produto</label>

                                <!-- Current Image Preview -->
                                @if($isEditing && $product->image)
                                    <div class="mt-2 mb-4">
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Imagem atual" class="h-32 w-32 object-cover rounded-lg border border-gray-200">
                                        <p class="mt-1 text-sm text-gray-500">Imagem atual</p>
                                    </div>
                                @endif

                                <!-- Upload Input -->
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Fazer upload de arquivo</span>
                                                <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">ou arraste e solte</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF até 2MB
                                        </p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEditing ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M5 13l4 4L19 7' }}"></path>
                            </svg>
                            {{ $isEditing ? 'Atualizar Produto' : 'Salvar Produto' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para aplicar máscara monetária brasileira
    function aplicarMascaraMonetaria(input) {
        let value = input.value;
        
        // Se já está formatado (tem vírgula), não reformata
        if (value.includes(',') && value.match(/^\d{1,3}(\.\d{3})*,\d{2}$/)) {
            return removerMascaraMonetaria(value);
        }
        
        // Remove tudo que não é número
        value = value.replace(/\D/g, '');
        
        // Converte para número e divide por 100 para ter centavos
        if (value === '') {
            input.value = '';
            return 0;
        }
        
        const number = parseFloat(value) / 100;
        
        // Formata como moeda brasileira
        input.value = number.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        return number;
    }

    // Função para remover máscara e retornar valor numérico
    function removerMascaraMonetaria(value) {
        if (!value) return 0;
        // Remove pontos e substitui vírgula por ponto
        const cleanValue = value.replace(/\./g, '').replace(',', '.');
        return parseFloat(cleanValue) || 0;
    }

    // Aplicar máscara nos campos monetários
    const camposMonetarios = ['price', 'cost_price'];
    
    camposMonetarios.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            // Aplicar máscara ao digitar
            element.addEventListener('input', function() {
                aplicarMascaraMonetaria(this);
            });

            // Aplicar máscara ao perder o foco
            element.addEventListener('blur', function() {
                aplicarMascaraMonetaria(this);
            });
        }
    });

    // Converter valores monetários antes de enviar o formulário
    const form = document.querySelector('form[method="POST"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Converter campos monetários para formato numérico (ponto decimal)
            camposMonetarios.forEach(fieldId => {
                const element = document.getElementById(fieldId);
                if (element && element.value && element.value.trim() !== '') {
                    // Remove pontos de milhar e substitui vírgula por ponto
                    let value = element.value.trim();
                    value = value.replace(/\./g, '').replace(',', '.');
                    // Garante que seja um número válido
                    const numericValue = parseFloat(value) || 0;
                    element.value = numericValue.toFixed(2);
                } else if (element && (!element.value || element.value.trim() === '')) {
                    // Se o campo estiver vazio e não for obrigatório, define como vazio
                    if (fieldId === 'cost_price') {
                        element.value = '';
                    }
                }
            });
        });
    }

    // Aplicar máscara nos valores iniciais se existirem
    camposMonetarios.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element && element.value) {
            aplicarMascaraMonetaria(element);
        }
    });
});
</script>
@endpush
@endsection
