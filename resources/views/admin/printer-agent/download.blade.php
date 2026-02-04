@extends('layouts.app')

@section('title', 'Download - Doceria Printer Agent')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-100 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-6">
                <h1 class="text-3xl font-bold text-white flex items-center">
                    <span class="mr-3">🖨️</span>Doceria Printer Agent
                </h1>
                <p class="text-purple-100 mt-2">Executável para gerenciar impressoras térmicas localmente</p>
            </div>

            <div class="px-8 py-6 space-y-6">
                <!-- Status do Agente -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4" x-data="{ checking: false, running: false }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full mr-3" :class="running ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    Status: <span x-text="running ? 'Agente Rodando' : 'Agente Não Detectado'"></span>
                                </p>
                            </div>
                        </div>
                        <button 
                            @click="checking = true; fetch('/gestor/api/printer/agent/status').then(r => r.json()).then(d => { running = d.running; checking = false; })"
                            :disabled="checking"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50"
                        >
                            <span x-show="!checking">🔄 Verificar</span>
                            <span x-show="checking">Verificando...</span>
                        </button>
                    </div>
                </div>

                <!-- Informações -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">ℹ️ O que é o Printer Agent?</h3>
                    <p class="text-blue-700 text-sm mb-3">
                        O <strong>Doceria Printer Agent</strong> é um executável Windows que roda no seu computador e gerencia 
                        impressoras térmicas diretamente. Ele funciona como uma ponte entre o sistema web (hospedado na nuvem) 
                        e suas impressoras locais.
                    </p>
                    <ul class="text-blue-700 text-sm space-y-1 list-disc list-inside">
                        <li>Mais confiável que o QZ Tray</li>
                        <li>Funciona mesmo com sistema hospedado na nuvem</li>
                        <li>Inicia automaticamente com o Windows</li>
                        <li>Atualizações automáticas</li>
                        <li>Interface simples de configuração</li>
                    </ul>
                </div>

                <!-- Requisitos -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">📋 Requisitos do Sistema</h3>
                    <ul class="text-yellow-700 text-sm space-y-1 list-disc list-inside">
                        <li>Windows 10 ou superior</li>
                        <li>Impressora térmica compatível com ESC/POS (ex: EPSON TM-T20X)</li>
                        <li>Conexão com a internet (para atualizações)</li>
                        <li>Permissões de administrador para instalação</li>
                    </ul>
                </div>

                <!-- Instruções de Instalação -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-4">📥 Como Instalar</h3>
                    <ol class="space-y-3 text-sm text-green-700">
                        <li class="flex items-start">
                            <span class="font-bold mr-2">1.</span>
                            <span>Baixe o instalador clicando no botão abaixo</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">2.</span>
                            <span>Execute o arquivo <code class="bg-green-100 px-2 py-1 rounded">Doceria-Printer-Agent-Setup.exe</code></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">3.</span>
                            <span>Siga as instruções do instalador (pode solicitar permissões de administrador)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">4.</span>
                            <span>Após a instalação, o agente iniciará automaticamente</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">5.</span>
                            <span>Configure a impressora em <strong>Configurações > Impressora</strong> no sistema</span>
                        </li>
                    </ol>
                </div>

                <!-- Download Button -->
                <div class="text-center py-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">
                        <strong>Versão:</strong> 1.0.0<br>
                        <strong>Tamanho:</strong> ~150 MB (inclui Electron e dependências)
                    </p>
                    <a 
                        href="#" 
                        onclick="alert('O instalador será disponibilizado após o build do projeto Electron. Execute: cd printer-agent && npm run build:win'); return false;"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-600 hover:to-indigo-700 transition-all transform hover:scale-105"
                    >
                        <span class="mr-3 text-2xl">📥</span>
                        <span>Baixar Instalador</span>
                    </a>
                    <p class="text-xs text-gray-500 mt-4">
                        ⚠️ O instalador ainda não está disponível. Execute o build do projeto Electron primeiro.
                    </p>
                </div>

                <!-- Troubleshooting -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🔧 Solução de Problemas</h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div>
                            <strong class="text-gray-800">Agente não inicia automaticamente:</strong>
                            <p class="mt-1">Verifique se o agente está configurado para iniciar com o Windows. Você pode iniciá-lo manualmente pelo menu Iniciar.</p>
                        </div>
                        <div>
                            <strong class="text-gray-800">Impressora não aparece na lista:</strong>
                            <p class="mt-1">Certifique-se de que a impressora está instalada no Windows e está ligada. Clique em "Atualizar Lista" nas configurações.</p>
                        </div>
                        <div>
                            <strong class="text-gray-800">Erro ao imprimir:</strong>
                            <p class="mt-1">Verifique os logs do agente (acessível pelo ícone na bandeja do sistema) e certifique-se de que a impressora está configurada corretamente.</p>
                        </div>
                    </div>
                </div>

                <!-- Voltar -->
                <div class="text-center pt-4">
                    <a 
                        href="{{ route('settings.index') }}" 
                        class="text-purple-600 hover:text-purple-800 font-medium"
                    >
                        ← Voltar para Configurações
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
