<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    /**
     * Get the cash registers opened by this user.
     */
    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    /**
     * Get the sales made by this user.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the expenses recorded by this user.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is gestor.
     */
    public function isGestor()
    {
        return $this->role === 'gestor';
    }

    /**
     * Check if user is atendente.
     */
    public function isAtendente()
    {
        return $this->role === 'atendente';
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Get user's roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Get user's direct permissions.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    /**
     * Assign a role to user.
     */
    public function assignRole($role)
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
    public function revokeRole($role)
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
    public function syncRoles($roles)
    {
        $roleIds = [];

        foreach ($roles as $role) {
            if (is_string($role)) {
                $roleModel = Role::where('name', $role)->first();
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            } elseif (is_numeric($role)) {
                $roleIds[] = $role;
            }
        }

        $this->roles()->sync($roleIds);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        return $this->roles()->where('id', $role)->exists();
    }

    /**
     * Check if user has a specific permission (direct or through roles).
     */
    public function hasPermission($permission)
    {
        // Check direct permissions
        if (is_string($permission)) {
            if ($this->permissions()->where('name', $permission)->exists()) {
                return true;
            }
        } elseif (is_numeric($permission)) {
            if ($this->permissions()->where('id', $permission)->exists()) {
                return true;
            }
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a specific permission for a module.
     */
    public function canAccess($module, $action = null)
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
    public function isAdminOrGestor()
    {
        return $this->hasRole('admin') || $this->hasRole('gestor');
    }

    /**
     * Get user's auth tokens.
     */
    public function authTokens()
    {
        return $this->hasMany(AuthToken::class);
    }

    /**
     * Generate a new auth token.
     */
    public function createAuthToken($name = 'api-token', $abilities = null, $expiresAt = null)
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
    public function revokeToken($token)
    {
        if ($token instanceof AuthToken) {
            return $token->revoke();
        }

        return $this->authTokens()->where('token', $token)->delete();
    }

    /**
     * Revoke all tokens.
     */
    public function revokeAllTokens()
    {
        return $this->authTokens()->delete();
    }

    /**
     * Find user by token.
     */
    public static function findByToken($token)
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
