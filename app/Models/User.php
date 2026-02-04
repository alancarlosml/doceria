<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    // Relationships
    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    public function authTokens(): HasMany
    {
        return $this->hasMany(AuthToken::class);
    }

    // Query Scopes
    public function scopeActive($query): void
    {
        $query->where('active', true);
    }

    public function scopeInactive($query): void
    {
        $query->where('active', false);
    }

    public function scopeSearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Business Logic Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGestor(): bool
    {
        return $this->role === 'gestor';
    }

    public function isAtendente(): bool
    {
        return $this->role === 'atendente';
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function hasAnyRole(): bool
    {
        return $this->roles()->count() > 0;
    }

    public static function hasActiveAdmin(): bool
    {
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            return false;
        }

        return self::whereHas('roles', function($q) use ($adminRole) {
            $q->where('roles.id', $adminRole->id);
        })->where('active', true)->count() > 0;
    }

    public function auditPermissionChange(string $actionType, ?Permission $permission = null, ?Role $role = null, ?string $details = null): void
    {
        $auditData = [
            'action_type' => $actionType,
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ];

        if ($permission) {
            $auditData['permission_id'] = $permission->id;
        }

        if ($role) {
            $auditData['role_id'] = $role->id;
        }

        $this->permissionAudits()->create($auditData);
    }

    public function permissionAudits()
    {
        return $this->hasMany(PermissionAudit::class);
    }

    /**
     * Assign a role to user.
     */
    public function assignRole(Role|string|int $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->attach($role);
        }
    }

    /**
     * Remove a role from user.
     */
    public function revokeRole(Role|string|int $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role);
        }
    }

    /**
     * Sync user's roles.
     */
    public function syncRoles(array $roles): void
    {
        $roleIds = [];

        foreach ($roles as $role) {
            if (is_numeric($role)) {
                $roleIds[] = (int) $role;
            } else {
                $roleModel = Role::where('name', $role)->first();
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            }
        }

        $this->roles()->sync($roleIds);
    }

    /**
     * Sync user's direct permissions.
     */
    public function syncPermissions(array $permissions, bool $throughRoles = false): void
    {
        $permissionIds = [];

        foreach ($permissions as $permission) {
            if (is_numeric($permission)) {
                $permissionIds[] = (int) $permission;
            } else {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $permissionIds[] = $permissionModel->id;
                }
            }
        }

        $this->permissions()->sync($permissionIds);
    }

    /**
     * Assign a permission directly to user.
     */
    public function assignPermission(Permission|string|int $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Remove a permission from user.
     */
    public function revokePermission(Permission|string|int $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission);
        }
    }

    /**
     * Revoke permission from user (alias for revokePermission).
     */
    public function revokePermissionTo(Permission|string|int $permission): void
    {
        $this->revokePermission($permission);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(Role|string|int $role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        return $this->roles()->where('id', $role)->exists();
    }

    /**
     * Check if user has a specific permission (direct or through roles).
     *
     * Logic: User has permission if:
     * 1. Permission exists through roles OR
     * 2. Permission exists as direct permission
     * 3. AND permission was NOT explicitly removed from user
     *
     * Direct permissions OVERRIDE role permissions (can add or remove)
     */
    public function hasPermission(Permission|string|int $permission): bool
    {
        // Admin tem acesso total
        if ($this->hasRole('admin')) {
            return true;
        }

        // Get permission ID regardless of input type
        $permissionId = null;
        if (is_string($permission)) {
            $permissionModel = Permission::where('name', $permission)->first();
            $permissionId = $permissionModel ? $permissionModel->id : null;
        } elseif (is_numeric($permission)) {
            $permissionId = $permission;
        }

        if (!$permissionId) {
            return false;
        }

        // Check permissions through roles first (roles usually have more permissions)
        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        foreach ($this->roles as $role) {
            if (!$role->relationLoaded('permissions')) {
                $role->load('permissions');
            }
            if ($role->hasPermission($permissionId)) {
                return true;
            }
        }

        // Check direct permissions (can override role permissions)
        $userPermission = $this->permissions()
            ->where('permission_id', $permissionId)
            ->first();

        if ($userPermission) {
            // Direct permission overrides role permissions
            $action = $userPermission->pivot->action ?? 'grant';
            return $action !== 'revoke';
        }

        return false;
    }

    /**
     * Check if user has a specific permission for a module.
     */
    public function canAccess(string $module, ?string $action = null): bool
    {
        $permissionName = $action ? "{$module}.{$action}" : $module;
        return $this->hasPermission($permissionName);
    }

    /**
     * Get user's role name (prioritize highest level).
     */
    public function getRoleName()
    {
        $roleHierarchy = ['admin' => 3, 'gestor' => 2, 'atendente' => 1];

        $highestRole = null;
        $highestLevel = 0;

        foreach ($this->roles as $role) {
            $level = $roleHierarchy[$role->name] ?? 0;
            if ($level > $highestLevel) {
                $highestLevel = $level;
                $highestRole = $role->name;
            }
        }

        return $highestRole ?: 'atendente';
    }

    /**
     * Check if user can perform admin operations.
     */
    public function isAdminOrGestor(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor');
    }

    /**
     * Generate a new auth token.
     */
    public function createAuthToken(string $name = 'api-token', ?string $abilities = null, ?\DateTime $expiresAt = null): AuthToken
    {
        return $this->authTokens()->create([
            'token' => AuthToken::generateToken(),
            'name' => $name,
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Revoke a specific token.
     */
    public function revokeToken(AuthToken|string $token): bool
    {
        if ($token instanceof AuthToken) {
            return $token->revoke();
        }

        return $this->authTokens()->where('token', $token)->delete();
    }

    /**
     * Revoke all tokens.
     */
    public function revokeAllTokens(): int
    {
        return $this->authTokens()->delete();
    }

    /**
     * Find user by token.
     */
    public static function findByToken(string $token): ?self
    {
        $authToken = AuthToken::findByToken($token);

        if (!$authToken || $authToken->isExpired()) {
            return null;
        }

        // Update last used timestamp
        $authToken->updateLastUsed();

        return $authToken->user;
    }
}
