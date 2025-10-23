<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class APITokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autenticação não fornecido'
            ], 401);
        }

        // Cache the user lookup (thanks to updated_at in auth_tokens table)
        $cacheKey = 'auth_token_' . hash('sha256', $token);

        $user = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($token) {
            return User::findByToken($token);
        });

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido ou expirado'
            ], 401);
        }

        // Set the authenticated user on the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Update last used timestamp (only every 5 minutes to reduce DB writes)
        $cacheLastUsedKey = 'auth_token_last_used_' . $user->id;
        if (!Cache::has($cacheLastUsedKey)) {
            $tokenModel = \App\Models\AuthToken::findByToken($token);
            if ($tokenModel) {
                $tokenModel->updateLastUsed();
            }
            Cache::put($cacheLastUsedKey, now(), now()->addMinutes(5));
        }

        return $next($request);
    }
}
