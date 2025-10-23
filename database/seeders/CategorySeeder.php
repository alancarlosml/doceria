<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bolos Tradicionais',
                'description' => 'Bolos clássicos e tradicionais',
                'active' => true,
            ],
            [
                'name' => 'Bolos Especiais',
                'description' => 'Bolos gourmet e especiais',
                'active' => true,
            ],
            [
                'name' => 'Doces Finos',
                'description' => 'Docinhos e sobremesas finas',
                'active' => true,
            ],
            [
                'name' => 'Tortas',
                'description' => 'Tortas doces e salgadas',
                'active' => true,
            ],
            [
                'name' => 'Pães',
                'description' => 'Pães artesanais e caseiros',
                'active' => true,
            ],
            [
                'name' => 'Salgados',
                'description' => 'Salgados para lanche e festas',
                'active' => true,
            ],
            [
                'name' => 'Bebidas',
                'description' => 'Refrigerantes, sucos e bebidas',
                'active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
