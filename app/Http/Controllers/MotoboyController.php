<?php

namespace App\Http\Controllers;

use App\Models\Motoboy;
use Illuminate\Http\Request;

class MotoboyController extends Controller
{
    /**
     * Display a listing of motoboys.
     */
    public function index(Request $request)
    {
        $query = Motoboy::query();

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        // Search by name, phone or CPF
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('placa_veiculo', 'like', "%{$search}%");
            });
        }

        $motoboys = $query->orderBy('name')->paginate(15);

        return view('admin.motoboy.motoboys', compact('motoboys'));
    }

    /**
     * Show the form for creating a new motoboy.
     */
    public function create()
    {
        $isEditing = false;
        return view('admin.motoboy.motoboy-form', compact('isEditing'));
    }

    /**
     * Store a newly created motoboy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:motoboys',
            'cnh' => 'nullable|string|max:20',
            'placa_veiculo' => 'nullable|string|max:10',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        Motoboy::create($validated);

        return redirect()->route('motoboys.index')
            ->with('success', 'Motoboy criado com sucesso!');
    }

    /**
     * Display the specified motoboy.
     */
    public function show(Motoboy $motoboy)
    {
        $recentDeliveries = $motoboy->sales()
            ->where('type', 'delivery')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalDeliveries = $motoboy->sales()->where('type', 'delivery')->count();
        $totalEarnings = $motoboy->sales()->where('type', 'delivery')->sum('total');
        $totalFee = $motoboy->sales()->where('type', 'delivery')->sum('delivery_fee');

        return view('admin.motoboy.motoboy-show', compact('motoboy', 'recentDeliveries', 'totalDeliveries', 'totalEarnings', 'totalFee'));
    }

    /**
     * Show the form for editing the specified motoboy.
     */
    public function edit(Motoboy $motoboy)
    {
        $isEditing = true;
        return view('admin.motoboy.motoboy-form', compact('motoboy', 'isEditing'));
    }

    /**
     * Update the specified motoboy.
     */
    public function update(Request $request, Motoboy $motoboy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:motoboys,cpf,' . $motoboy->id,
            'cnh' => 'nullable|string|max:20',
            'placa_veiculo' => 'nullable|string|max:10',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active');

        $motoboy->update($validated);

        return redirect()->route('motoboys.index')
            ->with('success', 'Motoboy atualizado com sucesso!');
    }

    /**
     * Remove the specified motoboy.
     */
    public function destroy(Motoboy $motoboy)
    {
        // Check if motoboy has deliveries
        if ($motoboy->sales()->count() > 0) {
            return redirect()->route('motoboys.index')
                ->with('error', 'Não é possível excluir motoboy que possui entregas!');
        }

        $motoboy->delete();

        return redirect()->route('motoboys.index')
            ->with('success', 'Motoboy excluído com sucesso!');
    }

    /**
     * Toggle motoboy active status.
     */
    public function toggleStatus(Motoboy $motoboy)
    {
        $motoboy->update(['active' => !$motoboy->active]);

        return response()->json([
            'success' => true,
            'active' => $motoboy->active,
            'message' => $motoboy->active ? 'Motoboy ativado!' : 'Motoboy desativado!'
        ]);
    }

    /**
     * Get active motoboys for API.
     */
    public function getActive()
    {
        $motoboys = Motoboy::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'placa_veiculo']);

        return response()->json($motoboys);
    }
}
