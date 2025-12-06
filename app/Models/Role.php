<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'default_permissions',
    ];

    protected $casts = [
        'default_permissions' => 'array',
    ];

    /**
     * Get all users with this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Get all permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Check if this role has a specific permission.
     */
    public function hasPermission($permission)
    {
        // Se as permissões estão carregadas, verificar na collection primeiro (mais rápido)
        if ($this->relationLoaded('permissions')) {
            if (is_string($permission)) {
                return $this->permissions->contains('name', $permission);
            }
            return $this->permissions->contains('id', $permission);
        }

        // Se não estão carregadas, fazer query no banco
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }

        return $this->permissions()->where('permissions.id', $permission)->exists();
    }

    /**
     * Assign permissions to this role.
     */
    public function assignPermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $this->permissions()->attach($permissionModel->id);
                }
            } elseif (is_numeric($permission)) {
                $this->permissions()->attach($permission);
            }
        }
    }

    /**
     * Remove permissions from this role.
     */
    public function revokePermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $this->permissions()->detach($permissionModel->id);
                }
            } elseif (is_numeric($permission)) {
                $this->permissions()->detach($permission);
            }
        }
    }

    /**
     * Sync permissions for this role.
     */
    public function syncPermissions($permissions)
    {
        $permissionIds = [];

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $permissionIds[] = $permissionModel->id;
                }
            } elseif (is_numeric($permission)) {
                $permissionIds[] = $permission;
            }
        }

        $this->permissions()->sync($permissionIds);
    }
}
