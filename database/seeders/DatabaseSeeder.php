<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌟 Iniciando seeders para sistema de doceria...');

        // Usuário padrão para desenvolvimento
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->command->info('✅ Usuário padrão criado');

        // Primeiro: Sistema de permissões (roles e permissões)
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Ordem importante: categorias antes de produtos
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            TableSeeder::class,
            MotoboySeeder::class,
            CashRegisterSeeder::class,
            ExpenseSeeder::class,
            MenuSeeder::class,
            SaleSeeder::class,
            SaleItemSeeder::class,
        ]);

        $this->command->info('🎉 Todos os seeders executados com sucesso!');
        $this->command->info('📊 Dados de exemplo criados para sistema de doceria');
    }
}
