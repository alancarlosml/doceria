<?php

namespace Database\Seeders;

use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashRegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Nenhum usuário encontrado. Execute o seeder de usuários primeiro.');
            return;
        }

        $cashRegisters = [
            [
                'user_id' => $users->first()->id,
                'opening_balance' => 200.00,
                'closing_balance' => null,
                'opened_at' => now()->subDays(1)->setHour(8)->setMinute(0)->setSecond(0),
                'closed_at' => null,
                'status' => 'aberto',
                'opening_notes' => 'Abertura do caixa para expediente',
                'closing_notes' => null,
            ],
            [
                'user_id' => $users->first()->id,
                'opening_balance' => 150.00,
                'closing_balance' => 1850.50,
                'opened_at' => now()->subDays(2)->setHour(8)->setMinute(0)->setSecond(0),
                'closed_at' => now()->subDays(1)->setHour(18)->setMinute(0)->setSecond(0),
                'status' => 'fechado',
                'opening_notes' => 'Abertura do caixa',
                'closing_notes' => 'Caixa fechado com R$ 1850,50',
            ],
            [
                'user_id' => $users->first()->id,
                'opening_balance' => 180.00,
                'closing_balance' => 2340.75,
                'opened_at' => now()->subDays(3)->setHour(8)->setMinute(0)->setSecond(0),
                'closed_at' => now()->subDays(2)->setHour(18)->setMinute(0)->setSecond(0),
                'status' => 'fechado',
                'opening_notes' => 'Abertura do caixa',
                'closing_notes' => 'Caixa fechado com R$ 2340,75',
            ],
        ];

        foreach ($cashRegisters as $cashRegister) {
            CashRegister::create($cashRegister);
        }
    }
}
