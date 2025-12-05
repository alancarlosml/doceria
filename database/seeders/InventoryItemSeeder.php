<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Pacote de Cacau 50%',
                'current_quantity' => 0,
                'min_quantity' => 5,
                'unit' => 'pacote',
                'notes' => 'Cacau em pó 50% para produção de doces',
                'active' => true,
            ],
            [
                'name' => 'Pacote de Trigo com fermento',
                'current_quantity' => 0,
                'min_quantity' => 10,
                'unit' => 'pacote',
                'notes' => 'Farinha de trigo com fermento para bolos e pães',
                'active' => true,
            ],
            [
                'name' => 'Pacote de Trigo sem fermento',
                'current_quantity' => 0,
                'min_quantity' => 10,
                'unit' => 'pacote',
                'notes' => 'Farinha de trigo sem fermento para massas',
                'active' => true,
            ],
            [
                'name' => 'Caixa de Leite condensado',
                'current_quantity' => 0,
                'min_quantity' => 20,
                'unit' => 'caixa',
                'notes' => 'Leite condensado para brigadeiros e doces',
                'active' => true,
            ],
            [
                'name' => 'Caixa de Creme de leite',
                'current_quantity' => 0,
                'min_quantity' => 15,
                'unit' => 'caixa',
                'notes' => 'Creme de leite para receitas diversas',
                'active' => true,
            ],
            [
                'name' => 'Ovos',
                'current_quantity' => 0,
                'min_quantity' => 30,
                'unit' => 'unidade',
                'notes' => 'Ovos para produção de bolos e doces',
                'active' => true,
            ],
            [
                'name' => 'Pacote de açúcar',
                'current_quantity' => 0,
                'min_quantity' => 10,
                'unit' => 'pacote',
                'notes' => 'Açúcar refinado para receitas',
                'active' => true,
            ],
            [
                'name' => 'Leite líquido',
                'current_quantity' => 0,
                'min_quantity' => 10,
                'unit' => 'litro',
                'notes' => 'Leite líquido para produção',
                'active' => true,
            ],
            [
                'name' => 'Itens de limpeza',
                'current_quantity' => 0,
                'min_quantity' => 5,
                'unit' => 'unidade',
                'notes' => 'Produtos de limpeza para higienização',
                'active' => true,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }

        $this->command->info('✅ Insumos iniciais criados com sucesso!');
    }
}
