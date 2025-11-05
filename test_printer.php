<?php

/**
 * Script de Teste de Impressora Térmica
 * 
 * Execute este script para testar a conexão com a impressora:
 * php artisan printer:test
 * 
 * Ou execute diretamente:
 * php test_printer.php
 */

require __DIR__ . '/vendor/autoload.php';

// Carregar ambiente Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ThermalPrinterService;
use App\Models\Setting;

echo "========================================\n";
echo "TESTE DE CONFIGURAÇÃO DA IMPRESSORA\n";
echo "========================================\n\n";

// Verificar configurações salvas
$savedHost = Setting::get('printer_host');
$savedPort = Setting::get('printer_port');
$savedType = Setting::get('printer_type', 'network');

if ($savedHost) {
    echo "📋 Configurações encontradas no sistema:\n";
    echo "   Host: $savedHost\n";
    echo "   Porta: $savedPort\n";
    echo "   Tipo: $savedType\n\n";
    
    $useSaved = readline("Deseja usar essas configurações? (s/n): ");
    
    if (strtolower($useSaved) === 's') {
        if ($savedType === 'windows') {
            $windowsName = Setting::get('printer_windows_name');
            $config = ['windows_printer_name' => $windowsName];
        } else {
            $config = [
                'host' => $savedHost,
                'port' => $savedPort ?: 9100,
            ];
        }
    } else {
        $config = getConfigFromUser();
    }
} else {
    echo "⚠️  Nenhuma configuração encontrada.\n";
    echo "Vamos configurar agora:\n\n";
    $config = getConfigFromUser();
    
    // Perguntar se deseja salvar
    $save = readline("Deseja salvar essas configurações? (s/n): ");
    if (strtolower($save) === 's') {
        Setting::set('printer_host', $config['host'] ?? '', 'string');
        Setting::set('printer_port', $config['port'] ?? 9100, 'integer');
        Setting::set('printer_type', isset($config['windows_printer_name']) ? 'windows' : 'network', 'string');
        if (isset($config['windows_printer_name'])) {
            Setting::set('printer_windows_name', $config['windows_printer_name'], 'string');
        }
        echo "✅ Configurações salvas!\n\n";
    }
}

echo "\n🔄 Tentando conectar...\n";

try {
    $printer = new ThermalPrinterService();
    $printer->connect($config);
    
    echo "✅ Conexão estabelecida com sucesso!\n\n";
    
    echo "🖨️  Realizando impressão de teste...\n";
    
    // Imprimir teste
    $printer->printHeader('DOCERIA DELÍCIA', '12.345.678/0001-90');
    $printer->printer->text("TESTE DE CONEXÃO\n");
    $printer->printer->text(str_repeat('-', 48) . "\n");
    $printer->printer->text("Data: " . date('d/m/Y H:i') . "\n");
    $printer->printer->text("Status: CONEXÃO OK\n");
    $printer->printer->text(str_repeat('-', 48) . "\n");
    $printer->printFooter('Teste realizado com sucesso!');
    $printer->cut();
    $printer->finalize();
    
    echo "✅ Impressão de teste realizada com sucesso!\n";
    echo "\n========================================\n";
    echo "✅ CONFIGURAÇÃO FUNCIONANDO CORRETAMENTE!\n";
    echo "========================================\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n\n";
    
    echo "💡 Dicas para resolver:\n";
    echo "1. Verifique se o IP está correto\n";
    echo "2. Verifique se a impressora está ligada e na rede\n";
    echo "3. Teste conectividade: ping [IP]\n";
    echo "4. Verifique se o firewall não está bloqueando\n";
    echo "5. Para impressora USB, verifique o nome no Windows\n";
    
    exit(1);
}

function getConfigFromUser()
{
    echo "Escolha o tipo de conexão:\n";
    echo "1. Rede (Network/IP)\n";
    echo "2. USB/Windows\n";
    echo "3. Arquivo (desenvolvimento)\n";
    
    $choice = readline("Opção (1-3): ");
    
    switch ($choice) {
        case '1':
            $host = readline("IP da impressora (ex: 192.168.1.100): ");
            $port = readline("Porta (pressione Enter para 9100): ") ?: 9100;
            
            return [
                'host' => $host,
                'port' => (int)$port,
            ];
            
        case '2':
            echo "\nImpressoras instaladas no Windows:\n";
            exec('powershell -Command "Get-Printer | Select-Object Name | Format-Table"', $output);
            echo implode("\n", $output) . "\n";
            
            $name = readline("Nome da impressora (exatamente como aparece acima): ");
            
            return [
                'windows_printer_name' => $name,
            ];
            
        case '3':
            $file = readline("Caminho do arquivo (ou Enter para padrão): ") 
                ?: storage_path('app/printer_output.txt');
            
            return [
                'file_path' => $file,
            ];
            
        default:
            echo "Opção inválida!\n";
            exit(1);
    }
}

