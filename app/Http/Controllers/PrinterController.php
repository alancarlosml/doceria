<?php

namespace App\Http\Controllers;

use App\Services\PrinterAgentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrinterController extends Controller
{
    /**
     * Verificar status do agente de impressão
     *
     * @return JsonResponse
     */
    public function agentStatus(): JsonResponse
    {
        $status = PrinterAgentService::getAgentStatus();

        if ($status) {
            return response()->json([
                'success' => true,
                'running' => true,
                'status' => $status
            ]);
        }

        return response()->json([
            'success' => true,
            'running' => false,
            'message' => 'Agente não está rodando'
        ]);
    }

    /**
     * Listar impressoras disponíveis via agente
     *
     * @return JsonResponse
     */
    public function agentPrinters(): JsonResponse
    {
        if (!PrinterAgentService::isAgentRunning()) {
            return response()->json([
                'success' => false,
                'message' => 'Agente não está rodando',
                'printers' => []
            ]);
        }

        $printers = PrinterAgentService::getAvailablePrinters();

        return response()->json([
            'success' => true,
            'printers' => $printers
        ]);
    }

    /**
     * Configurar impressora padrão no agente
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setAgentPrinter(Request $request): JsonResponse
    {
        $request->validate([
            'printer_name' => 'required|string|max:255'
        ]);

        if (!PrinterAgentService::isAgentRunning()) {
            return response()->json([
                'success' => false,
                'message' => 'Agente não está rodando'
            ], 400);
        }

        $success = PrinterAgentService::setDefaultPrinter($request->input('printer_name'));

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Impressora configurada com sucesso'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erro ao configurar impressora'
        ], 500);
    }

    /**
     * Obter configuração do agente
     *
     * @return JsonResponse
     */
    public function agentConfig(): JsonResponse
    {
        if (!PrinterAgentService::isAgentRunning()) {
            return response()->json([
                'success' => false,
                'message' => 'Agente não está rodando',
                'config' => null
            ]);
        }

        $config = PrinterAgentService::getAgentConfig();

        return response()->json([
            'success' => true,
            'config' => $config
        ]);
    }

    /**
     * Página de download do instalador
     *
     * @return \Illuminate\View\View
     */
    public function downloadPage()
    {
        return view('admin.printer-agent.download');
    }

    /**
     * Download do instalador (redireciona para arquivo ou mostra instruções)
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
        // Por enquanto, retornar instruções
        // No futuro, quando o instalador estiver pronto, fazer download direto
        return redirect()->route('printer.agent.download-page')
            ->with('info', 'O instalador será disponibilizado em breve. Por enquanto, siga as instruções na página.');
    }
}
