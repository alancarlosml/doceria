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

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/gestor', [PageController::class, 'gestor'])->name('gestor.login');
Route::post('/gestor/login', [AuthController::class, 'webLogin'])->name('gestor.login.submit');

// Authentication required routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('gestor.dashboard');
    Route::post('/gestor/logout', [AuthController::class, 'webLogout'])->name('gestor.logout');

    // Admin pages routes
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');

    // Expenses (Entradas/Saídas) routes
    Route::resource('expenses', ExpenseController::class)->except(['show']);

    // Menu management routes
    Route::get('/menus', [\App\Http\Controllers\MenuController::class, 'manage'])->name('menus.manage');
    Route::get('/menus/{day}', [\App\Http\Controllers\MenuController::class, 'manageDay'])->name('menus.day');
    Route::get('/api/menu-data/{day}', [\App\Http\Controllers\MenuController::class, 'getMenuDataForDay'])->name('menus.data');
    Route::post('/menus/toggle', [\App\Http\Controllers\MenuController::class, 'toggleForDay'])->name('menus.toggle');
});

// Authentication required routes
Route::middleware('auth')->group(function () {
    // Users Management (Somente Admin)
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

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
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
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
});

// API Authentication Routes (using custom token system)
Route::prefix('api')->group(function () {
    // Public authentication routes
    Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\API\AuthController::class, 'register']); // Optional

    // Protected authentication routes (require valid token)
    Route::middleware('auth.api')->group(function () {
        Route::post('logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
        Route::get('user', [\App\Http\Controllers\API\AuthController::class, 'user'])->name('api.user');
        Route::post('refresh', [\App\Http\Controllers\API\AuthController::class, 'refresh']);
        Route::post('change-password', [\App\Http\Controllers\API\AuthController::class, 'changePassword']);
    });
});

// API Resource Routes (all require authentication and permissions)
Route::middleware('auth.api')->prefix('api')->group(function () {
    // Categories API
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', App\Http\Controllers\CategoryController::class)->only(['index', 'show']);
        Route::get('categories-api', [App\Http\Controllers\CategoryController::class, 'apiIndex']);
    });

    // Products API
    Route::middleware('permission:products.view')->group(function () {
        Route::resource('products', App\Http\Controllers\ProductController::class)->only(['index', 'show']);
        Route::get('products-day/{dayOfWeek}', [App\Http\Controllers\ProductController::class, 'getAvailableForDay']);
        Route::get('products-category/{category}', [App\Http\Controllers\ProductController::class, 'getByCategory']);
    });

    // Sales API
    Route::middleware('permission:sales.view')->group(function () {
        Route::resource('sales', App\Http\Controllers\SaleController::class)->only(['index', 'show', 'store', 'update']);
        Route::post('sales/{sale}/update-status', [App\Http\Controllers\SaleController::class, 'updateStatus']);
        Route::post('sales/{sale}/finalize', [SaleController::class, 'finalize']);
        Route::post('sales/{sale}/cancel', [App\Http\Controllers\SaleController::class, 'cancel']);
        Route::get('sales-statistics', [App\Http\Controllers\SaleController::class, 'statistics']);
    });

    // Tables API
    Route::middleware('permission:tables.view')->group(function () {
        Route::resource('tables', App\Http\Controllers\TableController::class)->only(['index', 'show', 'update']);
        Route::post('tables/{table}/update-status', [App\Http\Controllers\TableController::class, 'updateStatus']);
        Route::get('tables-status', [App\Http\Controllers\TableController::class, 'getStatus']);
    });

    // Customers API
    Route::middleware('permission:customers.view')->group(function () {
        Route::resource('customers', App\Http\Controllers\CustomerController::class)->only(['index', 'show', 'store', 'update']);
        Route::get('customers-search', [App\Http\Controllers\CustomerController::class, 'search']);
    });

    // Motoboys API
    Route::middleware('permission:motoboys.view')->group(function () {
        Route::resource('motoboys', App\Http\Controllers\MotoboyController::class)->only(['index', 'show']);
        Route::get('motoboys-active', [App\Http\Controllers\MotoboyController::class, 'getActive']);
    });

    // Expenses API
    Route::middleware('permission:expenses.view')->group(function () {
        Route::resource('expenses', App\Http\Controllers\ExpenseController::class)->only(['index', 'show', 'store', 'update']);
        Route::get('expenses-statistics', [App\Http\Controllers\ExpenseController::class, 'statistics']);
    });

    // Menu API
    Route::middleware('permission:menu.view')->group(function () {
        Route::resource('menu', App\Http\Controllers\MenuController::class)->only(['index', 'show']);
        Route::get('menu-day/{dayOfWeek}', [App\Http\Controllers\MenuController::class, 'getForDay']);
        Route::get('menu-product/{product}', [App\Http\Controllers\MenuController::class, 'getProductDays']);
    });

    // Cash Registers API
    Route::middleware('permission:cash_registers.view')->group(function () {
        Route::resource('cash-registers', App\Http\Controllers\CashRegisterController::class)->only(['index', 'show', 'store', 'update']);
        Route::post('cash-registers/{cashRegister}/close', [App\Http\Controllers\CashRegisterController::class, 'close']);
        Route::get('cash-registers-statistics', [App\Http\Controllers\CashRegisterController::class, 'statistics']);
    });

    // Sale Items API
    Route::middleware('permission:sale_items.view')->group(function () {
        Route::resource('sale-items', App\Http\Controllers\SaleItemController::class)->only(['index', 'show']);
        Route::get('sale-items-sale/{sale}', [App\Http\Controllers\SaleItemController::class, 'getBySale']);
    });

    // Permissions Management API (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('permissions', App\Http\Controllers\PermissionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('permissions/assign-to-role', [App\Http\Controllers\PermissionController::class, 'assignToRole']);
        Route::post('permissions/assign-role-to-user', [App\Http\Controllers\PermissionController::class, 'assignRoleToUser']);
        Route::get('permissions/users/api', [App\Http\Controllers\PermissionController::class, 'getUsersWithPermissions']);
        Route::patch('permissions/users/{user}/role', [App\Http\Controllers\PermissionController::class, 'updateUserRole']);
    });

    // Users API (Admin only for full management)
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::post('users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus']);
    });
});
