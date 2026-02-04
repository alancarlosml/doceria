<?php

namespace App\Http\Requests;

use App\Enums\SaleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificar se o usuário está autenticado e tem permissão para atualizar status de vendas
        return auth()->check() && (
            auth()->user()->hasPermission('sales.update_status') ||
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('gestor')
        );
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(SaleStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status é obrigatório.',
            'status.enum' => 'Status inválido.',
        ];
    }
}
