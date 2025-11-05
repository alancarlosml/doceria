<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('admin.settings.index');
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
}
