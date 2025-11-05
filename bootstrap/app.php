<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Cookie;
// use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'auth.api' => \App\Http\Middleware\APITokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Customizar redirecionamento quando não autenticado
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            return $request->expectsJson();
        });

        // Redirecionar para login quando sessão expira
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sessão expirada. Por favor, faça login novamente.'], 401);
            }

            // Salvar a URL atual para redirecionar após login
            $intendedUrl = $request->fullUrl();
            if (!$request->is('gestor*')) {
                // Salvar na sessão (método nativo do Laravel)
                session()->put('url.intended', $intendedUrl);
                // Também salvar em cookie como backup caso a sessão expire completamente
                Cookie::queue('url.intended', $intendedUrl, 60);
            }

            return redirect()->route('gestor.login')
                ->with('warning', 'Sua sessão expirou. Por favor, faça login novamente.');
        });
    })->create();
