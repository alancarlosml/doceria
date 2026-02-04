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
                        Voltar
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

                <!-- Carousel Banner Section -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="mr-3">🎠</span>Banners do Carrossel
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Gerencie os banners que aparecem no carrossel da página inicial</p>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <!-- Upload Form -->
                        <form method="POST" action="{{ route('settings.banner.store') }}" enctype="multipart/form-data" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            @csrf
                            <h4 class="text-sm font-medium text-gray-900 mb-4">➕ Adicionar Novo Banner</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="banner_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        Imagem do Banner *
                                    </label>
                                    <input type="file" 
                                           id="banner_image" 
                                           name="banner_image" 
                                           accept="image/*"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                    <p class="mt-1 text-xs text-gray-500">Tamanho recomendado: 1200x400px. Máx: 5MB. Formatos: JPG, PNG, GIF, WebP</p>
                                </div>
                                
                                <div>
                                    <label for="banner_title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Título (opcional)
                                    </label>
                                    <input type="text" 
                                           id="banner_title" 
                                           name="banner_title" 
                                           maxlength="100"
                                           placeholder="Ex: Promoção de Natal"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="banner_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Descrição (opcional)
                                    </label>
                                    <input type="text" 
                                           id="banner_description" 
                                           name="banner_description" 
                                           maxlength="255"
                                           placeholder="Ex: 20% de desconto em brigadeiros"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                </div>
                                
                                <div>
                                    <label for="banner_link" class="block text-sm font-medium text-gray-700 mb-2">
                                        Link (opcional)
                                    </label>
                                    <input type="url" 
                                           id="banner_link" 
                                           name="banner_link" 
                                           maxlength="255"
                                           placeholder="https://..."
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Adicionar Banner
                                </button>
                            </div>
                        </form>

                        <!-- Current Banners List -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-4">📋 Banners Atuais ({{ $carouselBanners->count() }})</h4>
                            
                            @if($carouselBanners->isEmpty())
                                <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                    <span class="text-4xl">🖼️</span>
                                    <p class="mt-2 text-sm text-gray-500">Nenhum banner cadastrado ainda.</p>
                                    <p class="text-xs text-gray-400">Adicione banners para aparecerem no carrossel da página inicial.</p>
                                </div>
                            @else
                                <div class="space-y-3" id="banners-list">
                                    @foreach($carouselBanners as $banner)
                                        <div class="flex items-center gap-4 p-3 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow {{ !$banner->active ? 'opacity-60' : '' }}" data-banner-id="{{ $banner->id }}">
                                            <!-- Drag Handle -->
                                            <div class="cursor-move text-gray-400 hover:text-gray-600" title="Arraste para reordenar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                </svg>
                                            </div>
                                            
                                            <!-- Thumbnail -->
                                            <div class="flex-shrink-0 w-24 h-16 bg-gray-100 rounded overflow-hidden">
                                                @if($banner->image_url)
                                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?? 'Banner' }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Info -->
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $banner->title ?? 'Sem título' }}
                                                </p>
                                                @if($banner->description)
                                                    <p class="text-xs text-gray-500 truncate">{{ $banner->description }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $banner->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $banner->active ? '✅ Ativo' : '⏸️ Inativo' }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">Ordem: {{ $banner->order }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex items-center gap-2">
                                                <form method="POST" action="{{ route('settings.banner.toggle', $banner) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors" title="{{ $banner->active ? 'Desativar' : 'Ativar' }}">
                                                        @if($banner->active)
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('settings.banner.destroy', $banner) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este banner?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors" title="Remover">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-3 text-xs text-gray-500">💡 Arraste os banners para reorganizar a ordem de exibição.</p>
                            @endif
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

                <!-- Printer Agent Configuration Section -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6" x-data="printerAgentConfig()">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-indigo-600 border-b border-purple-200">
                        <h3 class="text-lg font-medium text-white flex items-center">
                            <span class="mr-3">🖨️</span>Doceria Printer Agent (Recomendado)
                        </h3>
                        <p class="mt-1 text-sm text-purple-100">Executável local para gerenciar impressoras - Mais confiável que QZ Tray</p>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        
                        <!-- Status do Agente -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" :class="agentRunning ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Status do Agente: <span x-text="agentRunning ? 'Rodando' : 'Não Detectado'"></span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1" x-show="!agentRunning">
                                            ⚠️ Agente não está rodando. Baixe e instale o executável abaixo.
                                        </p>
                                        <p class="text-xs text-green-600 mt-1" x-show="agentRunning && agentStatus">
                                            ✅ <span x-text="agentStatus.printerConfigured ? 'Impressora configurada: ' + agentStatus.printer : 'Pronto para configurar impressora'"></span>
                                        </p>
                                    </div>
                                </div>
                                <button 
                                    @click="checkAgentStatus()"
                                    :disabled="checkingStatus"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <span x-show="!checkingStatus">🔄 Verificar</span>
                                    <span x-show="checkingStatus">Verificando...</span>
                                </button>
                            </div>
                        </div>

                        <!-- Seleção de Impressora via Agente -->
                        <div x-show="agentRunning">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Impressora Padrão
                            </label>
                            <div class="flex gap-2">
                                <select 
                                    x-model="selectedAgentPrinter"
                                    @change="setAgentPrinter()"
                                    class="flex-1 shadow-sm focus:ring-purple-500 focus:border-purple-500 block sm:text-sm border-gray-300 rounded-md"
                                    :disabled="loadingPrinters || !agentRunning"
                                >
                                    <option value="">-- Carregando impressoras --</option>
                                    <template x-for="printer in agentPrinters" :key="printer.name">
                                        <option :value="printer.name" x-text="printer.name + (printer.isDefault ? ' (Padrão)' : '')"></option>
                                    </template>
                                </select>
                                <button 
                                    @click="refreshAgentPrinters()"
                                    :disabled="!agentRunning || loadingPrinters"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                                    title="Atualizar lista de impressoras"
                                >
                                    <span x-show="!loadingPrinters">🔄</span>
                                    <span x-show="loadingPrinters">⏳</span>
                                </button>
                            </div>
                        </div>

                        <!-- Download do Agente -->
                        <div x-show="!agentRunning" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800 mb-2">📥 Instalar Doceria Printer Agent:</p>
                                <p class="text-sm text-yellow-700 mb-3">
                                    O Printer Agent é um executável que roda no seu computador e gerencia as impressoras diretamente. 
                                    É mais confiável que o QZ Tray e funciona mesmo com o sistema hospedado na nuvem.
                                </p>
                                <a 
                                    href="{{ route('printer.agent.download-page') }}" 
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                >
                                    📥 Baixar Instalador
                                </a>
                                <p class="text-xs text-yellow-600 mt-3">
                                    Após instalar, o agente iniciará automaticamente. Clique em "Verificar" acima para detectá-lo.
                                </p>
                            </div>
                        </div>

                        <!-- Informações do Agente -->
                        <div x-show="agentRunning && agentStatus" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm">
                                <p class="font-medium text-green-800 mb-2">ℹ️ Informações do Agente:</p>
                                <ul class="text-green-700 space-y-1 text-xs">
                                    <li><strong>Versão:</strong> <span x-text="agentStatus.version || 'N/A'"></span></li>
                                    <li><strong>Porta:</strong> <span x-text="agentStatus.serverPort || '8080'"></span></li>
                                    <li><strong>Status:</strong> <span x-text="agentStatus.status || 'running'"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QZ Tray Printer Configuration Section -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden" x-data="qzTrayConfig()">
                    <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="mr-3">🖨️</span>Configuração da Impressora Térmica (QZ Tray)
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Configure a impressão direta via QZ Tray - funciona com sistema hospedado na nuvem</p>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        
                        <!-- Status do QZ Tray -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" :class="qzConnected ? 'bg-green-500' : 'bg-red-500'"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Status QZ Tray: <span x-text="qzConnected ? 'Conectado' : 'Desconectado'"></span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1" x-show="!qzConnected">
                                            ⚠️ QZ Tray não detectado. Verifique se está instalado e rodando.
                                        </p>
                                        <p class="text-xs text-green-600 mt-1" x-show="qzConnected">
                                            ✅ Pronto para imprimir!
                                        </p>
                                    </div>
                                </div>
                                <button 
                                    @click="connectQZ()"
                                    :disabled="connecting"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <span x-show="!connecting">🔄 Reconectar</span>
                                    <span x-show="connecting">Conectando...</span>
                                </button>
                            </div>
                        </div>

                        <!-- Seleção de Impressora -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Impressora
                            </label>
                            <div class="flex gap-2">
                                <select 
                                    id="qz_printer_select"
                                    x-model="selectedPrinter"
                                    @change="savePrinter()"
                                    class="flex-1 shadow-sm focus:ring-purple-500 focus:border-purple-500 block sm:text-sm border-gray-300 rounded-md"
                                    :disabled="!qzConnected || printers.length === 0"
                                >
                                    <option value="">-- Selecione uma impressora --</option>
                                    <template x-for="printer in printers" :key="printer">
                                        <option :value="printer" x-text="printer"></option>
                                    </template>
                                </select>
                                <button 
                                    @click="refreshPrinters()"
                                    :disabled="!qzConnected || loadingPrinters"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                                    title="Atualizar lista de impressoras"
                                >
                                    <span x-show="!loadingPrinters">🔄</span>
                                    <span x-show="loadingPrinters">⏳</span>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Selecione a impressora térmica (ex: EPSON TM-T20X)
                            </p>
                        </div>

                        <!-- Impressora Configurada -->
                        <div x-show="selectedPrinter" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <span class="text-green-500 mr-2">✅</span>
                                <div>
                                    <p class="text-sm font-medium text-green-800">Impressora Configurada:</p>
                                    <p class="text-sm text-green-700" x-text="selectedPrinter"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Instruções de Instalação -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800 mb-2">📥 Como instalar o QZ Tray:</p>
                                <ol class="text-sm text-blue-700 list-decimal list-inside space-y-1">
                                    <li>Baixe o QZ Tray em: <a href="https://qz.io/download/" target="_blank" class="underline font-medium">qz.io/download</a></li>
                                    <li>Execute o instalador e siga as instruções</li>
                                    <li>Após instalado, o QZ Tray iniciará automaticamente com o Windows</li>
                                    <li>Clique em "Reconectar" acima para detectar o QZ Tray</li>
                                    <li>Selecione sua impressora EPSON TM-T20X na lista</li>
                                </ol>
                                <p class="text-xs text-blue-600 mt-3">
                                    💡 <strong>Dica:</strong> O ícone do QZ Tray aparecerá na bandeja do sistema (próximo ao relógio)
                                </p>
                            </div>
                        </div>

                        <!-- Test Printer Button -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-1">🧪 Testar Impressora</h4>
                                <p class="text-xs text-gray-500">Imprime um cupom de teste para verificar se a configuração está funcionando.</p>
                            </div>
                            <button 
                                @click="testPrint()"
                                :disabled="!qzConnected || !selectedPrinter || testing"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-show="!testing">Testar Impressora</span>
                                <span x-show="testing">Imprimindo...</span>
                            </button>
                        </div>

                        <!-- Mensagem de resultado do teste -->
                        <div x-show="testMessage" 
                             :class="testSuccess ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                             class="border rounded-lg p-3 text-sm"
                             x-transition>
                            <span x-text="testMessage"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-5">
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

