@extends('layouts.admin')

@section('title', 'Configurações - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <span class="mr-3">⚙️</span>Configurações
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie as configurações gerais da doceria
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('gestor.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>

            @include('includes.flash-messages')

            <!-- Settings Form -->
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Store Status Section -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="mr-3">🏪</span>Controle de Loja
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Controle quando a loja está aberta ou fechada para atendimento</p>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        <!-- Store Status Toggle -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="radio"
                                           id="store_open"
                                           name="store_status"
                                           value="open"
                                           {{ App\Models\Setting::isStoreOpen() ? 'checked' : '' }}
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <label for="store_open" class="ml-3 text-sm font-medium text-gray-700">Loja Aberta</label>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Aberta para atendimento
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="radio"
                                           id="store_closed"
                                           name="store_status"
                                           value="closed"
                                           {{ !App\Models\Setting::isStoreOpen() ? 'checked' : '' }}
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                    <label for="store_closed" class="ml-3 text-sm font-medium text-gray-700">Loja Fechada</label>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    🔒 Fechada / Indisponível
                                </span>
                            </div>
                        </div>

                        <!-- Current Status Display -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full {{ App\Models\Setting::isStoreOpen() ? 'bg-green-500' : 'bg-red-500' }} mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Status atual: {{ App\Models\Setting::isStoreOpen() ? 'Aberta' : 'Fechada' }}
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ App\Models\Setting::isStoreOpen() ? 'Clientes podem fazer pedidos normalmente' : 'Página mostra que estamos indisponíveis no momento' }}
                                        </p>
                                        @if(App\Models\Setting::hasOpenCashRegister())
                                            <p class="text-xs text-blue-600 mt-1">
                                                💰 Há caixa aberto - válido para empréstimos ou ajustes
                                            </p>
                                        @else
                                            <p class="text-xs text-orange-600 mt-1">
                                                📊 Nenhum caixa aberto - apenas despedidas ou controles internos
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end space-y-2">
                                    @if(App\Models\Setting::isStoreOpen())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            🟢 Ativa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            🔴 Inativa
                                        </span>
                                    @endif

                                    <span class="text-xs text-gray-500">
                                        Atualizado em: {{ now()->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcement Banner Section -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="mr-3">📢</span>Banner de Aviso
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Configure mensagens importantes para seus clientes na página inicial</p>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <!-- Banner Active Toggle -->
                        <div>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="banner_active"
                                       value="1"
                                       {{ App\Models\Setting::get('banner_active') == 1 ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm">
                                    <span class="text-gray-700 font-medium">Mostrar banner de aviso</span>
                                    <span class="text-gray-500 block text-xs">Quando ativado, o banner aparecerá no topo da página inicial para todos os visitantes</span>
                                </span>
                            </label>
                        </div>

                        <!-- Banner Message -->
                        <div>
                            <label for="banner_message" class="block text-sm font-medium text-gray-700 mb-2">
                                Mensagem do Banner
                            </label>
                            <div class="relative">
                                <textarea
                                    id="banner_message"
                                    name="banner_message"
                                    rows="3"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300  @error('banner_message') border-red-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 mt-2"
                                    placeholder="Ex: Atenção, amanhã teremos promoção de 20% de desconto nas fatias"
                                    maxlength="500"
                                >{{ old('banner_message', App\Models\Setting::get('banner_message', '')) }}</textarea>
                                <div class="mt-1 text-right">
                                    <span id="char-count" class="text-xs text-gray-500">0/500 caracteres</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Digite uma mensagem importante para comunicar aos seus clientes
                            </p>
                            @error('banner_message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview Section -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">📋 Visualização do Banner:</h4>
                            <div id="banner-preview" class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg {{ App\Models\Setting::get('banner_active', false) !== true ? 'opacity-50' : '' }}">
                                <div class="flex">
                                    {{-- <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div> --}}
                                    <div class="ml-3">
                                        <p id="banner-preview-text" class="text-sm text-blue-700">
                                           📢 {{ App\Models\Setting::get('banner_message', '') ?: 'Digite uma mensagem para ver a visualização...' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if(App\Models\Setting::get('banner_active', false) !== true)
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    O banner está desativado. Ative o switch acima para exibir na página inicial.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('gestor.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        💾 Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for banner message
    const bannerTextarea = document.getElementById('banner_message');
    const charCount = document.getElementById('char-count');
    const bannerPreview = document.getElementById('banner-preview-text');
    const bannerCheckbox = document.querySelector('input[name="banner_active"]');
    const previewContainer = document.getElementById('banner-preview');

    // Update character count and preview
    function updatePreview() {
        const text = bannerTextarea.value;
        const count = text.length;
        charCount.textContent = count + '/500 caracteres';

        if (count > 0) {
            bannerPreview.textContent = text;
        } else {
            bannerPreview.textContent = 'Digite uma mensagem para ver a visualização...';
        }

        // Change color if approaching limit
        if (count > 450) {
            charCount.className = 'text-xs text-red-600';
        } else {
            charCount.className = 'text-xs text-gray-500';
        }

        // Update preview opacity based on checkbox
        if (bannerCheckbox.checked) {
            previewContainer.classList.remove('opacity-50');
        } else {
            previewContainer.classList.add('opacity-50');
        }
    }

    // Add event listeners
    bannerTextarea.addEventListener('input', updatePreview);
    bannerCheckbox.addEventListener('change', updatePreview);

    // Initialize
    updatePreview();
});
</script>
@endsection
