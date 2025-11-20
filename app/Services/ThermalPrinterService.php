<?php

namespace App\Services;

use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Exception;
use Illuminate\Support\Facades\Log;

class ThermalPrinterService
{
    protected $printer;
    protected $connector;
    protected $isConnected = false;

    /**
     * Largura máxima da linha para impressora 80mm (48 caracteres padrão)
     */
    const LINE_WIDTH = 48;

    /**
     * Configurações padrão da impressora
     */
    protected $defaultConfig = [
        'windows_printer_name' => 'EPSON TM-T20X Receipt6' //Nome da impressora no Windows
    ];

    /**
     * Log helper com fallback
     */
    protected function log($level, $message, $context = [])
    {
        try {
            Log::channel('printer')->{$level}($message, $context);
        } catch (\Exception $e) {
            // Fallback: escrever no log principal se o canal printer falhar
            try {
                Log::channel('printer_fallback')->{$level}('[PRINTER] ' . $message, $context);
            } catch (\Exception $e2) {
                // Último recurso: usar log padrão
                Log::{$level}('[PRINTER] ' . $message, $context);
            }
        }
    }

    /**
     * Listar impressoras disponíveis no Windows
     * Útil para diagnóstico
     */
    public static function listWindowsPrinters(): array
    {
        Log::channel('printer')->info('=== LISTANDO IMPRESSORAS DO WINDOWS ===');
        
        $printers = [];
        
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                // Comando PowerShell para listar impressoras
                $command = 'powershell -Command "Get-Printer | Select-Object Name | Format-Table -HideTableHeaders"';
                $output = shell_exec($command);
                
                if ($output) {
                    $lines = explode("\n", trim($output));
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            $printers[] = $line;
                            Log::channel('printer')->info('Impressora encontrada:', ['name' => $line]);
                        }
                    }
                } else {
                    Log::channel('printer')->warning('Nenhuma impressora encontrada via PowerShell');
                }
            } else {
                Log::channel('printer')->warning('Sistema não é Windows, não é possível listar impressoras');
            }
        } catch (\Exception $e) {
            Log::channel('printer')->error('Erro ao listar impressoras:', [
                'message' => $e->getMessage()
            ]);
        }
        
        Log::channel('printer')->info('Total de impressoras encontradas:', ['count' => count($printers)]);
        
        return $printers;
    }

    /**
     * Obter configurações do banco de dados ou usar padrões
     *
     * @return array
     */
    public static function getConfigFromSettings(): array
    {
        Log::channel('printer')->info('=== OBTENDO CONFIGURAÇÕES DA IMPRESSORA ===');
        
        $printerType = Setting::get('printer_type');
        Log::channel('printer')->info('Tipo de impressora configurado:', ['type' => $printerType]);
        
        // Se não há tipo configurado, verificar se há configuração padrão de Windows
        if (!$printerType) {
            Log::channel('printer')->info('Nenhum tipo configurado, verificando padrão...');
            $default = new self();
            // Se tem windows_printer_name configurado por padrão, usar Windows
            if (isset($default->defaultConfig['windows_printer_name'])) {
                $config = ['windows_printer_name' => $default->defaultConfig['windows_printer_name']];
                Log::channel('printer')->info('Usando configuração padrão Windows:', $config);
                return $config;
            }
            // Caso contrário, usar rede como padrão
            Log::channel('printer')->warning('Nenhuma configuração encontrada, retornando vazio');
            return [];
        }
        
        if ($printerType === 'windows') {
            Log::channel('printer')->info('Tipo Windows detectado, buscando nome da impressora...');
            $windowsName = Setting::get('printer_windows_name');
            if ($windowsName) {
                $config = ['windows_printer_name' => $windowsName];
                Log::channel('printer')->info('Nome da impressora encontrado:', $config);
                return $config;
            }
            // Se está configurado como Windows mas não tem nome salvo, usar padrão
            Log::channel('printer')->warning('Nome não encontrado no banco, usando padrão...');
            $default = new self();
            if (isset($default->defaultConfig['windows_printer_name'])) {
                $config = ['windows_printer_name' => $default->defaultConfig['windows_printer_name']];
                Log::channel('printer')->info('Usando nome padrão:', $config);
                return $config;
            }
        }
        
        // Configuração de rede
        Log::channel('printer')->info('Tentando configuração de rede...');
        $host = Setting::get('printer_host');
        $port = Setting::get('printer_port', 9100);
        
        if ($host) {
            $config = [
                'host' => $host,
                'port' => $port,
            ];
            Log::channel('printer')->info('Configuração de rede encontrada:', $config);
            return $config;
        }
        
        // Retornar configuração padrão se nada estiver salvo
        Log::channel('printer')->info('Usando configuração padrão do código...');
        $default = new self();
        Log::channel('printer')->info('Configuração padrão:', $default->defaultConfig);
        return $default->defaultConfig;
    }

    /**
     * Conectar à impressora
     *
     * @param array $config Configurações da impressora
     * @return bool
     */
    public function connect(array $config = [])
    {
        Log::channel('printer')->info('=== TENTANDO CONECTAR À IMPRESSORA ===');
        Log::channel('printer')->info('Configuração recebida:', $config);
        
        $config = array_merge($this->defaultConfig, $config);
        Log::channel('printer')->info('Configuração após merge:', $config);

        try {
            if (isset($config['file_path'])) {
                // Impressão em arquivo (para desenvolvimento)
                Log::channel('printer')->info('Usando FilePrintConnector', ['path' => $config['file_path']]);
                $this->connector = new FilePrintConnector($config['file_path']);
            } elseif (isset($config['windows_printer_name'])) {
                // Impressão via USB/Windows
                $printerName = $config['windows_printer_name'];
                Log::channel('printer')->info('Usando WindowsPrintConnector', ['printer_name' => $printerName]);
                
                // Listar impressoras disponíveis para diagnóstico
                $availablePrinters = self::listWindowsPrinters();
                Log::channel('printer')->info('Impressoras disponíveis no sistema:', ['printers' => $availablePrinters]);
                
                // Verificar se a impressora existe na lista
                $printerExists = in_array($printerName, $availablePrinters);
                Log::channel('printer')->info('Impressora encontrada na lista?', [
                    'printer_name' => $printerName,
                    'exists' => $printerExists
                ]);
                
                if (!$printerExists && !empty($availablePrinters)) {
                    Log::channel('printer')->warning('⚠️ IMPRESSORA NÃO ENCONTRADA NA LISTA!', [
                        'procurada' => $printerName,
                        'disponiveis' => $availablePrinters
                    ]);
                }
                
                // Verificar se a impressora existe no Windows
                try {
                    $this->connector = new WindowsPrintConnector($printerName);
                    Log::channel('printer')->info('Conector Windows criado com sucesso');
                } catch (Exception $e) {
                    Log::channel('printer')->error('Erro ao criar WindowsPrintConnector:', [
                        'printer_name' => $printerName,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                // Impressão via rede (padrão)
                Log::channel('printer')->info('Usando NetworkPrintConnector', [
                    'host' => $config['host'] ?? 'N/A',
                    'port' => $config['port'] ?? 'N/A'
                ]);
                $this->connector = new NetworkPrintConnector($config['host'], $config['port']);
            }

            Log::channel('printer')->info('Criando instância do Printer...');
            $this->printer = new Printer($this->connector);
            Log::channel('printer')->info('Printer criado com sucesso');

            $this->isConnected = true;
            Log::channel('printer')->info('✅ CONEXÃO ESTABELECIDA COM SUCESSO');
            return true;

        } catch (Exception $e) {
            $this->isConnected = false;
            Log::channel('printer')->error('❌ ERRO AO CONECTAR:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Erro ao conectar à impressora: ' . $e->getMessage());
        }
    }

    /**
     * Verificar se está conectado
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    /**
     * Imprimir cabeçalho da doceria
     */
    public function printHeader($businessName = 'Doceria Delícia', $cnpj = null)
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printer->setTextSize(1, 2);
        $this->printer->text($businessName . "\n");

        if ($cnpj) {
            $this->printer->setTextSize(1, 1);
            $this->printer->text("CNPJ: $cnpj\n");
        }

        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");
    }

    /**
     * Imprimir dados do pedido/cupom
     */
    public function printOrder($orderData)
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        // Número do pedido
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setTextSize(1, 1);
        $this->printer->text("PEDIDO #" . str_pad($orderData['order_number'], 6, '0', STR_PAD_LEFT) . "\n");

        if (isset($orderData['date'])) {
            $this->printer->text($orderData['date'] . "\n");
        }

        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");

        // Cliente (se aplicável)
        if (isset($orderData['customer_name'])) {
            $customerName = $this->truncateText($orderData['customer_name'], self::LINE_WIDTH - 9);
            $this->printer->text("Cliente: " . $customerName . "\n");
        }

        if (isset($orderData['customer_phone'])) {
            $this->printer->text("Tel: " . $orderData['customer_phone'] . "\n");
        }

        if (isset($orderData['delivery_address'])) {
            $this->printer->text("Entrega:\n");
            $this->printWrappedText($orderData['delivery_address'], self::LINE_WIDTH);
        }

        $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");

        // Itens do pedido
        foreach ($orderData['items'] as $item) {
            $quantity = $item['quantity'];
            $name = $this->truncateText($item['name'], self::LINE_WIDTH - 15); // Reservar espaço para quantidade e preço
            $subtotal = number_format($item['subtotal'], 2, ',', '.');
            
            // Linha 1: Quantidade x Nome
            $line1 = sprintf("%dx %s", $quantity, $name);
            $this->printer->text($this->padText($line1, self::LINE_WIDTH) . "\n");
            
            // Linha 2: Preço unitário (se necessário) e subtotal alinhado à direita
            $unitPrice = number_format($item['price'], 2, ',', '.');
            $unitPriceText = "R$ {$unitPrice}/un";
            $subtotalText = "R$ {$subtotal}";
            
            // Se couber na mesma linha, mostrar ambos
            if (mb_strlen($unitPriceText) + mb_strlen($subtotalText) + 5 <= self::LINE_WIDTH) {
                $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
                $this->printer->text("{$unitPriceText} = {$subtotalText}\n");
                $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            } else {
                // Se não couber, mostrar apenas subtotal
                $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
                $this->printer->text("{$subtotalText}\n");
                $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            }
        }

        $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");

        // Totais
        if (isset($orderData['subtotal'])) {
            $this->printer->text("Subtotal: R$ " . number_format($orderData['subtotal'], 2, ',', '.') . "\n");
        }

        if (isset($orderData['discount']) && $orderData['discount'] > 0) {
            $this->printer->text("Desconto: R$ " . number_format($orderData['discount'], 2, ',', '.') . "\n");
        }

        if (isset($orderData['delivery_fee']) && $orderData['delivery_fee'] > 0) {
            $this->printer->text("Taxa Entrega: R$ " . number_format($orderData['delivery_fee'], 2, ',', '.') . "\n");
        }

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setTextSize(1, 2);
        $this->printer->text("TOTAL: R$ " . number_format($orderData['total'], 2, ',', '.') . "\n");

        $this->printer->setTextSize(1, 1);
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);

        // Método de pagamento
        if (isset($orderData['payment_method'])) {
            $methodName = $this->getPaymentMethodName($orderData['payment_method']);
            $this->printer->text("Pagamento: $methodName\n");
        }

        // Tipo do pedido
        if (isset($orderData['order_type'])) {
            $typeName = ($orderData['order_type'] === 'delivery') ? 'Entrega' : 'Balcão';
            $this->printer->text("Tipo: $typeName\n");
        }

        $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");
    }

    /**
     * Imprimir rodapé
     */
    public function printFooter($footerText = "Obrigado pela preferência!")
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text($footerText . "\n\n");

        // Data de impressão
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->setTextSize(1, 1);
        $this->printer->text("Impresso em: " . date('d/m/Y H:i') . "\n");
    }

    /**
     * Cortar papel
     */
    public function cut()
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        $this->printer->cut();
    }

    /**
     * Abrir gaveta (se suportado)
     */
    public function openDrawer()
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        try {
            $this->printer->pulse();
        } catch (Exception $e) {
            // Algumas impressoras não suportam abertura de gaveta
        }
    }

    /**
     * Finalizar impressão
     */
    public function finalize()
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        $this->printer->close();
        $this->isConnected = false;
    }

    /**
     * Obter nome legível do método de pagamento
     */
    protected function getPaymentMethodName($method): string
    {
        $methods = [
            'dinheiro' => 'Dinheiro',
            'cartao_credito' => 'Cartão Crédito',
            'cartao_debito' => 'Cartão Débito',
            'pix' => 'PIX',
            'transferencia' => 'Transferência',
            'boleto' => 'Boleto'
        ];

        return $methods[$method] ?? $method;
    }

    /**
     * Truncar texto para caber na largura da linha
     */
    protected function truncateText($text, $maxLength)
    {
        $text = trim($text);
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        return mb_substr($text, 0, $maxLength - 3, 'UTF-8') . '...';
    }

    /**
     * Quebrar texto longo em múltiplas linhas
     */
    protected function printWrappedText($text, $maxWidth)
    {
        $text = trim($text);
        $words = explode(' ', $text);
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? $currentLine . ' ' . $word : $word;
            
            if (mb_strlen($testLine) <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $this->printer->text($currentLine . "\n");
                }
                // Se a palavra sozinha é maior que a largura, truncar
                if (mb_strlen($word) > $maxWidth) {
                    $this->printer->text($this->truncateText($word, $maxWidth) . "\n");
                    $currentLine = '';
                } else {
                    $currentLine = $word;
                }
            }
        }

        if ($currentLine) {
            $this->printer->text($currentLine . "\n");
        }
    }

    /**
     * Preencher texto até a largura especificada
     */
    protected function padText($text, $width)
    {
        $text = mb_substr($text, 0, $width, 'UTF-8');
        return str_pad($text, $width, ' ', STR_PAD_RIGHT);
    }

    /**
     * Método simplificado para imprimir pedido completo
     */
    public function printSale($sale, array $config = [])
    {
        Log::channel('printer')->info('=== INICIANDO IMPRESSÃO DE VENDA ===', [
            'sale_id' => $sale->id ?? 'N/A',
            'config' => $config
        ]);

        try {
            $this->connect($config);
            Log::channel('printer')->info('Conexão estabelecida, preparando dados...');

            $orderData = [
                'order_number' => $sale->id,
                'date' => $sale->created_at->format('d/m/Y H:i'),
                'items' => $sale->saleItems->map(function ($item) {
                    return [
                        'name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'subtotal' => $item->subtotal
                    ];
                })->toArray(),
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount ?? 0,
                'delivery_fee' => $sale->delivery_fee ?? 0,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'order_type' => $sale->type, // 'balcao' ou 'delivery'
            ];

            if ($sale->customer) {
                $orderData['customer_name'] = $sale->customer->name;
                $orderData['customer_phone'] = $sale->customer->phone;
            }

            if ($sale->address) {
                $orderData['delivery_address'] = $sale->address;
            }

            Log::channel('printer')->info('Dados preparados:', [
                'order_number' => $orderData['order_number'],
                'items_count' => count($orderData['items']),
                'total' => $orderData['total']
            ]);

            Log::channel('printer')->info('Imprimindo cabeçalho...');
            $this->printHeader();
            
            Log::channel('printer')->info('Imprimindo pedido...');
            $this->printOrder($orderData);
            
            Log::channel('printer')->info('Imprimindo rodapé...');
            $this->printFooter();
            
            Log::channel('printer')->info('Cortando papel...');
            $this->cut();
            
            Log::channel('printer')->info('✅ IMPRESSÃO CONCLUÍDA COM SUCESSO');

        } catch (Exception $e) {
            Log::channel('printer')->error('❌ ERRO DURANTE IMPRESSÃO:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            Log::channel('printer')->info('Finalizando conexão...');
            $this->finalize();
        }
    }

    /**
     * Novo método estático para uso rápido
     */
    public static function print($sale, array $config = [])
    {
        Log::channel('printer')->info('=== CHAMADA ESTÁTICA ThermalPrinterService::print ===');
        
        // Se não há config, buscar do banco
        if (empty($config)) {
            Log::channel('printer')->info('Nenhuma config fornecida, buscando do banco...');
            $config = self::getConfigFromSettings();
        }
        
        $printer = new self();
        $printer->printSale($sale, $config);
    }

    /**
     * Imprimir cupom de teste
     */
    public function printTestReceipt()
    {
        if (!$this->isConnected) {
            throw new Exception('Impressora não conectada');
        }

        Log::channel('printer')->info('=== INICIANDO IMPRESSÃO DE TESTE ===');

        try {
            // Cabeçalho
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->setTextSize(1, 2);
            $this->printer->text("TESTE DE IMPRESSORA\n");
            $this->printer->setTextSize(1, 1);
            $this->printer->text("Doce Doce Brigaderia\n");
            
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");
            
            // Informações do teste
            $this->printer->text("Data/Hora: " . date('d/m/Y H:i:s') . "\n");
            $this->printer->text("Status: Conexão OK\n");
            $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");
            
            // Mensagem de teste
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text("\n");
            $this->printer->text("✅ IMPRESSORA FUNCIONANDO\n");
            $this->printer->text("CORRETAMENTE!\n");
            $this->printer->text("\n");
            
            // Informações técnicas
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");
            $this->printer->text("Configuração:\n");
            
            $config = self::getConfigFromSettings();
            if (isset($config['host'])) {
                $this->printer->text("Tipo: Rede (Network)\n");
                $this->printer->text("IP: " . $config['host'] . "\n");
                $this->printer->text("Porta: " . ($config['port'] ?? 9100) . "\n");
            } elseif (isset($config['windows_printer_name'])) {
                $this->printer->text("Tipo: USB/Windows\n");
                $this->printer->text("Nome: " . $config['windows_printer_name'] . "\n");
            }
            
            $this->printer->text(str_repeat('-', self::LINE_WIDTH) . "\n");
            
            // Rodapé
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text("\n");
            $this->printer->text("Este é um cupom de teste\n");
            $this->printer->text("para verificar a conexão.\n");
            $this->printer->text("\n");
            
            // Cortar papel
            $this->cut();
            
            Log::channel('printer')->info('✅ IMPRESSÃO DE TESTE CONCLUÍDA COM SUCESSO');
            
        } catch (Exception $e) {
            Log::channel('printer')->error('❌ ERRO DURANTE IMPRESSÃO DE TESTE:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }
}
