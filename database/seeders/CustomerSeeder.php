<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Maria Silva',
                'phone' => '(11) 99999-9999',
                'email' => 'maria.silva@email.com',
                'cpf' => '123.456.789-00',
                'address' => 'Rua das Flores, 123',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01234-567',
            ],
            [
                'name' => 'João Santos',
                'phone' => '(11) 88888-8888',
                'email' => 'joao.santos@email.com',
                'cpf' => '987.654.321-00',
                'address' => 'Av. Paulista, 456',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01310-100',
            ],
            [
                'name' => 'Ana Costa',
                'phone' => '(11) 77777-7777',
                'email' => 'ana.costa@email.com',
                'cpf' => '456.789.123-00',
                'address' => 'Rua Augusta, 789',
                'neighborhood' => 'Consolação',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01305-000',
            ],
            [
                'name' => 'Carlos Oliveira',
                'phone' => '(11) 66666-6666',
                'email' => 'carlos.oliveira@email.com',
                'cpf' => '789.123.456-00',
                'address' => 'Rua da Consolação, 321',
                'neighborhood' => 'Cerqueira César',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01416-001',
            ],
            [
                'name' => 'Fernanda Lima',
                'phone' => '(11) 55555-5555',
                'email' => 'fernanda.lima@email.com',
                'cpf' => '321.654.987-00',
                'address' => 'Alameda Santos, 654',
                'neighborhood' => 'Jardins',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01418-100',
            ],
            [
                'name' => 'Roberto Pereira',
                'phone' => '(11) 44444-4444',
                'email' => 'roberto.pereira@email.com',
                'cpf' => '654.987.321-00',
                'address' => 'Rua Oscar Freire, 987',
                'neighborhood' => 'Jardins',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01426-001',
            ],
            [
                'name' => 'Juliana Alves',
                'phone' => '(11) 33333-3333',
                'email' => 'juliana.alves@email.com',
                'cpf' => '147.258.369-00',
                'address' => 'Av. Brigadeiro Faria Lima, 1357',
                'neighborhood' => 'Itaim Bibi',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '04538-133',
            ],
            [
                'name' => 'Marcos Souza',
                'phone' => '(11) 22222-2222',
                'email' => 'marcos.souza@email.com',
                'cpf' => '963.852.741-00',
                'address' => 'Rua Joaquim Floriano, 2468',
                'neighborhood' => 'Itaim Bibi',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '04534-004',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
