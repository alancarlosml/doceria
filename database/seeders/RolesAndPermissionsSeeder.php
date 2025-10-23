<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Criando permissões do sistema...');

        // Permissões de Usuários
        $permissions = [
            // Módulo Usuários
            ['name' => 'users.view', 'label' => 'Visualizar Usuários', 'module' => 'users', 'action' => 'view'],
            ['name' => 'users.create', 'label' => 'Criar Usuários', 'module' => 'users', 'action' => 'create'],
            ['name' => 'users.edit', 'label' => 'Editar Usuários', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'users.delete', 'label' => 'Excluir Usuários', 'module' => 'users', 'action' => 'delete'],

            // Módulo Categorias
            ['name' => 'categories.view', 'label' => 'Visualizar Categorias', 'module' => 'categories', 'action' => 'view'],
            ['name' => 'categories.create', 'label' => 'Criar Categorias', 'module' => 'categories', 'action' => 'create'],
            ['name' => 'categories.edit', 'label' => 'Editar Categorias', 'module' => 'categories', 'action' => 'edit'],
            ['name' => 'categories.delete', 'label' => 'Excluir Categorias', 'module' => 'categories', 'action' => 'delete'],

            // Módulo Produtos
            ['name' => 'products.view', 'label' => 'Visualizar Produtos', 'module' => 'products', 'action' => 'view'],
            ['name' => 'products.create', 'label' => 'Criar Produtos', 'module' => 'products', 'action' => 'create'],
            ['name' => 'products.edit', 'label' => 'Editar Produtos', 'module' => 'products', 'action' => 'edit'],
            ['name' => 'products.delete', 'label' => 'Excluir Produtos', 'module' => 'products', 'action' => 'delete'],

            // Módulo Vendas
            ['name' => 'sales.view', 'label' => 'Visualizar Vendas', 'module' => 'sales', 'action' => 'view'],
            ['name' => 'sales.create', 'label' => 'Criar Vendas', 'module' => 'sales', 'action' => 'create'],
            ['name' => 'sales.edit', 'label' => 'Editar Vendas', 'module' => 'sales', 'action' => 'edit'],
            ['name' => 'sales.delete', 'label' => 'Excluir Vendas', 'module' => 'sales', 'action' => 'delete'],
            ['name' => 'sales.cancel', 'label' => 'Cancelar Vendas', 'module' => 'sales', 'action' => 'cancel'],
            ['name' => 'sales.update_status', 'label' => 'Alterar Status de Vendas', 'module' => 'sales', 'action' => 'update_status'],

            // Módulo Mesas
            ['name' => 'tables.view', 'label' => 'Visualizar Mesas', 'module' => 'tables', 'action' => 'view'],
            ['name' => 'tables.create', 'label' => 'Criar Mesas', 'module' => 'tables', 'action' => 'create'],
            ['name' => 'tables.edit', 'label' => 'Editar Mesas', 'module' => 'tables', 'action' => 'edit'],
            ['name' => 'tables.delete', 'label' => 'Excluir Mesas', 'module' => 'tables', 'action' => 'delete'],

            // Módulo Motoboys
            ['name' => 'motoboys.view', 'label' => 'Visualizar Motoboys', 'module' => 'motoboys', 'action' => 'view'],
            ['name' => 'motoboys.create', 'label' => 'Criar Motoboys', 'module' => 'motoboys', 'action' => 'create'],
            ['name' => 'motoboys.edit', 'label' => 'Editar Motoboys', 'module' => 'motoboys', 'action' => 'edit'],
            ['name' => 'motoboys.delete', 'label' => 'Excluir Motoboys', 'module' => 'motoboys', 'action' => 'delete'],

            // Módulo Clientes
            ['name' => 'customers.view', 'label' => 'Visualizar Clientes', 'module' => 'customers', 'action' => 'view'],
            ['name' => 'customers.create', 'label' => 'Criar Clientes', 'module' => 'customers', 'action' => 'create'],
            ['name' => 'customers.edit', 'label' => 'Editar Clientes', 'module' => 'customers', 'action' => 'edit'],
            ['name' => 'customers.delete', 'label' => 'Excluir Clientes', 'module' => 'customers', 'action' => 'delete'],

            // Módulo Despesas
            ['name' => 'expenses.view', 'label' => 'Visualizar Despesas', 'module' => 'expenses', 'action' => 'view'],
            ['name' => 'expenses.create', 'label' => 'Criar Despesas', 'module' => 'expenses', 'action' => 'create'],
            ['name' => 'expenses.edit', 'label' => 'Editar Despesas', 'module' => 'expenses', 'action' => 'edit'],
            ['name' => 'expenses.delete', 'label' => 'Excluir Despesas', 'module' => 'expenses', 'action' => 'delete'],

            // Módulo Cardápio
            ['name' => 'menu.view', 'label' => 'Visualizar Cardápio', 'module' => 'menu', 'action' => 'view'],
            ['name' => 'menu.create', 'label' => 'Gerenciar Cardápio', 'module' => 'menu', 'action' => 'create'],
            ['name' => 'menu.edit', 'label' => 'Editar Cardápio', 'module' => 'menu', 'action' => 'edit'],
            ['name' => 'menu.delete', 'label' => 'Excluir do Cardápio', 'module' => 'menu', 'action' => 'delete'],

            // Módulo Caixa
            ['name' => 'cash_registers.view', 'label' => 'Visualizar Caixas', 'module' => 'cash_registers', 'action' => 'view'],
            ['name' => 'cash_registers.create', 'label' => 'Abrir Caixa', 'module' => 'cash_registers', 'action' => 'create'],
            ['name' => 'cash_registers.edit', 'label' => 'Editar Caixa', 'module' => 'cash_registers', 'action' => 'edit'],
            ['name' => 'cash_registers.close', 'label' => 'Fechar Caixa', 'module' => 'cash_registers', 'action' => 'close'],

            // Módulo Itens de Venda
            ['name' => 'sale_items.view', 'label' => 'Visualizar Itens de Venda', 'module' => 'sale_items', 'action' => 'view'],
            ['name' => 'sale_items.create', 'label' => 'Criar Itens de Venda', 'module' => 'sale_items', 'action' => 'create'],
            ['name' => 'sale_items.edit', 'label' => 'Editar Itens de Venda', 'module' => 'sale_items', 'action' => 'edit'],
            ['name' => 'sale_items.delete', 'label' => 'Excluir Itens de Venda', 'module' => 'sale_items', 'action' => 'delete'],

            // Módulo Itens de Despesa
            ['name' => 'expense_categories.view', 'label' => 'Visualizar Categorias de Despesa', 'module' => 'expense_categories', 'action' => 'view'],
            ['name' => 'expense_categories.create', 'label' => 'Criar Categorias de Despesa', 'module' => 'expense_categories', 'action' => 'create'],
            ['name' => 'expense_categories.edit', 'label' => 'Editar Categorias de Despesa', 'module' => 'expense_categories', 'action' => 'edit'],
            ['name' => 'expense_categories.delete', 'label' => 'Excluir Categorias de Despesa', 'module' => 'expense_categories', 'action' => 'delete'],

            // Relatórios e Estatísticas
            ['name' => 'reports.view', 'label' => 'Visualizar Relatórios', 'module' => 'reports', 'action' => 'view'],
            ['name' => 'analytics.view', 'label' => 'Visualizar Estatísticas', 'module' => 'analytics', 'action' => 'view'],

            // Configurações
            ['name' => 'settings.view', 'label' => 'Visualizar Configurações', 'module' => 'settings', 'action' => 'view'],
            ['name' => 'settings.edit', 'label' => 'Editar Configurações', 'module' => 'settings', 'action' => 'edit'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::create($permissionData);
        }

        $this->command->info('✓ ' . count($permissions) . ' permissões criadas');

        // Criar Roles
        $this->command->info('Criando roles do sistema...');

        $roles = [
            [
                'name' => 'admin',
                'label' => 'Administrador',
                'description' => 'Acesso total a todas as funcionalidades do sistema',
                'all_permissions' => true,
            ],
            [
                'name' => 'gestor',
                'label' => 'Gerente',
                'description' => 'Gerenciamento completo do negócio exceto usuários',
                'permissions' => [
                    // Todos os módulos exceto users
                    'categories.*', 'products.*', 'sales.*', 'tables.*',
                    'motoboys.*', 'customers.*', 'expenses.*', 'menu.*',
                    'cash_registers.*', 'sale_items.*', 'reports.view', 'analytics.view',
                    'settings.view',
                ],
            ],
            [
                'name' => 'atendente',
                'label' => 'Atendente',
                'description' => 'Acesso básico para operações do dia a dia',
                'permissions' => [
                    'sales.create', 'sales.view', 'sales.edit', 'sales.cancel', 'sales.update_status',
                    'tables.view', 'tables.edit', 'customers.view', 'customers.create', 'customers.edit',
                    'motoboys.view', 'menu.view', 'sale_items.create', 'sale_items.edit', 'sale_items.delete',
                    'expenses.create', 'expenses.view', 'reports.view',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'label' => $roleData['label'],
                'description' => $roleData['description'],
            ]);

            if (isset($roleData['all_permissions']) && $roleData['all_permissions']) {
                // Admin tem todas as permissões
                $role->syncPermissions(Permission::all());
            } elseif (isset($roleData['permissions'])) {
                $permissionNames = [];
                foreach ($roleData['permissions'] as $pattern) {
                    if (str_contains($pattern, '*')) {
                        // Pattern like "sales.*" - get all permissions for a module
                        $module = str_replace('.*', '', $pattern);
                        $modulePermissions = Permission::byModule($module)->pluck('name')->toArray();
                        $permissionNames = array_merge($permissionNames, $modulePermissions);
                    } else {
                        $permissionNames[] = $pattern;
                    }
                }
                $role->syncPermissions(array_unique($permissionNames));
            }
        }

        $this->command->info('✓ ' . count($roles) . ' roles criadas com suas permissões');

        // Atribuir role admin ao primeiro usuário
        $user = \App\Models\User::first();
        if ($user) {
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
                $this->command->info('✓ Role admin atribuída ao usuário: ' . $user->name);
            }
        }

        $this->command->info('🎉 Sistema de permissões configurado com sucesso!');
    }
}