// Alpine.js component for Printer Agent configuration
function printerAgentConfig() {
    return {
        agentRunning: false,
        checkingStatus: false,
        agentStatus: null,
        agentPrinters: [],
        selectedAgentPrinter: '',
        loadingPrinters: false,

        async init() {
            await this.checkAgentStatus();
            if (this.agentRunning) {
                await this.refreshAgentPrinters();
            }
        },

        async checkAgentStatus() {
            this.checkingStatus = true;
            try {
                // Tentar verificar diretamente via agente
                if (typeof PrinterAgent !== 'undefined') {
                    const available = await PrinterAgent.checkStatus();
                    if (available) {
                        this.agentRunning = true;
                        this.agentStatus = PrinterAgent.status;
                        return;
                    }
                }

                // Fallback: verificar via API Laravel
                const response = await fetch('/gestor/api/printer/agent/status', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.agentRunning = data.running || false;
                    this.agentStatus = data.status || null;
                } else {
                    this.agentRunning = false;
                    this.agentStatus = null;
                }
            } catch (error) {
                console.error('Erro ao verificar status do agente:', error);
                this.agentRunning = false;
                this.agentStatus = null;
            } finally {
                this.checkingStatus = false;
            }
        },

        async refreshAgentPrinters() {
            if (!this.agentRunning) return;

            this.loadingPrinters = true;
            try {
                // Tentar via agente direto
                if (typeof PrinterAgent !== 'undefined') {
                    const printers = await PrinterAgent.getPrinters();
                    this.agentPrinters = printers;
                    
                    // Selecionar impressora configurada se houver
                    if (this.agentStatus && this.agentStatus.printer) {
                        this.selectedAgentPrinter = this.agentStatus.printer;
                    }
                    return;
                }

                // Fallback: via API Laravel
                const response = await fetch('/gestor/api/printer/agent/printers', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.agentPrinters = data.printers || [];
                    
                    if (this.agentStatus && this.agentStatus.printer) {
                        this.selectedAgentPrinter = this.agentStatus.printer;
                    }
                }
            } catch (error) {
                console.error('Erro ao listar impressoras do agente:', error);
                this.agentPrinters = [];
            } finally {
                this.loadingPrinters = false;
            }
        },

        async setAgentPrinter() {
            if (!this.selectedAgentPrinter || !this.agentRunning) return;

            try {
                // Tentar via agente direto
                if (typeof PrinterAgent !== 'undefined') {
                    const success = await PrinterAgent.setPrinter(this.selectedAgentPrinter);
                    if (success) {
                        // Atualizar status
                        await this.checkAgentStatus();
                    }
                    return;
                }

                // Fallback: via API Laravel
                const response = await fetch('/gestor/api/printer/agent/config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        printer_name: this.selectedAgentPrinter
                    })
                });

                if (response.ok) {
                    await this.checkAgentStatus();
                }
            } catch (error) {
                console.error('Erro ao configurar impressora no agente:', error);
            }
        }
    };
}

