<?php

namespace App\Http\Requests;

use App\Enums\SaleType;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends BaseSaleRequest
{
    public function authorize(): bool
    {
        // Verificar se o usuário está autenticado e tem permissão para criar vendas
        return auth()->check() && (
            auth()->user()->hasPermission('sales.create') ||
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('gestor')
        );
    }

    public function rules(): array
    {
        return array_merge($this->getCommonRules(), [
            'finalize' => ['nullable', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return $this->getCommonMessages();
    }

    protected function prepareForValidation(): void
    {
        // Validar motoboy obrigatório para delivery
        if ($this->type === SaleType::DELIVERY->value && empty($this->motoboy_id)) {
            $this->merge(['_requires_motoboy' => true]);
        }
    }
}
