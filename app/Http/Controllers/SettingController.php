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
        ]);

        // Salvar status da loja primeiro
        Setting::set('store_status', $request->input('store_status'), 'string');

        // Salvar se banner está ativo (sempre salvar como boolean)
        Setting::set('banner_active', $request->has('banner_active') ? true : false, 'boolean');

        // Salvar mensagem do banner
        if ($request->has('banner_message')) {
            Setting::set('banner_message', $request->input('banner_message'), 'string');
        }

        return redirect()->back()->with('success', 'Configurações salvas com sucesso!');
    }
}
