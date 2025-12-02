<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\CarouselBanner;
use App\Services\ThermalPrinterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $carouselBanners = CarouselBanner::orderBy('order')->get();
        return view('admin.settings.index', compact('carouselBanners'));
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request)
    {
        $request->validate([
            'banner_message' => 'nullable|string|max:500',
            'banner_active' => 'nullable|boolean',
            'store_status' => 'required|in:open,closed',
            'printer_type' => 'nullable|in:network,windows',
            'printer_host' => 'nullable|string|max:255',
            'printer_port' => 'nullable|integer|min:1|max:65535',
            'printer_windows_name' => 'nullable|string|max:255',
        ]);

        // Salvar status da loja primeiro
        Setting::set('store_status', $request->input('store_status'), 'string');

        // Salvar se banner está ativo (sempre salvar como boolean)
        Setting::set('banner_active', $request->has('banner_active') ? true : false, 'boolean');

        // Salvar mensagem do banner
        if ($request->has('banner_message')) {
            Setting::set('banner_message', $request->input('banner_message'), 'string');
        }

        // Salvar configurações da impressora
        if ($request->has('printer_type')) {
            Setting::set('printer_type', $request->input('printer_type'), 'string');
            
            if ($request->input('printer_type') === 'network') {
                if ($request->has('printer_host')) {
                    Setting::set('printer_host', $request->input('printer_host'), 'string');
                }
                if ($request->has('printer_port')) {
                    Setting::set('printer_port', $request->input('printer_port'), 'integer');
                }
            } elseif ($request->input('printer_type') === 'windows') {
                if ($request->has('printer_windows_name')) {
                    Setting::set('printer_windows_name', $request->input('printer_windows_name'), 'string');
                }
            }
        }

        return redirect()->back()->with('success', 'Configurações salvas com sucesso!');
    }

    /**
     * Store a new carousel banner
     */
    public function storeBanner(Request $request)
    {
        $request->validate([
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
            'banner_title' => 'nullable|string|max:100',
            'banner_description' => 'nullable|string|max:255',
            'banner_link' => 'nullable|url|max:255',
        ]);

        // Upload da imagem
        $path = $request->file('banner_image')->store('carousel-banners', 'public');

        // Pegar a maior ordem atual e adicionar 1
        $maxOrder = CarouselBanner::max('order') ?? 0;

        CarouselBanner::create([
            'image' => $path,
            'title' => $request->input('banner_title'),
            'description' => $request->input('banner_description'),
            'link' => $request->input('banner_link'),
            'order' => $maxOrder + 1,
            'active' => true,
        ]);

        return redirect()->back()->with('success', 'Banner adicionado com sucesso!');
    }

    /**
     * Update banner order
     */
    public function updateBannerOrder(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:carousel_banners,id',
            'banners.*.order' => 'required|integer',
        ]);

        foreach ($request->input('banners') as $bannerData) {
            CarouselBanner::where('id', $bannerData['id'])->update(['order' => $bannerData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Ordem atualizada!']);
    }

    /**
     * Toggle banner active status
     */
    public function toggleBanner(CarouselBanner $banner)
    {
        $banner->update(['active' => !$banner->active]);

        $status = $banner->active ? 'ativado' : 'desativado';
        return redirect()->back()->with('success', "Banner {$status} com sucesso!");
    }

    /**
     * Delete a carousel banner
     */
    public function destroyBanner(CarouselBanner $banner)
    {
        // Deletar a imagem do storage
        $banner->deleteImage();
        
        // Deletar o registro
        $banner->delete();

        return redirect()->back()->with('success', 'Banner removido com sucesso!');
    }

    /**
     * Test printer connection and print a test receipt
     */
    public function testPrinter(Request $request)
    {
        $printerService = null;
        
        try {
            $printerService = new ThermalPrinterService();
            
            // Get configuration from settings
            $config = ThermalPrinterService::getConfigFromSettings();
            
            if (empty($config)) {
                return redirect()->back()->with('error', 'Por favor, configure a impressora antes de testar.');
            }
            
            // Connect to printer
            $printerService->connect($config);
            
            // Print test receipt
            $printerService->printTestReceipt();
            
            // Finalize
            $printerService->finalize();
            
            return redirect()->back()->with('success', '✅ Teste de impressão realizado com sucesso! Verifique se o cupom foi impresso.');
            
        } catch (\Exception $e) {
            // Ensure connection is closed even on error
            if ($printerService && $printerService->isConnected()) {
                try {
                    $printerService->finalize();
                } catch (\Exception $finalizeException) {
                    // Ignore errors during finalization
                }
            }
            
            return redirect()->back()->with('error', '❌ Erro ao testar impressora: ' . $e->getMessage());
        }
    }
}
