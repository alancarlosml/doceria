<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $permissions = Permission::with('roles')->orderBy('module')->orderBy('action')->get();
        $permissionsByModule = $permissions->groupBy('module');

        $roles = Role::with('permissions')->get();

        return view('permissions.index', compact('permissions', 'permissionsByModule', 'roles'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'label' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')
            ->with('success', 'Permissão criada com sucesso!');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load(['roles', 'users']);
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'label' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')
            ->with('success', 'Permissão atualizada com sucesso!');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'Não é possível excluir uma permissão que está atribuída a roles.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permissão excluída com sucesso!');
    }

    /**
     * Assign permissions to a role.
     */
    public function assignToRole(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::find($request->role_id);
        $role->syncPermissions($request->permission_ids ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permissões atribuídas com sucesso!'
        ]);
    }

    /**
     * Assign role to user.
     */
    public function assignRoleToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::find($request->user_id);
        $role = Role::find($request->role_id);

        // Remove existing roles and assign new one
        $user->syncRoles([$role->id]);

        return response()->json([
            'success' => true,
            'message' => 'Role atribuída com sucesso!'
        ]);
    }

    /**
     * Get users with their roles and permissions (API).
     */
    public function getUsersWithPermissions()
    {
        $users = User::with(['roles.permissions'])->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleName(),
                'permissions' => $user->permissions->pluck('name')->toArray(),
                'active' => $user->active,
            ];
        });

        return response()->json($users);
    }

    /**
     * Update user role via API.
     */
    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,gestor,atendente',
        ]);

        $role = Role::where('name', $request->role)->first();
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role não encontrada.'
            ], 404);
        }

        $user->syncRoles([$role->id]);

        return response()->json([
            'success' => true,
            'message' => 'Role do usuário atualizada com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->getRoleName(),
            ]
        ]);
    }
}