// Alpine.js component for QZ Tray configuration
function qzTrayConfig() {
    return {
        qzConnected: false,
        connecting: false,
        printers: [],
        selectedPrinter: localStorage.getItem('qz_printer_name') || '',
        loadingPrinters: false,
        testing: false,
        testMessage: '',
        testSuccess: false,

        async init() {
            // Try to connect to QZ Tray on page load
            await this.connectQZ();
        },

        async connectQZ() {
            this.connecting = true;
            this.testMessage = '';
            
            try {
                if (typeof QZPrint !== 'undefined') {
                    const connected = await QZPrint.init();
                    this.qzConnected = connected;
                    
                    if (connected) {
                        await this.refreshPrinters();
                    }
                } else {
                    console.error('QZPrint not loaded');
                    this.qzConnected = false;
                }
            } catch (error) {
                console.error('Error connecting to QZ Tray:', error);
                this.qzConnected = false;
            } finally {
                this.connecting = false;
            }
        },

        async refreshPrinters() {
            if (!this.qzConnected) return;
            
            this.loadingPrinters = true;
            try {
                this.printers = await QZPrint.listPrinters();
                
                // If previously selected printer is still available, keep it selected
                if (this.selectedPrinter && !this.printers.includes(this.selectedPrinter)) {
                    this.selectedPrinter = '';
                }
            } catch (error) {
                console.error('Error listing printers:', error);
                this.printers = [];
            } finally {
                this.loadingPrinters = false;
            }
        },

        savePrinter() {
            if (this.selectedPrinter) {
                QZPrint.setPrinter(this.selectedPrinter);
                this.testMessage = '✅ Impressora salva: ' + this.selectedPrinter;
                this.testSuccess = true;
                
                // Clear message after 3 seconds
                setTimeout(() => {
                    this.testMessage = '';
                }, 3000);
            }
        },

        async testPrint() {
            if (!this.qzConnected || !this.selectedPrinter) return;
            
            this.testing = true;
            this.testMessage = '';
            
            try {
                await QZPrint.printTest();
                this.testMessage = '✅ Cupom de teste enviado com sucesso! Verifique a impressora.';
                this.testSuccess = true;
            } catch (error) {
                console.error('Print test error:', error);
                this.testMessage = '❌ Erro ao imprimir: ' + error.message;
                this.testSuccess = false;
            } finally {
                this.testing = false;
            }
        }
    };
}

