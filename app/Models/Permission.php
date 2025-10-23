<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'module',
        'action',
        'description',
    ];

    /**
     * Get all roles with this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Get all users with this direct permission.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }

    /**
     * Check if permission belongs to a specific module.
     */
    public function isInModule($module)
    {
        return $this->module === $module;
    }

    /**
     * Check if permission has a specific action.
     */
    public function hasAction($action)
    {
        return $this->action === $action;
    }

    /**
     * Get permissions by module.
     */
    public static function byModule($module)
    {
        return static::where('module', $module)->get();
    }

    /**
     * Get permissions by action.
     */
    public static function byAction($action)
    {
        return static::where('action', $action)->get();
    }
}
