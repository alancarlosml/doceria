<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificar se o usuário está autenticado e tem permissão para editar produtos
        return auth()->check() && (
            auth()->user()->hasPermission('products.update') ||
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('gestor')
        );
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome do produto não pode ter mais de 255 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço não pode ser negativo.',
            'cost_price.numeric' => 'O preço de custo deve ser um número.',
            'cost_price.min' => 'O preço de custo não pode ser negativo.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou gif.',
            'image.max' => 'A imagem não pode ter mais de 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalizar valores monetários brasileiros
        if ($this->has('price')) {
            $this->merge([
                'price' => $this->normalizeMonetaryValue($this->price),
            ]);
        }

        if ($this->has('cost_price')) {
            $this->merge([
                'cost_price' => $this->normalizeMonetaryValue($this->cost_price),
            ]);
        }
    }

    private function normalizeMonetaryValue($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);
        
        // Se contém vírgula, assume formato brasileiro (1.234,56)
        if (str_contains($value, ',')) {
            // Remove pontos (separadores de milhar) e substitui vírgula por ponto
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        
        return $value;
    }
}
