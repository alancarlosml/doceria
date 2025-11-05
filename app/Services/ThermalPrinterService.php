<?php

namespace App\Services;

use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Exception;

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
     * Obter configurações do banco de dados ou usar padrões
     *
     * @return array
     */
    public static function getConfigFromSettings(): array
    {
        $printerType = Setting::get('printer_type');
        
        // Se não há tipo configurado, verificar se há configuração padrão de Windows
        if (!$printerType) {
            $default = new self();
            // Se tem windows_printer_name configurado por padrão, usar Windows
            if (isset($default->defaultConfig['windows_printer_name'])) {
                return ['windows_printer_name' => $default->defaultConfig['windows_printer_name']];
            }
            // Caso contrário, usar rede como padrão
            return [];
        }
        
        if ($printerType === 'windows') {
            $windowsName = Setting::get('printer_windows_name');
            if ($windowsName) {
                return ['windows_printer_name' => $windowsName];
            }
            // Se está configurado como Windows mas não tem nome salvo, usar padrão
            $default = new self();
            if (isset($default->defaultConfig['windows_printer_name'])) {
                return ['windows_printer_name' => $default->defaultConfig['windows_printer_name']];
            }
        }
        
        // Configuração de rede
        $host = Setting::get('printer_host');
        $port = Setting::get('printer_port', 9100);
        
        if ($host) {
            return [
                'host' => $host,
                'port' => $port,
            ];
        }
        
        // Retornar configuração padrão se nada estiver salvo
        $default = new self();
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
        $config = array_merge($this->defaultConfig, $config);

        try {
            if (isset($config['file_path'])) {
                // Impressão em arquivo (para desenvolvimento)
                $this->connector = new FilePrintConnector($config['file_path']);
            } elseif (isset($config['windows_printer_name'])) {
                // Impressão via USB/Windows
                $this->connector = new WindowsPrintConnector($config['windows_printer_name']);
            } else {
                // Impressão via rede (padrão)
                $this->connector = new NetworkPrintConnector($config['host'], $config['port']);
            }

            $this->printer = new Printer($this->connector);

            $this->isConnected = true;
            return true;

        } catch (Exception $e) {
            $this->isConnected = false;
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
        $this->connect($config);

        try {
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

            $this->printHeader();
            $this->printOrder($orderData);
            $this->printFooter();
            $this->cut();

        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->finalize();
        }
    }

    /**
     * Novo método estático para uso rápido
     */
    public static function print($sale, array $config = [])
    {
        $printer = new self();
        $printer->printSale($sale, $config);
    }
}
