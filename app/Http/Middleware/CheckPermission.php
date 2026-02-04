<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('gestor.login');
        }

        // Forçar refresh do modelo para garantir dados atualizados
        $user->refresh();
        $user->load(['roles.permissions']);

        // Admin tem acesso total
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Verificar se o usuário tem a permissão requerida
        $permissionModel = \App\Models\Permission::where('name', $permission)->first();

        if (!$permissionModel) {
            abort(403, 'Permissão não encontrada no sistema.');
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
