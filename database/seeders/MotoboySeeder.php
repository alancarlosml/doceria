<?php

namespace Database\Seeders;

use App\Models\Motoboy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MotoboySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motoboys = [
            [
                'name' => 'Carlos Mendes',
                'phone' => '(11) 99999-1111',
                'cpf' => '123.456.789-01',
                'cnh' => '12345678901',
                'placa_veiculo' => 'ABC-1234',
                'active' => true,
            ],
            [
                'name' => 'Roberto Lima',
                'phone' => '(11) 99999-2222',
                'cpf' => '987.654.321-02',
                'cnh' => '98765432109',
                'placa_veiculo' => 'DEF-5678',
                'active' => true,
            ],
            [
                'name' => 'André Silva',
                'phone' => '(11) 99999-3333',
                'cpf' => '456.789.123-03',
                'cnh' => '45678912304',
                'placa_veiculo' => 'GHI-9012',
                'active' => true,
            ],
            [
                'name' => 'Felipe Costa',
                'phone' => '(11) 99999-4444',
                'cpf' => '789.123.456-04',
                'cnh' => '78912345605',
                'placa_veiculo' => 'JKL-3456',
                'active' => true,
            ],
            [
                'name' => 'Lucas Pereira',
                'phone' => '(11) 99999-5555',
                'cpf' => '321.654.987-05',
                'cnh' => '32165498706',
                'placa_veiculo' => 'MNO-7890',
                'active' => true,
            ],
        ];

        foreach ($motoboys as $motoboy) {
            Motoboy::create($motoboy);
        }
    }
}
