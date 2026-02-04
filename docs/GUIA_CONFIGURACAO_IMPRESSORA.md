# Guia de Configuração da Impressora Térmica

Este guia explica como obter as informações necessárias para configurar a impressora no computador onde ela está conectada.

## Tipos de Conexão Suportadas

O sistema suporta 3 tipos de conexão com a impressora:

1. **Impressora de Rede** (NetworkPrintConnector) - Padrão
2. **Impressora USB/Windows** (WindowsPrintConnector)
3. **Impressão em Arquivo** (FilePrintConnector) - Para desenvolvimento

---

## 1. Impressora de Rede (Padrão)

### Configurações necessárias:
- **host**: IP da impressora na rede
- **port**: Porta de comunicação (geralmente 9100)

### Como obter o IP da impressora:

#### **Opção A: Via painel da impressora**
1. Pressione o botão de configuração/menu na impressora
2. Navegue até "Configurações de Rede" ou "Network Settings"
3. Procure por "IP Address" ou "Endereço IP"
4. Anote o IP (exemplo: `192.168.1.100`)

#### **Opção B: Via computador Windows**
1. Abra o **Painel de Controle** → **Dispositivos e Impressoras**
2. Clique com botão direito na impressora → **Propriedades da Impressora**
3. Vá na aba **Porta**
4. Procure por uma porta que comece com `IP_` ou `TCP/IP_`
5. O IP estará na descrição da porta

#### **Opção C: Via linha de comando**
```powershell
# No PowerShell (Windows)
Get-Printer | Select-Object Name, PortName | Format-Table
```

#### **Opção D: Via configuração de rede**
1. Acesse o roteador da rede (geralmente `192.168.1.1` ou `192.168.0.1`)
2. Procure por "Dispositivos Conectados" ou "DHCP Client List"
3. Localize a impressora e anote o IP

### Como descobrir a porta:

A porta padrão para impressoras térmicas em rede é **9100** (protocolo RAW).

Para verificar se a porta está correta:
```powershell
# No PowerShell (Windows)
Test-NetConnection -ComputerName [IP_DA_IMPRESSORA] -Port 9100
```

Se retornar `TcpTestSucceeded : True`, a porta está correta.

---

## 2. Impressora USB/Windows

### Configuração necessária:
- **windows_printer_name**: Nome da impressora no Windows

### Como obter o nome da impressora:

#### **Opção A: Via Painel de Controle**
1. Abra **Painel de Controle** → **Dispositivos e Impressoras**
2. Localize sua impressora térmica
3. O nome exibido é o nome que você precisa (exemplo: `XP-80C` ou `TM-T20`)

#### **Opção B: Via linha de comando**
```powershell
# No PowerShell (Windows)
Get-Printer | Select-Object Name | Format-Table
```

#### **Opção C: Via configurações do Windows**
1. Pressione `Windows + I` para abrir Configurações
2. Vá em **Dispositivos** → **Impressoras e scanners**
3. Localize sua impressora e anote o nome exato

**⚠️ IMPORTANTE:** Use o nome EXATO como aparece no Windows, incluindo espaços e caracteres especiais.

---

## 3. Configuração no Código

### Para impressora de rede:
```php
$config = [
    'host' => '192.168.1.100',  // IP da impressora
    'port' => 9100,              // Porta padrão (geralmente 9100)
    'timeout' => 10              // Timeout em segundos
];

$printerService = new ThermalPrinterService();
$printerService->connect($config);
```

### Para impressora USB/Windows:
```php
$config = [
    'windows_printer_name' => 'XP-80C'  // Nome exato da impressora no Windows
];

$printerService = new ThermalPrinterService();
$printerService->connect($config);
```

### Para desenvolvimento (arquivo):
```php
$config = [
    'file_path' => storage_path('app/printer_output.txt')
];

$printerService = new ThermalPrinterService();
$printerService->connect($config);
```

---

## 4. Teste de Conexão

### Criar um script de teste:

Crie um arquivo `test_printer.php` na raiz do projeto:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\ThermalPrinterService;

$config = [
    'host' => '192.168.1.100',  // Substitua pelo IP da sua impressora
    'port' => 9100,
];

try {
    $printer = new ThermalPrinterService();
    $printer->connect($config);
    
    echo "✅ Conexão estabelecida com sucesso!\n";
    
    // Teste de impressão simples
    $printer->printHeader('TESTE DE CONEXÃO');
    $printer->printFooter('Teste realizado com sucesso!');
    $printer->cut();
    $printer->finalize();
    
    echo "✅ Impressão de teste realizada!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
```

Execute:
```bash
php test_printer.php
```

---

## 5. Configuração Permanente (Recomendado)

### Opção A: Usar Settings do Sistema

Você pode salvar as configurações da impressora na tabela `settings`:

```php
use App\Models\Setting;

// Salvar configurações
Setting::set('printer_host', '192.168.1.100', 'string');
Setting::set('printer_port', 9100, 'integer');
Setting::set('printer_type', 'network', 'string'); // 'network' ou 'windows'

// Recuperar configurações
$host = Setting::get('printer_host', '192.168.0.100');
$port = Setting::get('printer_port', 9100);
$type = Setting::get('printer_type', 'network');
```

### Opção B: Variáveis de Ambiente (.env)

Adicione no arquivo `.env`:

```env
PRINTER_HOST=192.168.1.100
PRINTER_PORT=9100
PRINTER_TYPE=network
PRINTER_WINDOWS_NAME=XP-80C
```

E use no código:
```php
$config = [
    'host' => env('PRINTER_HOST', '192.168.0.100'),
    'port' => env('PRINTER_PORT', 9100),
];
```

---

## 6. Solução de Problemas

### Erro: "Erro ao conectar à impressora"
- ✅ Verifique se o IP está correto
- ✅ Verifique se a impressora está ligada e conectada à rede
- ✅ Teste a conectividade: `ping [IP_DA_IMPRESSORA]`
- ✅ Verifique se o firewall não está bloqueando a porta 9100

### Erro: "Connection timeout"
- ✅ Aumente o timeout: `'timeout' => 30`
- ✅ Verifique se o IP está correto
- ✅ Verifique se a impressora está na mesma rede

### Erro: "Printer not found" (Windows)
- ✅ Verifique se o nome da impressora está correto
- ✅ Verifique se a impressora está instalada no Windows
- ✅ Teste imprimindo um documento de teste pelo Windows primeiro

---

## 7. Informações Adicionais

### Charset (windows-1252)
- Já está configurado para suportar caracteres especiais em português (ã, ç, á, etc.)
- Não precisa alterar na maioria dos casos

### Portas comuns:
- **9100**: Porta padrão RAW (mais comum)
- **515**: Porta LPR/LPD
- **631**: Porta IPP

### Verificar se a impressora responde:
```powershell
# Windows PowerShell
Test-NetConnection -ComputerName 192.168.1.100 -Port 9100
```

---

## Próximos Passos

1. Identifique o tipo de conexão da sua impressora
2. Obtenha as informações necessárias (IP ou nome)
3. Atualize o arquivo `ThermalPrinterService.php` ou crie uma configuração dinâmica
4. Teste a conexão usando o script de teste
5. Configure permanentemente usando Settings ou .env

