<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use App\Models\CashRegister;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $expenses = [
            [
                'user_id' => $users->first()->id,
                'type' => 'saida',
                'description' => 'Compra de farinha de trigo - 50kg',
                'amount' => 150.00,
                'date' => now()->subDays(5)->format('Y-m-d'),
                'payment_method' => 'pix',
                'notes' => 'Fornecedor: Distribuidora São Paulo',
            ],
            [
                'user_id' => $users->first()->id,
                'type' => 'saida',
                'description' => 'Compra de açúcar - 25kg',
                'amount' => 85.00,
                'date' => now()->subDays(4)->format('Y-m-d'),
                'payment_method' => 'dinheiro',
                'notes' => 'Pagamento à vista',
            ],
            [
                'user_id' => $users->first()->id,
                'type' => 'saida',
                'description' => 'Caixas para bolos - 100 unidades',
                'amount' => 45.00,
                'date' => now()->subDays(3)->format('Y-m-d'),
                'payment_method' => 'cartao_credito',
                'notes' => 'Entrega em 2 dias úteis',
            ],
            [
                'user_id' => $users->first()->id,
                'type' => 'saida',
                'description' => 'Conta de luz - Outubro/2025',
                'amount' => 320.50,
                'date' => now()->subDays(2)->format('Y-m-d'),
                'payment_method' => 'boleto',
                'notes' => 'Vencimento: 15/11/2025',
            ],
            [
                'user_id' => $users->first()->id,
                'type' => 'saida',
                'description' => 'Gasolina para entregas - 40 litros',
                'amount' => 220.00,
                'date' => now()->subDays(1)->format('Y-m-d'),
                'payment_method' => 'cartao_debito',
                'notes' => 'Posto Shell - Av. Paulista',
            ],
            [
                'user_id' => $users->first()->id,
                'type' => 'saida',
                'description' => 'Produtos de limpeza diversos',
                'amount' => 65.30,
                'date' => now()->format('Y-m-d'),
                'payment_method' => 'dinheiro',
                'notes' => 'Detergente, desinfetante, papel toalha',
            ],
            [
                'user_id' => $users->first()->id,
                'type' => 'entrada',
                'description' => 'Receita de vendas do dia',
                'amount' => 1250.00,
                'date' => now()->format('Y-m-d'),
                'payment_method' => 'dinheiro',
                'notes' => 'Total de vendas do caixa',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
