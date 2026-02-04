<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
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
    public function create(): View
    {
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('admin.users.user-form', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
    public function show(User $user): View
    {
        $user->load('roles', 'sales', 'cashRegisters');
        return view('admin.users.user-show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('admin.users.user-form', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

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

        // Sync roles with protection
        if (isset($validated['roles'])) {
            // Check if removing admin role from an admin user
            $currentRoles = $user->roles->pluck('id')->toArray();
            $newRoles = $validated['roles'];
            $adminRole = \App\Models\Role::where('name', 'admin')->first();

            if ($adminRole && in_array($adminRole->id, $currentRoles) && !in_array($adminRole->id, $newRoles)) {
                // Check if this is the last admin
                $adminCount = \App\Models\User::whereHas('roles', function($q) use ($adminRole) {
                    $q->where('roles.id', $adminRole->id);
                })->where('active', true)->count();

                if ($adminCount <= 1) {
                    return redirect()->route('users.edit', $user)
                        ->with('error', 'Não é possível remover a role de administrador do último administrador ativo do sistema!');
                }
            }

            $user->syncRoles($newRoles);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
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
    public function toggleStatus(User $user): JsonResponse
    {
        // Prevent deactivating the current user
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode desativar seu próprio usuário!'
            ]);
        }

        // Prevent deactivating the last active admin
        if ($user->active && $user->hasRole('admin')) {
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            $adminCount = \App\Models\User::whereHas('roles', function($q) use ($adminRole) {
                $q->where('roles.id', $adminRole->id);
            })->where('active', true)->count();

            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível desativar o último administrador ativo do sistema!'
                ]);
            }
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
    public function editProfile(): View
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
    public function updateProfile(UpdateUserRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

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

        // Always process roles for admin users
        if ($user->hasRole('admin')) {
            // Sync roles regardless of whether roles were submitted (use existing roles if none submitted)
            $rolesToAssign = isset($validated['roles']) ? $validated['roles'] : $user->roles()->pluck('id')->toArray();
            $user->syncRoles($rolesToAssign);
            // Refresh the authenticated user to update role cache
            Auth::setUser($user->fresh()->load('roles'));
        }

        return redirect()->route('profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function checkIntegrity(): JsonResponse
    {
        $issues = [];

        // Check if there are any users without roles
        $usersWithoutRoles = User::whereDoesntHave('roles')->get();
        if ($usersWithoutRoles->count() > 0) {
            $issues[] = [
                'type' => 'users_without_roles',
                'severity' => 'warning',
                'count' => $usersWithoutRoles->count(),
                'message' => "{$usersWithoutRoles->count()} usuários sem role atribuída",
                'users' => $usersWithoutRoles->pluck('name', 'email')->toArray()
            ];
        }

        // Check if there are no active admins
        $hasActiveAdmin = User::hasActiveAdmin();
        if (!$hasActiveAdmin) {
            $issues[] = [
                'type' => 'no_active_admin',
                'severity' => 'critical',
                'message' => 'Não há administradores ativos no sistema!'
            ];
        }

        // Check total number of admins
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $adminCount = $adminRole ? User::whereHas('roles', function($q) use ($adminRole) {
            $q->where('roles.id', $adminRole->id);
        })->count() : 0;

        if ($adminCount === 0) {
            $issues[] = [
                'type' => 'no_admins',
                'severity' => 'critical',
                'message' => 'Não há administradores cadastrados no sistema!'
            ];
        } elseif ($adminCount === 1) {
            $issues[] = [
                'type' => 'single_admin',
                'severity' => 'warning',
                'message' => 'Existe apenas 1 administrador. Recomenda-se ter pelo menos 2.'
            ];
        }

        return response()->json([
            'status' => empty($issues) ? 'healthy' : 'issues_found',
            'issues' => $issues,
            'stats' => [
                'total_users' => User::count(),
                'active_users' => User::where('active', true)->count(),
                'admin_count' => $adminCount,
                'users_without_roles' => $usersWithoutRoles->count()
            ]
        ]);
    }

    public function auditLog(Request $request)
    {
        $query = \App\Models\PermissionAudit::with(['user', 'performedBy', 'permission', 'role'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $audits = $query->paginate(50);

        return view('admin.users.audit', compact('audits'));
    }
}
