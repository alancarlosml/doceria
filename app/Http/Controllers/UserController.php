<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filter by active status
        if ($request->filled('active') && $request->active !== 'todos') {
            $query->where('active', $request->active === 'ativo');
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('admin.users.users', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('admin.users.user-form', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array|nullable',
            'roles.*' => 'exists:roles,id',
            'active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'active' => $request->has('active'),
        ]);

        // Assign roles
        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles', 'sales', 'cashRegisters');
        return view('admin.users.user-show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('admin.users.user-form', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array|nullable',
            'roles.*' => 'exists:roles,id',
            'active' => 'boolean',
        ]);

        // Update basic fields
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'active' => $request->has('active'),
        ];

        // Add password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync roles
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent deactivating the current user
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode desativar seu próprio usuário!'
            ]);
        }

        $user->update(['active' => !$user->active]);

        return response()->json([
            'success' => true,
            'active' => $user->active,
            'message' => $user->active ? 'Usuário ativado!' : 'Usuário desativado!'
        ]);
    }

    /**
     * Show the form for editing the current authenticated user profile.
     */
    public function editProfile()
    {
        $user = auth()->user();

        // If admin, can change roles, else just basic info
        if (auth()->user()->hasRole('admin')) {
            $roles = \App\Models\Role::orderBy('name')->get();
        } else {
            $roles = null;
        }

        return view('admin.users.profile', compact('user', 'roles'));
    }

    /**
     * Update the current authenticated user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'active' => 'boolean',
        ]);

        if ($user->hasRole('admin')) {
            // Admin can also update roles
            $additionalValidation = $request->validate([
                'roles' => 'array|nullable',
                'roles.*' => 'exists:roles,id',
            ]);

            if (isset($additionalValidation['roles'])) {
                $validated['roles'] = $additionalValidation['roles'];
            }
        }

        // Update basic fields
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'active' => $request->has('active'),
        ];

        // Add password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync roles if admin
        if ($user->hasRole('admin') && isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }
}