// Drag and drop for banner reordering
document.addEventListener('DOMContentLoaded', function() {
    const bannersList = document.getElementById('banners-list');
    if (!bannersList) return;

    let draggedItem = null;

    bannersList.querySelectorAll('[data-banner-id]').forEach(item => {
        item.setAttribute('draggable', 'true');

        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('opacity-50');
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('opacity-50');
            draggedItem = null;
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('border-purple-400', 'border-2');
        });

        item.addEventListener('dragleave', function() {
            this.classList.remove('border-purple-400', 'border-2');
        });

        item.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-400', 'border-2');

            if (draggedItem !== this) {
                const allItems = [...bannersList.querySelectorAll('[data-banner-id]')];
                const draggedIndex = allItems.indexOf(draggedItem);
                const targetIndex = allItems.indexOf(this);

                if (draggedIndex < targetIndex) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }

                // Save new order
                saveBannerOrder();
            }
        });
    });

    function saveBannerOrder() {
        const items = bannersList.querySelectorAll('[data-banner-id]');
        const banners = [];
        
        items.forEach((item, index) => {
            banners.push({
                id: item.dataset.bannerId,
                order: index + 1
            });
        });

        fetch('{{ route("settings.banner.order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ banners })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update order numbers in UI
                items.forEach((item, index) => {
                    const orderSpan = item.querySelector('.text-xs.text-gray-400');
                    if (orderSpan) {
                        orderSpan.textContent = 'Ordem: ' + (index + 1);
                    }
                });
            }
        })
        .catch(error => console.error('Error saving order:', error));
    }
});
</script>
@endsection
