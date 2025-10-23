<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('Nenhum produto encontrado. Execute ProductSeeder primeiro.');
            return;
        }

        $daysOfWeek = [
            'segunda',
            'terca',
            'quarta',
            'quinta',
            'sexta',
            'sabado',
            'domingo'
        ];

        // Define disponibilidade para cada produto por dia da semana
        foreach ($products as $product) {
            $availableDays = [];

            // Pães disponíveis todos os dias
            if ($product->category->name === 'Pães') {
                $availableDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
            }
            // Bebidas disponíveis todos os dias
            elseif ($product->category->name === 'Bebidas') {
                $availableDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
            }
            // Bolos tradicionais disponíveis de segunda a sábado
            elseif ($product->category->name === 'Bolos Tradicionais') {
                $availableDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
            }
            // Bolos especiais disponíveis de quarta a domingo
            elseif ($product->category->name === 'Bolos Especiais') {
                $availableDays = ['quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
            }
            // Doces finos disponíveis de quinta a domingo
            elseif ($product->category->name === 'Doces Finos') {
                $availableDays = ['quinta', 'sexta', 'sabado', 'domingo'];
            }
            // Tortas disponíveis de sexta a domingo
            elseif ($product->category->name === 'Tortas') {
                $availableDays = ['sexta', 'sabado', 'domingo'];
            }
            // Salgados disponíveis de segunda a sexta
            elseif ($product->category->name === 'Salgados') {
                $availableDays = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];
            }

            // Se não foi definido, assume disponível todos os dias
            if (empty($availableDays)) {
                $availableDays = $daysOfWeek;
            }

            // Cria registros no menu para cada dia disponível
            foreach ($availableDays as $day) {
                Menu::create([
                    'product_id' => $product->id,
                    'day_of_week' => $day,
                    'available' => true,
                ]);
            }
        }

        $this->command->info('Cardápio criado com sucesso para ' . $products->count() . ' produtos.');
    }
}
