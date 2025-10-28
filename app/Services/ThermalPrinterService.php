<?php

namespace App\Services;

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
     * Configurações padrão da impressora
     */
    protected $defaultConfig = [
        'host' => '192.168.0.100',
        'port' => 9100,
        'charset' => 'windows-1252', // Para caracteres especiais em português
        'timeout' => 10,
        'vendor_code' => 'EscposDriver'
    ];

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
        $this->printer->text(str_repeat('-', 48) . "\n");
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
        $this->printer->text(str_repeat('-', 48) . "\n");

        // Cliente (se aplicável)
        if (isset($orderData['customer_name'])) {
            $this->printer->text("Cliente: " . $orderData['customer_name'] . "\n");
        }

        if (isset($orderData['customer_phone'])) {
            $this->printer->text("Tel: " . $orderData['customer_phone'] . "\n");
        }

        if (isset($orderData['delivery_address'])) {
            $this->printer->text("Entrega: " . $orderData['delivery_address'] . "\n");
        }

        $this->printer->text(str_repeat('-', 48) . "\n");

        // Itens do pedido
        foreach ($orderData['items'] as $item) {
            $name = mb_substr($item['name'], 0, 20, 'UTF-8'); // Limitar tamanho
            $quantity = $item['quantity'];
            $price = number_format($item['price'], 2, ',', '.');
            $subtotal = number_format($item['subtotal'], 2, ',', '.');

            $this->printer->text("$quantity x $name\n");
            $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
            $this->printer->text("R$ $subtotal\n");
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        }

        $this->printer->text(str_repeat('-', 48) . "\n");

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

        $this->printer->text(str_repeat('-', 48) . "\n");
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
