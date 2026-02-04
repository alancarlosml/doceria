<?php

namespace App\Http\Requests;

use App\Enums\SaleType;

class UpdateSaleRequest extends BaseSaleRequest
{
    public function authorize(): bool
    {
        // Verificar se o usuário está autenticado e tem permissão para editar vendas
        return auth()->check() && (
            auth()->user()->hasPermission('sales.update') ||
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('gestor')
        );
    }

    public function rules(): array
    {
        return array_merge($this->getCommonRules(), [
            'finalize' => ['nullable', 'boolean'],
            'close_account' => ['nullable', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return $this->getCommonMessages();
    }
}
