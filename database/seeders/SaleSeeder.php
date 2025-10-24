<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Table;
use App\Models\Motoboy;
use App\Models\User;
use App\Models\CashRegister;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $tables = Table::all();
        $motoboys = Motoboy::all();
        $users = User::all();
        $cashRegisters = CashRegister::all();

        if ($customers->isEmpty() || $users->isEmpty() || $cashRegisters->isEmpty()) {
            $this->command->warn('Clientes, usuários ou caixas não encontrados. Execute os seeders necessários primeiro.');
            return;
        }

        $sales = [
            [
                'cash_register_id' => $cashRegisters->first()->id,
                'user_id' => $users->first()->id,
                'customer_id' => $customers->first()->id,
                'table_id' => $tables->first()?->id,
                'motoboy_id' => null,
                'code' => 'VEN-' . strtoupper(uniqid()),
                'type' => 'balcao',
                'status' => 'finalizado',
                'subtotal' => 85.00,
                'discount' => 0.00,
                'delivery_fee' => 0.00,
                'total' => 85.00,
                'payment_method' => 'dinheiro',
                'delivery_date' => null,
                'delivery_time' => null,
                'notes' => 'Venda no balcão',
                'delivery_address' => null,
            ],
            [
                'cash_register_id' => $cashRegisters->first()->id,
                'user_id' => $users->first()->id,
                'customer_id' => $customers->skip(1)->first()->id,
                'table_id' => $tables->skip(1)->first()?->id,
                'motoboy_id' => null,
                'code' => 'VEN-' . strtoupper(uniqid()),
                'type' => 'balcao',
                'status' => 'finalizado',
                'subtotal' => 120.50,
                'discount' => 5.00,
                'delivery_fee' => 0.00,
                'total' => 115.50,
                'payment_method' => 'pix',
                'delivery_date' => null,
                'delivery_time' => null,
                'notes' => 'Cliente pediu desconto',
                'delivery_address' => null,
            ],
            [
                'cash_register_id' => $cashRegisters->first()->id,
                'user_id' => $users->first()->id,
                'customer_id' => $customers->skip(2)->first()->id,
                'table_id' => null,
                'motoboy_id' => $motoboys->first()?->id,
                'code' => 'VEN-' . strtoupper(uniqid()),
                'type' => 'delivery',
                'status' => 'entregue',
                'subtotal' => 95.00,
                'discount' => 0.00,
                'delivery_fee' => 5.00,
                'total' => 100.00,
                'payment_method' => 'cartao_credito',
                'delivery_date' => now()->subDays(1)->format('Y-m-d'),
                'delivery_time' => '14:30:00',
                'notes' => 'Entrega urgente',
                'delivery_address' => 'Rua Augusta, 789, Centro, São Paulo - SP',
            ],
        ];

        foreach ($sales as $sale) {
            Sale::create($sale);
        }

        // Criar vendas pendentes para teste do POS e fechamento de caixa
        $pendingSales = [
            [
                'cash_register_id' => $cashRegisters->first()->id,
                'user_id' => $users->first()->id,
                'customer_id' => $customers->first()->id,
                'table_id' => $tables->first()?->id,
                'motoboy_id' => null,
                'code' => 'VEN-PEND-' . strtoupper(uniqid()),
                'type' => 'balcao',
                'status' => 'pendente',
                'subtotal' => 45.00,
                'discount' => 0.00,
                'delivery_fee' => 0.00,
                'total' => 45.00,
                'payment_method' => 'dinheiro',
                'delivery_date' => null,
                'delivery_time' => null,
                'notes' => 'Pedido pendente na mesa - cliente disse que volta logo',
                'delivery_address' => null,
            ],
            [
                'cash_register_id' => $cashRegisters->first()->id,
                'user_id' => $users->first()->id,
                'customer_id' => $customers->skip(1)->first()->id,
                'table_id' => $tables->skip(1)->first()?->id,
                'motoboy_id' => $motoboys->first()?->id,
                'code' => 'VEN-PEND-' . strtoupper(uniqid()),
                'type' => 'delivery',
                'status' => 'pendente',
                'subtotal' => 78.90,
                'discount' => 0.00,
                'delivery_fee' => 5.00,
                'total' => 83.90,
                'payment_method' => 'dinheiro',
                'delivery_date' => now()->format('Y-m-d'),
                'delivery_time' => '18:30:00',
                'notes' => 'Cliente pediu entrega urgente mas ainda não pagou',
                'delivery_address' => 'Rua da Mata, 456, Santana, São Paulo - SP',
            ],
            [
                'cash_register_id' => $cashRegisters->first()->id,
                'user_id' => $users->first()->id,
                'customer_id' => $customers->skip(2)->first()->id,
                'table_id' => $tables->skip(2)->first()?->id,
                'motoboy_id' => null,
                'code' => 'VEN-PEND-' . strtoupper(uniqid()),
                'type' => 'balcao',
                'status' => 'pendente',
                'subtotal' => 35.50,
                'discount' => 0.00,
                'delivery_fee' => 0.00,
                'total' => 35.50,
                'payment_method' => 'cartao_credito',
                'delivery_date' => null,
                'delivery_time' => null,
                'notes' => 'Cliente foi atender telefone, voltará em seguida',
                'delivery_address' => null,
            ],
        ];

        foreach ($pendingSales as $sale) {
            Sale::create($sale);
        }

        $this->command->info('Vendas criadas com sucesso incluindo vendas pendentes para teste do POS!');
    }
}
