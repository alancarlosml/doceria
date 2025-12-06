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
        // Log inicial para garantir que o middleware está sendo executado
        \Log::info('CheckPermission middleware executado', [
            'permission' => $permission,
            'url' => $request->fullUrl(),
            'user_authenticated' => Auth::check(),
        ]);

        $user = Auth::user();

        if (!$user) {
            \Log::warning('Usuário não autenticado no CheckPermission');
            return redirect()->route('gestor.login');
        }

        // Forçar refresh do modelo do banco para garantir dados atualizados
        // IMPORTANTE: Isso garante que estamos usando dados frescos do banco, não da sessão
        // Usar refresh() em vez de fresh() para manter a mesma instância
        $user->refresh();
        $user->load(['roles.permissions']);
        
        // Log para debug
        \Log::info('Usuário após refresh', [
            'user_id' => $user->id,
            'roles_count' => $user->roles->count(),
            'permissions_count' => $user->roles->flatMap->permissions->count(),
        ]);

        // Admin tem acesso total
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Verificar se o usuário tem a permissão requerida
        $permissionModel = \App\Models\Permission::where('name', $permission)->first();
        
        if (!$permissionModel) {
            \Log::error('Permissão não encontrada no banco', ['permission' => $permission]);
            abort(403, 'Permissão não encontrada no sistema.');
        }
        
        $hasPermission = $user->hasPermission($permission);
        
        // Debug detalhado - SEMPRE logar
        $debugInfo = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'permission' => $permission,
            'permission_id' => $permissionModel->id,
            'hasPermission_result' => $hasPermission,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'user_permissions_via_roles' => $user->roles->flatMap->permissions->pluck('name')->unique()->toArray(),
        ];
        
        // Verificar manualmente através das roles
        $manualCheck = false;
        foreach ($user->roles as $role) {
            $role->load('permissions');
            if ($role->permissions->contains('id', $permissionModel->id)) {
                $manualCheck = true;
                break;
            }
        }
        $debugInfo['manual_check_via_roles'] = $manualCheck;
        
        // SEMPRE logar o resultado
        \Log::info('Verificação de permissão', $debugInfo);
        
        if (!$hasPermission) {
            \Log::warning('Acesso negado - Debug detalhado', $debugInfo);
            
            // Se o check manual passou mas hasPermission falhou, há um bug - FORÇAR PERMITIR
            if ($manualCheck) {
                \Log::error('INCONSISTÊNCIA: hasPermission retornou false mas check manual retornou true! Permitindo acesso...', $debugInfo);
                // Se o check manual passou, permitir acesso mesmo que hasPermission falhou
                return $next($request);
            }
            
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
