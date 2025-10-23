<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Nenhuma categoria encontrada. Execute CategorySeeder primeiro.');
            return;
        }

        $products = [
            // Bolos Tradicionais
            [
                'category_id' => $categories->where('name', 'Bolos Tradicionais')->first()->id,
                'name' => 'Bolo de Chocolate',
                'description' => 'Bolo fofinho de chocolate com cobertura de brigadeiro',
                'price' => 45.00,
                'cost_price' => 15.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Bolos Tradicionais')->first()->id,
                'name' => 'Bolo de Baunilha',
                'description' => 'Bolo clássico de baunilha com recheio de doce de leite',
                'price' => 40.00,
                'cost_price' => 12.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Bolos Tradicionais')->first()->id,
                'name' => 'Bolo de Cenoura',
                'description' => 'Bolo de cenoura com cobertura de chocolate',
                'price' => 35.00,
                'cost_price' => 10.00,
                'image' => null,
                'active' => true,
            ],

            // Bolos Especiais
            [
                'category_id' => $categories->where('name', 'Bolos Especiais')->first()->id,
                'name' => 'Bolo Red Velvet',
                'description' => 'Bolo vermelho com cream cheese e calda de frutas vermelhas',
                'price' => 65.00,
                'cost_price' => 25.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Bolos Especiais')->first()->id,
                'name' => 'Bolo de Nozes',
                'description' => 'Bolo com nozes, castanhas e recheio de brigadeiro',
                'price' => 55.00,
                'cost_price' => 20.00,
                'image' => null,
                'active' => true,
            ],

            // Doces Finos
            [
                'category_id' => $categories->where('name', 'Doces Finos')->first()->id,
                'name' => 'Brigadeiro Gourmet',
                'description' => 'Brigadeiro tradicional com chocolate belga (100 unidades)',
                'price' => 80.00,
                'cost_price' => 25.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Doces Finos')->first()->id,
                'name' => 'Beijinho',
                'description' => 'Beijinho com coco ralado (100 unidades)',
                'price' => 70.00,
                'cost_price' => 20.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Doces Finos')->first()->id,
                'name' => 'Casadinho',
                'description' => 'Dois biscoitos com recheio de doce de leite (100 unidades)',
                'price' => 75.00,
                'cost_price' => 22.00,
                'image' => null,
                'active' => true,
            ],

            // Tortas
            [
                'category_id' => $categories->where('name', 'Tortas')->first()->id,
                'name' => 'Torta de Limão',
                'description' => 'Torta com mousse de limão e merengue',
                'price' => 50.00,
                'cost_price' => 18.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Tortas')->first()->id,
                'name' => 'Torta de Maçã',
                'description' => 'Torta americana de maçã com canela',
                'price' => 48.00,
                'cost_price' => 16.00,
                'image' => null,
                'active' => true,
            ],

            // Pães
            [
                'category_id' => $categories->where('name', 'Pães')->first()->id,
                'name' => 'Pão Francês',
                'description' => 'Pão francês tradicional (unidade)',
                'price' => 1.50,
                'cost_price' => 0.40,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Pães')->first()->id,
                'name' => 'Pão de Leite',
                'description' => 'Pão macio com leite (unidade)',
                'price' => 2.00,
                'cost_price' => 0.60,
                'image' => null,
                'active' => true,
            ],

            // Salgados
            [
                'category_id' => $categories->where('name', 'Salgados')->first()->id,
                'name' => 'Coxinha de Frango',
                'description' => 'Coxinha de frango com catupiry (100 unidades)',
                'price' => 120.00,
                'cost_price' => 40.00,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Salgados')->first()->id,
                'name' => 'Esfiha de Carne',
                'description' => 'Esfiha de carne moída (100 unidades)',
                'price' => 100.00,
                'cost_price' => 35.00,
                'image' => null,
                'active' => true,
            ],

            // Bebidas
            [
                'category_id' => $categories->where('name', 'Bebidas')->first()->id,
                'name' => 'Refrigerante Coca-Cola',
                'description' => 'Refrigerante Coca-Cola 2L',
                'price' => 8.00,
                'cost_price' => 4.50,
                'image' => null,
                'active' => true,
            ],
            [
                'category_id' => $categories->where('name', 'Bebidas')->first()->id,
                'name' => 'Suco Natural de Laranja',
                'description' => 'Suco de laranja natural 1L',
                'price' => 12.00,
                'cost_price' => 6.00,
                'image' => null,
                'active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
