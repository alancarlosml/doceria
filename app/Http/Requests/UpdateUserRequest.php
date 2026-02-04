<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificar se o usuário está autenticado e tem permissão para editar usuários
        // Usuários podem editar seu próprio perfil, mas apenas admins podem editar outros
        $targetUser = $this->route('user');
        
        if (!$targetUser) {
            // Se não há usuário na rota, provavelmente é edição de perfil próprio
            return auth()->check();
        }
        
        return auth()->check() && (
            auth()->id() === $targetUser->id || // Próprio perfil
            auth()->user()->hasPermission('users.update') ||
            auth()->user()->hasRole('admin')
        );
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->user?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array', 'nullable'],
            'roles.*' => ['exists:roles,id'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'roles.array' => 'Os papéis devem ser um array.',
            'roles.*.exists' => 'Um ou mais papéis selecionados são inválidos.',
        ];
    }
}
