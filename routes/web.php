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

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/gestor', [PageController::class, 'gestor'])->name('gestor.login');
Route::post('/gestor/login', [AuthController::class, 'webLogin'])->name('gestor.login.submit');

// Authentication required routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('gestor.dashboard');
    Route::post('/gestor/logout', [AuthController::class, 'webLogout'])->name('gestor.logout');

    // Admin pages routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

    // Expenses (Entradas/Saídas) routes
    Route::resource('expenses', ExpenseController::class)->except(['show']);

    // Menu management routes
    Route::get('/menus', [MenuController::class, 'manage'])->name('menus.manage');
    Route::get('/menus/{day}', [MenuController::class, 'manageDay'])->name('menus.day');
    Route::get('/api/menu-data/{day}', [MenuController::class, 'getMenuDataForDay'])->name('menus.data');
    Route::post('/menus/toggle', [MenuController::class, 'toggleForDay'])->name('menus.toggle');
});

// Authentication required routes
Route::middleware('auth')->group(function () {
    // Users Management (Somente Admin)
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserController::class);
    });
    // Toggle status without permission check (for testing)
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Profile Management (All authenticated users)
    Route::middleware('auth')->group(function () {
        Route::get('profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
        Route::put('profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    });

    // Permissions Management (Somente Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('permissions', PermissionController::class);
        Route::post('permissions/assign-to-role', [PermissionController::class, 'assignToRole'])->name('permissions.assign-to-role');
        Route::post('permissions/assign-role-to-user', [PermissionController::class, 'assignRoleToUser'])->name('permissions.assign-role-to-user');
        Route::get('permissions/users/api', [PermissionController::class, 'getUsersWithPermissions'])->name('permissions.users.api');
        Route::patch('permissions/users/{user}/role', [PermissionController::class, 'updateUserRole'])->name('permissions.users.update-role');

        // Granular user permissions management
        Route::get('users/{user}/permissions', [PermissionController::class, 'manageUserPermissions'])->name('users.permissions.manage');
        Route::post('users/{user}/permissions/assign', [PermissionController::class, 'assignPermissionsToUser'])->name('users.permissions.assign');
        Route::delete('users/{user}/permissions/remove', [PermissionController::class, 'removePermissionFromUser'])->name('users.permissions.remove');
        Route::get('permissions/users-complete', [PermissionController::class, 'getUsersPermissions'])->name('permissions.users.complete');
    });

    // Settings Management (Somente Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Categories Management
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::get('categories-api', [CategoryController::class, 'apiIndex'])->name('categories.api');
    });

    // Products Management
    Route::middleware('permission:products.view')->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('products/{product}/update-availability', [ProductController::class, 'updateAvailability'])->name('products.update-availability');
        Route::get('products-day/{dayOfWeek}', [ProductController::class, 'getAvailableForDay'])->name('products.day');
        Route::get('products-category/{category}', [ProductController::class, 'getByCategory'])->name('products.category');
    });

    // Sales Management
    Route::middleware('permission:sales.view')->group(function () {
        Route::resource('sales', SaleController::class);
        Route::get('/pos', [SaleController::class, 'pos'])->name('sales.pos');
        Route::get('sales/{sale}/pos-data', [SaleController::class, 'getPosData'])->name('sales.pos-data');
        Route::post('sales/{sale}/finalize', [SaleController::class, 'finalize'])->name('sales.finalize');
        Route::post('sales/{sale}/update-status', [SaleController::class, 'updateStatus'])->name('sales.update-status');
        Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('sales/{sale}/print-receipt', [SaleController::class, 'printReceipt'])->name('sales.print-receipt');
        Route::get('sales-statistics', [SaleController::class, 'statistics'])->name('sales.statistics');
    });

    // Tables Management
    Route::middleware('permission:tables.view')->group(function () {
        Route::resource('tables', TableController::class);
        Route::post('tables/{table}/toggle-status', [TableController::class, 'toggleStatus'])->name('tables.toggle-status');
        Route::post('tables/{table}/update-status', [TableController::class, 'updateStatus'])->name('tables.update-status');
        Route::get('tables-status', [TableController::class, 'getStatus'])->name('tables.status');
    });

    // Cash Register Management
    Route::middleware('permission:cash_registers.view')->group(function () {
        Route::resource('cash-registers', CashRegisterController::class);
        Route::get('cash-registers-close', [CashRegisterController::class, 'closeForm'])->name('cash-registers.close-form');
        Route::get('cash-registers/{cashRegister}/sales', [CashRegisterController::class, 'sales'])->name('cash-registers.sales');
        Route::post('cash-registers/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('cash-registers.close');
        Route::get('cash-registers-statistics', [CashRegisterController::class, 'statistics'])->name('cash-registers.statistics');
    });

    // Customers Management
    Route::middleware('permission:customers.view')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('customers-search', [CustomerController::class, 'search'])->name('customers.search');
    });

    // Motoboys Management
    Route::middleware('permission:motoboys.view')->group(function () {
        Route::resource('motoboys', MotoboyController::class);
        Route::post('motoboys/{motoboy}/toggle-status', [MotoboyController::class, 'toggleStatus'])->name('motoboys.toggle-status');
        Route::get('motoboys-active', [MotoboyController::class, 'getActive'])->name('motoboys.active');
    });

    // Expenses Management
    Route::middleware('permission:expenses.view')->group(function () {
        Route::resource('expenses', ExpenseController::class);
        Route::get('expenses-statistics', [ExpenseController::class, 'statistics'])->name('expenses.statistics');
    });

    // Menu Management
    Route::middleware('permission:menu.view')->group(function () {
        Route::resource('menu', MenuController::class);
        Route::post('menu/{menu}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('menu.toggle-availability');
        Route::get('menu-day/{dayOfWeek}', [MenuController::class, 'getForDay'])->name('menu.day');
        Route::get('menu-product/{product}', [MenuController::class, 'getProductDays'])->name('menu.product');
    });

    // Sale Items Management
    Route::middleware('permission:sale_items.view')->group(function () {
        Route::resource('sale-items', SaleItemController::class);
        Route::get('sale-items-sale/{sale}', [SaleItemController::class, 'getBySale'])->name('sale-items.sale');
    });

    // Reports Management (Protected by permissions)
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
        Route::get('reports/employees', [ReportController::class, 'employees'])->name('reports.employees');
        Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('reports/export-csv', [ReportController::class, 'exportCSV'])->name('reports.export-csv');
    });

    // Encomendas Management
    Route::middleware('auth')->group(function () {
        Route::get('encomendas', [EncomendasController::class, 'index'])->name('encomendas.index');
        Route::get('encomendas/create', [EncomendasController::class, 'create'])->name('encomendas.create');
        Route::post('encomendas', [EncomendasController::class, 'store'])->name('encomendas.store');
        Route::get('encomendas/{encomenda}', [EncomendasController::class, 'show'])->name('encomendas.show');
        Route::get('encomendas/{encomenda}/edit', [EncomendasController::class, 'edit'])->name('encomendas.edit');
        Route::put('encomendas/{encomenda}', [EncomendasController::class, 'update'])->name('encomendas.update');
        Route::delete('encomendas/{encomenda}', [EncomendasController::class, 'destroy'])->name('encomendas.destroy');
        Route::post('encomendas/{encomenda}/update-status', [EncomendasController::class, 'updateStatus'])->name('encomendas.update-status');
        Route::get('api/encomendas-stats', [EncomendasController::class, 'stats'])->name('encomendas.stats');
    });
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
