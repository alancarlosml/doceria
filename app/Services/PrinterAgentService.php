<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PrinterAgentService
{
    /**
     * URL base do agente local
     */
    const AGENT_BASE_URL = 'http://localhost:8080';

    /**
     * Timeout para requisições HTTP (em segundos)
     */
    const REQUEST_TIMEOUT = 5;

    /**
     * Verificar se o agente está rodando
     *
     * @return bool
     */
    public static function isAgentRunning(): bool
    {
        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->get(self::AGENT_BASE_URL . '/status');

            return $response->successful() && $response->json('status') === 'running';
        } catch (Exception $e) {
            Log::channel('printer')->debug('Agente não está rodando', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obter status completo do agente
     *
     * @return array|null
     */
    public static function getAgentStatus(): ?array
    {
        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->get(self::AGENT_BASE_URL . '/status');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::channel('printer')->debug('Erro ao obter status do agente', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Listar impressoras disponíveis via agente
     *
     * @return array
     */
    public static function getAvailablePrinters(): array
    {
        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->get(self::AGENT_BASE_URL . '/printers');

            if ($response->successful()) {
                $data = $response->json();
                return $data['printers'] ?? [];
            }

            return [];
        } catch (Exception $e) {
            Log::channel('printer')->debug('Erro ao listar impressoras via agente', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Enviar comando de impressão para o agente
     *
     * @param mixed $sale Objeto Sale ou array com dados do recibo
     * @param string|null $authToken Token de autenticação (opcional)
     * @return bool
     */
    public static function printReceipt($sale, ?string $authToken = null): bool
    {
        Log::channel('printer')->info('=== TENTANDO IMPRESSÃO VIA AGENTE ===');

        try {
            // Converter Sale para array de dados do recibo
            $receiptData = self::prepareReceiptData($sale);

            Log::channel('printer')->info('Dados do recibo preparados', [
                'order_number' => $receiptData['order_number'] ?? 'N/A'
            ]);

            // Preparar headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if ($authToken) {
                $headers['X-Auth-Token'] = $authToken;
            }

            // Enviar requisição para o agente
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders($headers)
                ->post(self::AGENT_BASE_URL . '/print', $receiptData);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['success'] ?? false) {
                    Log::channel('printer')->info('✅ Impressão via agente concluída com sucesso');
                    return true;
                } else {
                    Log::channel('printer')->error('❌ Agente retornou erro', [
                        'error' => $result['error'] ?? 'Erro desconhecido'
                    ]);
                    return false;
                }
            } else {
                Log::channel('printer')->error('❌ Erro na resposta do agente', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (Exception $e) {
            Log::channel('printer')->error('❌ Erro ao enviar impressão para agente', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return false;
        }
    }

    /**
     * Preparar dados do recibo a partir de um objeto Sale
     *
     * @param mixed $sale
     * @return array
     */
    protected static function prepareReceiptData($sale): array
    {
        // Se já for array, retornar como está
        if (is_array($sale)) {
            return $sale;
        }

        // Se for objeto Sale do Laravel
        $receiptData = [
            'business_name' => 'Doceria Delícia',
            'order_number' => $sale->id ?? null,
            'date' => isset($sale->created_at) ? $sale->created_at->format('d/m/Y H:i') : date('d/m/Y H:i'),
            'items' => [],
            'subtotal' => $sale->subtotal ?? 0,
            'discount' => $sale->discount ?? 0,
            'delivery_fee' => $sale->delivery_fee ?? 0,
            'total' => $sale->total ?? 0,
            'payment_method' => $sale->payment_method ?? null,
            'payment_methods_split' => $sale->payment_methods_split ?? null,
            'amount_received' => $sale->amount_received ?? null,
            'change_amount' => $sale->change_amount ?? null,
            'order_type' => $sale->type ?? 'balcao',
            'footer' => 'Obrigado pela preferência!'
        ];

        // Adicionar dados do cliente
        if (isset($sale->customer)) {
            $receiptData['customer_name'] = $sale->customer->name ?? null;
            $receiptData['customer_phone'] = $sale->customer->phone ?? null;
        }

        // Adicionar endereço de entrega
        if (isset($sale->address)) {
            $receiptData['delivery_address'] = $sale->address;
        }

        // Adicionar itens
        if (isset($sale->saleItems)) {
            $receiptData['items'] = $sale->saleItems->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Produto',
                    'quantity' => $item->quantity ?? 1,
                    'price' => $item->unit_price ?? 0,
                    'subtotal' => $item->subtotal ?? 0
                ];
            })->toArray();
        }

        return $receiptData;
    }

    /**
     * Configurar impressora padrão no agente
     *
     * @param string $printerName
     * @param string|null $authToken
     * @return bool
     */
    public static function setDefaultPrinter(string $printerName, ?string $authToken = null): bool
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if ($authToken) {
                $headers['X-Auth-Token'] = $authToken;
            }

            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders($headers)
                ->post(self::AGENT_BASE_URL . '/config', [
                    'printerName' => $printerName
                ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::channel('printer')->error('Erro ao configurar impressora no agente', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obter configuração do agente
     *
     * @param string|null $authToken
     * @return array|null
     */
    public static function getAgentConfig(?string $authToken = null): ?array
    {
        try {
            $headers = [
                'Accept' => 'application/json'
            ];

            if ($authToken) {
                $headers['X-Auth-Token'] = $authToken;
            }

            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders($headers)
                ->get(self::AGENT_BASE_URL . '/config');

            if ($response->successful()) {
                $data = $response->json();
                return $data['config'] ?? null;
            }

            return null;
        } catch (Exception $e) {
            Log::channel('printer')->debug('Erro ao obter configuração do agente', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
