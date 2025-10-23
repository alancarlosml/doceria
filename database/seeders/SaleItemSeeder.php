<?php

namespace Database\Seeders;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales = Sale::all();
        $products = Product::all();

        if ($sales->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Vendas ou produtos não encontrados. Execute os seeders necessários primeiro.');
            return;
        }

        $saleItems = [
            [
                'sale_id' => $sales->first()->id,
                'product_id' => $products->where('name', 'Bolo de Chocolate')->first()->id,
                'quantity' => 1,
                'unit_price' => 45.00,
                'subtotal' => 45.00,
                'notes' => null,
            ],
            [
                'sale_id' => $sales->first()->id,
                'product_id' => $products->where('name', 'Brigadeiro Gourmet')->first()->id,
                'quantity' => 50,
                'unit_price' => 0.80,
                'subtotal' => 40.00,
                'notes' => 'Para festa de aniversário',
            ],
            [
                'sale_id' => $sales->skip(1)->first()->id,
                'product_id' => $products->where('name', 'Bolo Red Velvet')->first()->id,
                'quantity' => 1,
                'unit_price' => 65.00,
                'subtotal' => 65.00,
                'notes' => null,
            ],
            [
                'sale_id' => $sales->skip(1)->first()->id,
                'product_id' => $products->where('name', 'Torta de Limão')->first()->id,
                'quantity' => 1,
                'unit_price' => 50.00,
                'subtotal' => 50.00,
                'notes' => 'Entrega na sexta-feira',
            ],
            [
                'sale_id' => $sales->skip(1)->first()->id,
                'product_id' => $products->where('name', 'Refrigerante Coca-Cola')->first()->id,
                'quantity' => 1,
                'unit_price' => 8.00,
                'subtotal' => 8.00,
                'notes' => null,
            ],
            [
                'sale_id' => $sales->skip(2)->first()->id,
                'product_id' => $products->where('name', 'Coxinha de Frango')->first()->id,
                'quantity' => 100,
                'unit_price' => 1.20,
                'subtotal' => 120.00,
                'notes' => 'Para evento corporativo',
            ],
        ];

        foreach ($saleItems as $item) {
            SaleItem::create($item);
        }
    }
}
