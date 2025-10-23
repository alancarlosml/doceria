<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            [
                'number' => '01',
                'capacity' => 2,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '02',
                'capacity' => 2,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '03',
                'capacity' => 4,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '04',
                'capacity' => 4,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '05',
                'capacity' => 6,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '06',
                'capacity' => 6,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '07',
                'capacity' => 8,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '08',
                'capacity' => 8,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '09',
                'capacity' => 10,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => '10',
                'capacity' => 10,
                'status' => 'disponivel',
                'active' => true,
            ],
            [
                'number' => 'BALCAO',
                'capacity' => 12,
                'status' => 'disponivel',
                'active' => true,
            ],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
