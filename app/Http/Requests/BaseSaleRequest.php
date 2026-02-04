<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Enums\SaleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class BaseSaleRequest extends FormRequest
{
    /**
     * Get common validation rules for sale requests
     */
    protected function getCommonRules(): array
    {
        return [
            'type' => ['required', Rule::enum(SaleType::class)],
            'table_id' => ['nullable', 'exists:tables,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'motoboy_id' => ['nullable', 'exists:motoboys,id'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'payment_methods_split' => ['nullable', 'array'],
            'payment_methods_split.*.method' => ['required_with:payment_methods_split', Rule::enum(PaymentMethod::class)],
            'payment_methods_split.*.value' => ['required_with:payment_methods_split', 'numeric', 'min:0'],
            'payment_methods_split.*.amount_received' => ['nullable', 'numeric', 'min:0'],
            'payment_methods_split.*.change_amount' => ['nullable', 'numeric', 'min:0'],
            'amount_received' => ['nullable', 'numeric', 'min:0'],
            'change_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get common validation messages
     */
    protected function getCommonMessages(): array
    {
        return [
            'type.required' => 'O tipo de venda é obrigatório.',
            'type.enum' => 'Tipo de venda inválido.',
            'table_id.exists' => 'A mesa selecionada não existe.',
            'customer_id.exists' => 'O cliente selecionado não existe.',
            'motoboy_id.exists' => 'O motoboy selecionado não existe.',
            'payment_method.required' => 'O método de pagamento é obrigatório.',
            'payment_method.enum' => 'Método de pagamento inválido.',
            'items.required' => 'É necessário adicionar pelo menos um item.',
            'items.min' => 'É necessário adicionar pelo menos um item.',
            'items.*.product_id.required' => 'O produto é obrigatório.',
            'items.*.product_id.exists' => 'O produto selecionado não existe.',
            'items.*.quantity.required' => 'A quantidade é obrigatória.',
            'items.*.quantity.min' => 'A quantidade deve ser no mínimo 1.',
        ];
    }

    /**
     * Common validation logic for delivery sales
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->type === SaleType::DELIVERY->value && empty($this->motoboy_id)) {
                $validator->errors()->add('motoboy_id', 'Selecione um motoboy para delivery!');
            }
        });
    }
}
