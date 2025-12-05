@extends('layouts.admin')

@section('title', 'Vistoria de Estoque - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        ✅ Vistoria de Estoque
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Atualize as quantidades de todos os insumos ao final do expediente.
                    </p>
                </div>

                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Dica:</strong> Verifique cada insumo e atualize a quantidade atual. Os itens com estoque baixo serão destacados automaticamente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="{{ route('inventory.save-inspection') }}" id="inspection-form">
                    @csrf

                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-4">
                            @forelse($items as $item)
                            <div class="border rounded-lg p-4 {{ $item->isCriticalStock() ? 'border-red-300 bg-red-50' : ($item->isLowStock() ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200') }}">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                    <!-- Item Info -->
                                    <div class="md:col-span-1">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($item->isCriticalStock())
                                                    <span class="text-2xl">🚨</span>
                                                @elseif($item->isLowStock())
                                                    <span class="text-2xl">⚠️</span>
                                                @else
                                                    <span class="text-2xl">📦</span>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-900">{{ $item->name }}</h3>
                                                <p class="text-xs text-gray-500">
                                                    Mínimo: {{ number_format($item->min_quantity, 2, ',', '.') }} {{ $item->unit }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Quantity Display -->
                                    <div class="md:col-span-1">
                                        <div class="text-sm">
                                            <span class="text-gray-600">Quantidade atual:</span>
                                            <span class="font-semibold text-gray-900 ml-2">
                                                {{ number_format($item->current_quantity, 2, ',', '.') }} {{ $item->unit }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Quantity Input -->
                                    <div class="md:col-span-1">
                                        <label for="items[{{ $item->id }}][current_quantity]" class="block text-xs font-medium text-gray-700 mb-1">
                                            Nova Quantidade
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   id="items[{{ $item->id }}][current_quantity]" 
                                                   name="items[{{ $item->id }}][current_quantity]" 
                                                   value="{{ $item->current_quantity }}" 
                                                   required 
                                                   class="flex-1 dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                            <span class="text-xs text-gray-500">{{ $item->unit }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>Nenhum insumo ativo encontrado.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Vistoria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

