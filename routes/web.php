<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MotoboyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EncomendasController;
use App\Http\Controllers\SettingController;

// ========================================
// PÚBLICO - Páginas públicas
// ========================================
Route::get('/', [PageController::class, 'index'])->name('home');

// ========================================
// AUTENTICAÇÃO - Login/Logout do Sistema
// ========================================
Route::get('/gestor', [PageController::class, 'gestor'])->name('gestor.login');
Route::post('/gestor/login', [AuthController::class, 'webLogin'])->name('gestor.login.submit');

// ========================================
// GESTOR - Área Administrativa (URLs em português)
// ========================================
Route::prefix('gestor')->middleware('auth')->group(function () {
    // DASHBOARD
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('gestor.dashboard');
    Route::post('/logout', [AuthController::class, 'webLogout'])->name('gestor.logout');

    // ========================================
    // USUÁRIOS E PERMISSÕES (Somente Admin)
    // ========================================
    Route::middleware('role:admin')->group(function () {
        // Usuários
        Route::resource('/usuarios', UserController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy'
        ]);
        Route::post('/usuarios/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Permissões
        Route::resource('/permissoes', PermissionController::class)->names([
            'index' => 'permissions.index',
            'create' => 'permissions.create',
            'store' => 'permissions.store',
            'show' => 'permissions.show',
            'edit' => 'permissions.edit',
            'update' => 'permissions.update',
            'destroy' => 'permissions.destroy'
        ]);
        Route::post('/permissoes/assign-to-role', [PermissionController::class, 'assignToRole'])->name('permissions.assign-to-role');
        Route::post('/permissoes/assign-role-to-user', [PermissionController::class, 'assignRoleToUser'])->name('permissions.assign-role-to-user');
        Route::get('/permissoes/usuarios/api', [PermissionController::class, 'getUsersWithPermissions'])->name('permissions.users.api');
        Route::patch('/permissoes/usuarios/{user}/role', [PermissionController::class, 'updateUserRole'])->name('permissions.users.update-role');
        Route::get('/permissoes/usuarios-completos', [PermissionController::class, 'getUsersPermissions'])->name('permissions.users.complete');

        // Permissões granulares por usuário
        Route::get('/usuarios/{user}/permissoes', [PermissionController::class, 'manageUserPermissions'])->name('users.permissions.manage');
        Route::post('/usuarios/{user}/permissoes/assign', [PermissionController::class, 'assignPermissionsToUser'])->name('users.permissions.assign');
        Route::delete('/usuarios/{user}/permissoes/remover', [PermissionController::class, 'removePermissionFromUser'])->name('users.permissions.remove');

        // Configurações
        Route::get('/configuracoes', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/configuracoes', [SettingController::class, 'update'])->name('settings.update');
    });

    // ========================================
    // PERFIL (Todos os usuários autenticados)
    // ========================================
    Route::middleware('auth')->group(function () {
        Route::get('/perfil/editar', [UserController::class, 'editProfile'])->name('profile.edit');
        Route::put('/perfil/atualizar', [UserController::class, 'updateProfile'])->name('profile.update');
    });

    // ========================================
    // PRODUTOS (Com permissão)
    // ========================================
    Route::middleware('permission:products.view')->group(function () {
        Route::resource('/produtos', ProductController::class)->names([
            'index' => 'products.index',
            'create' => 'products.create',
            'store' => 'products.store',
            'show' => 'products.show',
            'edit' => 'products.edit',
            'update' => 'products.update',
            'destroy' => 'products.destroy'
        ]);
        Route::post('/produtos/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('/produtos/{product}/update-availability', [ProductController::class, 'updateAvailability'])->name('products.update-availability');
        Route::get('/produtos-dia/{dayOfWeek}', [ProductController::class, 'getAvailableForDay'])->name('products.day');
        Route::get('/produtos-categoria/{category}', [ProductController::class, 'getByCategory'])->name('products.category');
    });

    // ========================================
    // CATEGORIAS (Com permissão)
    // ========================================
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('/categorias', CategoryController::class)->names([
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'show' => 'categories.show',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy'
        ]);
        Route::post('/categorias/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::get('/categorias-api', [CategoryController::class, 'apiIndex'])->name('categories.api');
    });

    // ========================================
    // VENDAS E PDV (Com permissão)
    // ========================================
    Route::middleware('permission:sales.view')->group(function () {
        Route::resource('/vendas', SaleController::class)->names([
            'index' => 'sales.index',
            'create' => 'sales.create',
            'store' => 'sales.store',
            'show' => 'sales.show',
            'edit' => 'sales.edit',
            'update' => 'sales.update',
            'destroy' => 'sales.destroy'
        ]);
        Route::get('/pos', [SaleController::class, 'pos'])->name('sales.pos');
        Route::get('/vendas/{sale}/pos-data', [SaleController::class, 'getPosData'])->name('sales.pos-data');
        Route::post('/vendas/{sale}/finalizar', [SaleController::class, 'finalize'])->name('sales.finalize');
        Route::post('/vendas/{sale}/atualizar-status', [SaleController::class, 'updateStatus'])->name('sales.update-status');
        Route::post('/vendas/{sale}/cancelar', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('/vendas/{sale}/imprimir-recibo', [SaleController::class, 'printReceipt'])->name('sales.print-receipt');
        Route::get('/vendas-estatisticas', [SaleController::class, 'statistics'])->name('sales.statistics');
    });

    // ========================================
    // MESAS (Com permissão)
    // ========================================
    Route::middleware('permission:tables.view')->group(function () {
        Route::resource('/mesas', TableController::class)->names([
            'index' => 'tables.index',
            'create' => 'tables.create',
            'store' => 'tables.store',
            'show' => 'tables.show',
            'edit' => 'tables.edit',
            'update' => 'tables.update',
            'destroy' => 'tables.destroy'
        ]);
        Route::post('/mesas/{table}/toggle-status', [TableController::class, 'toggleStatus'])->name('tables.toggle-status');
        Route::post('/mesas/{table}/atualizar-status', [TableController::class, 'updateStatus'])->name('tables.update-status');
        Route::get('/mesas-status', [TableController::class, 'getStatus'])->name('tables.status');
    });

    // ========================================
    // CLIENTES (Com permissão)
    // ========================================
    Route::middleware('permission:customers.view')->group(function () {
        Route::resource('/clientes', CustomerController::class)->names([
            'index' => 'customers.index',
            'create' => 'customers.create',
            'store' => 'customers.store',
            'show' => 'customers.show',
            'edit' => 'customers.edit',
            'update' => 'customers.update',
            'destroy' => 'customers.destroy'
        ]);
        Route::get('/clientes-pesquisa', [CustomerController::class, 'search'])->name('customers.search');
    });

    // ========================================
    // MOTOBOYS (Com permissão)
    // ========================================
    Route::middleware('permission:motoboys.view')->group(function () {
        Route::resource('/motoboys', MotoboyController::class);
        Route::post('/motoboys/{motoboy}/toggle-status', [MotoboyController::class, 'toggleStatus'])->name('motoboys.toggle-status');
        Route::get('/motoboys-ativos', [MotoboyController::class, 'getActive'])->name('motoboys.active');
    });

    // ========================================
    // CAIXA (Com permissão)
    // ========================================
    Route::middleware('permission:cash_registers.view')->group(function () {
        Route::resource('/caixa', CashRegisterController::class)->names([
            'index' => 'cash-registers.index',
            'create' => 'cash-registers.create',
            'store' => 'cash-registers.store',
            'show' => 'cash-registers.show',
            'edit' => 'cash-registers.edit',
            'update' => 'cash-registers.update',
            'destroy' => 'cash-registers.destroy'
        ]);
        Route::get('/caixa/fechar', [CashRegisterController::class, 'closeForm'])->name('cash-registers.close-form');
        Route::get('/caixa/{cashRegister}/vendas', [CashRegisterController::class, 'sales'])->name('cash-registers.sales');
        Route::post('/caixa/{cashRegister}/fechar', [CashRegisterController::class, 'close'])->name('cash-registers.close');
        Route::get('/caixa-estatisticas', [CashRegisterController::class, 'statistics'])->name('cash-registers.statistics');
    });

    // ========================================
    // ENTRADAS/SAÍDAS (Com permissão)
    // ========================================
    Route::middleware('permission:expenses.view')->group(function () {
        Route::resource('/entradas-saidas', ExpenseController::class)->names([
            'index' => 'expenses.index',
            'create' => 'expenses.create',
            'store' => 'expenses.store',
            'show' => 'expenses.show',
            'edit' => 'expenses.edit',
            'update' => 'expenses.update',
            'destroy' => 'expenses.destroy'
        ])->except(['show']);
        Route::get('/entradas-saidas-estatisticas', [ExpenseController::class, 'statistics'])->name('expenses.statistics');
    });

    // ========================================
    // CARDÁPIO (Menu) (Com permissão)
    // ========================================
    Route::middleware('permission:menu.view')->group(function () {
        Route::resource('/cardapio', MenuController::class)->names([
            'index' => 'menu.index',
            'create' => 'menu.create',
            'store' => 'menu.store',
            'show' => 'menu.show',
            'edit' => 'menu.edit',
            'update' => 'menu.update',
            'destroy' => 'menu.destroy'
        ]);
        Route::post('/cardapio/{menu}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('menu.toggle-availability');
        Route::get('/cardapio-dia/{dayOfWeek}', [MenuController::class, 'getForDay'])->name('menu.day');
        Route::get('/cardapio-produto/{product}', [MenuController::class, 'getProductDays'])->name('menu.product');

        // Rotas legacy do menu
        Route::get('/menus', [MenuController::class, 'manage'])->name('menus.manage');
        Route::get('/menus/{day}', [MenuController::class, 'manageDay'])->name('menus.day');
        Route::get('/api/menu-data/{day}', [MenuController::class, 'getMenuDataForDay'])->name('menus.data');
        Route::post('/menus/toggle', [MenuController::class, 'toggleForDay'])->name('menus.toggle');
    });

    // ========================================
    // RELATÓRIOS (Com permissão)
    // ========================================
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/relatorios/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('/relatorios/produtos', [ReportController::class, 'products'])->name('reports.products');
        Route::get('/relatorios/fluxo-caixa', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
        Route::get('/relatorios/funcionarios', [ReportController::class, 'employees'])->name('reports.employees');
        Route::get('/relatorios/clientes', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('/relatorios/exportar-csv', [ReportController::class, 'exportCSV'])->name('reports.export-csv');
    });

    // ========================================
    // ENCOMENDAS (Todos autenticados)
    // ========================================
    Route::middleware('auth')->group(function () {
        Route::get('/encomendas', [EncomendasController::class, 'index'])->name('encomendas.index');
        Route::get('/encomendas/criar', [EncomendasController::class, 'create'])->name('encomendas.create');
        Route::post('/encomendas', [EncomendasController::class, 'store'])->name('encomendas.store');
        Route::get('/encomendas/{encomenda}', [EncomendasController::class, 'show'])->name('encomendas.show');
        Route::get('/encomendas/{encomenda}/editar', [EncomendasController::class, 'edit'])->name('encomendas.edit');
        Route::put('/encomendas/{encomenda}', [EncomendasController::class, 'update'])->name('encomendas.update');
        Route::delete('/encomendas/{encomenda}', [EncomendasController::class, 'destroy'])->name('encomendas.destroy');
        Route::post('/encomendas/{encomenda}/atualizar-status', [EncomendasController::class, 'updateStatus'])->name('encomendas.update-status');
        Route::get('/api/encomendas-stats', [EncomendasController::class, 'stats'])->name('encomendas.stats');
    });

    // Menu data para interface web
    Route::get('menu-data/{day}', [MenuController::class, 'getMenuDataForDay'])->name('api.menu-data');
});

// API Authentication Routes (using custom token system)
Route::prefix('api')->group(function () {
    // Public authentication routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']); // Optional

    // Protected authentication routes (require valid token)
    Route::middleware('auth.api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user'])->name('api.user');
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});

// API Resource Routes (all require authentication and permissions)
Route::middleware('auth.api')->prefix('api')->group(function () {
    // Categories API
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'show']);
        Route::get('categories-api', [CategoryController::class, 'apiIndex']);
    });

    // Products API
    Route::middleware('permission:products.view')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        Route::get('products-day/{dayOfWeek}', [ProductController::class, 'getAvailableForDay']);
        Route::get('products-category/{category}', [ProductController::class, 'getByCategory']);
    });

    // Sales API
    Route::middleware('permission:sales.view')->group(function () {
        Route::resource('sales', SaleController::class)->only(['index', 'show', 'store', 'update']);
        Route::post('sales/{sale}/update-status', [SaleController::class, 'updateStatus']);
        Route::post('sales/{sale}/finalize', [SaleController::class, 'finalize']);
        Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel']);
        Route::post('sales/{sale}/print-receipt', [SaleController::class, 'printReceipt']);
        Route::get('sales-statistics', [SaleController::class, 'statistics']);
    });

    // Tables API
    Route::middleware('permission:tables.view')->group(function () {
        Route::resource('tables', TableController::class)->only(['index', 'show', 'update']);
        Route::post('tables/{table}/update-status', [TableController::class, 'updateStatus']);
        Route::get('tables-status', [TableController::class, 'getStatus']);
    });

    // Customers API
    Route::middleware('permission:customers.view')->group(function () {
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'store', 'update']);
        Route::get('customers-search', [CustomerController::class, 'search']);
    });

    // Motoboys API
    Route::middleware('permission:motoboys.view')->group(function () {
        Route::resource('motoboys', MotoboyController::class)->only(['index', 'show']);
        Route::get('motoboys-active', [MotoboyController::class, 'getActive']);
    });

    // Expenses API
    Route::middleware('permission:expenses.view')->group(function () {
        Route::resource('expenses', ExpenseController::class)->only(['index', 'show', 'store', 'update']);
        Route::get('expenses-statistics', [ExpenseController::class, 'statistics']);
    });

    // Menu API
    Route::middleware('permission:menu.view')->group(function () {
        Route::resource('menu', MenuController::class)->only(['index', 'show']);
        Route::get('menu-day/{dayOfWeek}', [MenuController::class, 'getForDay']);
        Route::get('menu-product/{product}', [MenuController::class, 'getProductDays']);
        Route::get('menu-data/{day}', [MenuController::class, 'getMenuDataForDay']);
    });

    // Cash Registers API
    Route::middleware('permission:cash_registers.view')->group(function () {
        Route::resource('cash-registers', CashRegisterController::class)->only(['index', 'show', 'store', 'update']);
        Route::post('cash-registers/{cashRegister}/close', [CashRegisterController::class, 'close']);
        Route::get('cash-registers-statistics', [CashRegisterController::class, 'statistics']);
    });

    // Sale Items API
    Route::middleware('permission:sale_items.view')->group(function () {
        Route::resource('sale-items', SaleItemController::class)->only(['index', 'show']);
        Route::get('sale-items-sale/{sale}', [SaleItemController::class, 'getBySale']);
    });

    // Permissions Management API (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('permissions/assign-to-role', [PermissionController::class, 'assignToRole']);
        Route::post('permissions/assign-role-to-user', [PermissionController::class, 'assignRoleToUser']);
        Route::get('permissions/users/api', [PermissionController::class, 'getUsersWithPermissions']);
        Route::patch('permissions/users/{user}/role', [PermissionController::class, 'updateUserRole']);
    });

    // Users API (Admin only for full management)
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    });
});
