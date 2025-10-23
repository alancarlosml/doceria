<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AuthToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and return token.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            $user = Auth::user();

            // Check if user is active
            if (!$user->active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário inativo'
                ], 401);
            }

            // Revoke all previous tokens
            $user->revokeAllTokens();

            // Create new API token
            $authToken = $user->createAuthToken('web-api');

            $token = $authToken->token;

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->getRoleName(),
                        'permissions' => $user->permissions->pluck('name'),
                        'active' => $user->active,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Get token from Authorization header
            $token = $request->bearerToken();

            if ($token) {
                $request->user()->revokeToken($token);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user information.
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Load roles and permissions
            $user->load(['roles', 'permissions']);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->getRoleName(),
                        'permissions' => $user->permissions->pluck('name'),
                        'active' => $user->active,
                        'created_at' => $user->created_at,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter dados do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh user token (optional).
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Revoke current token
            $currentToken = $request->bearerToken();
            $user->revokeToken($currentToken);

            // Create new token
            $authToken = $user->createAuthToken('web-api');
            $token = $authToken->token;

            return response()->json([
                'success' => true,
                'message' => 'Token atualizado com sucesso',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Web Login - Traditional session-based login.
     */
    public function webLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
                return back()
                    ->withErrors(['email' => 'Credenciais inválidas'])
                    ->withInput();
            }

            $user = Auth::user();

            // Check if user is active
            if (!$user->active) {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Usuário inativo'])
                    ->withInput();
            }

            // Redirect to dashboard
            return redirect()->route('gestor.dashboard')
                ->with('success', 'Login realizado com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['email' => 'Erro interno do servidor'])
                ->withInput();
        }
    }

    /**
     * Web Logout.
     */
    public function webLogout()
    {
        Auth::logout();
        return redirect()->route('gestor.login')
            ->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha atual incorreta'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Revoke all tokens (force logout from other devices)
            $user->revokeAllTokens();

            // Create new token
            $authToken = $user->createAuthToken('web-api', null, null, 60 * 24); // 24 hours placeholder
            $token = $authToken->token;

            return response()->json([
                'success' => true,
                'message' => 'Senha atualizada com sucesso',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar senha',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
