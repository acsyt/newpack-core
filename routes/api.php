<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProductClassController;
use App\Http\Controllers\ProductSubclassController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseLocationController;
use App\Http\Controllers\InventoryStockController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ZipCodeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SuburbController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MeasureUnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'NewPack API v1. By @Acsyt';
});

Route::as('api.')
->group(function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
        Route::post('/send-reset-link', [AuthController::class, 'forgotPassword'])->name('password.send_reset_link');

        Route::get('/validate-token', [AuthController::class, 'validateToken'])->name('password.validate_token');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

        Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail'])->name('auth.send_verification');

        Route::post('/verify-email-token', [AuthController::class, 'verifyEmailWithToken'])->name('auth.verify_email_token');
        Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/user', [AuthController::class, 'getUser'])->name('auth.user');
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        });
    });

    Route::middleware('auth:sanctum')->group(function (): void {

        Route::prefix('roles')->group(function () {
            Route::get("/", [RoleController::class, 'findAll'])->name('roles.findAll');
            Route::get("/{role}", [RoleController::class, 'findById'])->name('roles.findById');
            Route::post('/', [RoleController::class, 'createRole'])->name('roles.createRole');
            Route::put("/{role}", [RoleController::class, 'updateRole'])->name('roles.updateRole');
        });

        Route::get('/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'findAllUsers'])->name('user.findAll');
            Route::post('/', [UserController::class, 'createUser'])->name('user.create');
            Route::prefix('{user}')->group(function (): void {
                Route::get('/', [UserController::class, 'findOneUser'])->name('user.findOne');
                Route::patch('/', [UserController::class, 'updateUser'])->name('user.update');
                Route::delete('/', [UserController::class, 'deleteUser'])->name('user.delete');
            });
        });

        Route::prefix('roles')->group(function () {
            Route::get("/", [RoleController::class, 'findAllRoles'])->name('roles.findAll');
            Route::get("/{role}", [RoleController::class, 'findOneRole'])->name('roles.findOne');
            Route::post('/', [RoleController::class, 'createRole'])->name('roles.create');
            Route::patch("/{role}", [RoleController::class, 'updateRole'])->name('roles.update');
        });

        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'findAllCustomers'])->name('customers.findAll');
            Route::post('/', [CustomerController::class, 'createCustomer'])->name('customers.create');
            Route::prefix('{customer}')->group(function () {
                Route::get('/', [CustomerController::class, 'findOneCustomer'])->name('customers.findOne');
                Route::patch('/', [CustomerController::class, 'updateCustomer'])->name('customers.update');
                Route::delete('/', [CustomerController::class, 'deleteCustomer'])->name('customers.delete');
            });
        });

        Route::get('zip-codes/{zipCode}', [ZipCodeController::class, 'show'])->name('zip_codes.show');

        Route::get('states', [StateController::class, 'index'])->name('states.index');
        Route::get('cities', [CityController::class, 'index'])->name('cities.index');
        Route::get('countries', [CountryController::class, 'index'])->name('countries.index');
        Route::get('suburbs', [SuburbController::class, 'index'])->name('suburbs.index');
        Route::get('addresses/lookup/{zipCode}', [AddressController::class, 'lookup'])->name('addresses.lookup');

        Route::prefix('processes')->group(function () {
            Route::get('/', [ProcessController::class, 'findAllProcesses'])->name('processes.findAll');
            Route::post('/', [ProcessController::class, 'createProcess'])->name('processes.create');
            Route::prefix('{process}')->group(function () {
                Route::get('/', [ProcessController::class, 'findOneProcess'])->name('processes.findOne');
                // Route::patch('/', [ProcessController::class, 'updateProcess'])->name('processes.update');
                // Route::delete('/', [ProcessController::class, 'deleteProcess'])->name('processes.delete');
            });
        });

        Route::prefix('product-classes')->group(function () {
            Route::get('/', [ProductClassController::class, 'findAllProductClasses'])->name('product_classes.findAll');
            // Route::post('/', [ProductClassController::class, 'createProductClass'])->name('product_classes.create');
            Route::prefix('{productClass}')->group(function () {
                Route::get('/', [ProductClassController::class, 'findOneProductClass'])->name('product_classes.findOne');
                // Route::patch('/', [ProductClassController::class, 'updateProductClass'])->name('product_classes.update');
                // Route::delete('/', [ProductClassController::class, 'deleteProductClass'])->name('product_classes.delete');
            });
        });

        Route::prefix('product-subclasses')->group(function () {
            Route::get('/', [ProductSubclassController::class, 'findAllProductSubclasses'])->name('product_subclasses.findAll');
            // Route::post('/', [ProductSubclassController::class, 'createProductSubclass'])->name('product_subclasses.create');
            Route::prefix('{productSubclass}')->group(function () {
                Route::get('/', [ProductSubclassController::class, 'findOneProductSubclass'])->name('product_subclasses.findOne');
                // Route::patch('/', [ProductSubclassController::class, 'updateProductSubclass'])->name('product_subclasses.update');
                // Route::delete('/', [ProductSubclassController::class, 'deleteProductSubclass'])->name('product_subclasses.delete');
            });
        });

        Route::prefix('machines')->group(function () {
            Route::get('/', [MachineController::class, 'findAllMachines'])->name('machines.findAll');
            Route::post('/', [MachineController::class, 'createMachine'])->name('machines.create');
            Route::prefix('{machine}')->group(function () {
                Route::get('/', [MachineController::class, 'findOneMachine'])->name('machines.findOne');
                Route::patch('/', [MachineController::class, 'updateMachine'])->name('machines.update');
                Route::delete('/', [MachineController::class, 'deleteMachine'])->name('machines.delete');
            });
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'findAllProducts'])->name('products.findAll');
            Route::post('/', [ProductController::class, 'createProduct'])->name('products.create');
            Route::prefix('{product}')->group(function () {
                Route::get('/', [ProductController::class, 'findOneProduct'])->name('products.findOne');
                Route::patch('/', [ProductController::class, 'updateProduct'])->name('products.update');
                Route::delete('/', [ProductController::class, 'deleteProduct'])->name('products.delete');
            });
        });

        Route::prefix('suppliers')->group(function () {
            Route::get('/', [SupplierController::class, 'findAllSuppliers'])->name('suppliers.findAll');
            Route::post('/', [SupplierController::class, 'createSupplier'])->name('suppliers.create');
            Route::prefix('{supplier}')->group(function () {
                Route::get('/', [SupplierController::class, 'findOneSupplier'])->name('suppliers.findOne');
                Route::patch('/', [SupplierController::class, 'updateSupplier'])->name('suppliers.update');
                Route::delete('/', [SupplierController::class, 'deleteSupplier'])->name('suppliers.delete');
            });
        });

        Route::prefix('settings')->group(function (){
            Route::get("/", [SettingController::class, 'getSettings'])->name('settings.get_settings');
            Route::put("/", [SettingController::class, 'updateSettings'])->name('settings.update_settings');
        });

        Route::prefix('warehouses')->group(function () {
            Route::get('/', [WarehouseController::class, 'findAllWarehouses'])->name('warehouses.findAll');
            Route::post('/', [WarehouseController::class, 'createWarehouse'])->name('warehouses.create');
            Route::prefix('{warehouse}')->group(function () {
                Route::get('/', [WarehouseController::class, 'findOneWarehouse'])->name('warehouses.findOne');
                Route::patch('/', [WarehouseController::class, 'updateWarehouse'])->name('warehouses.update');
                Route::delete('/', [WarehouseController::class, 'deleteWarehouse'])->name('warehouses.delete');
            });
        });

        Route::prefix('warehouse-locations')->group(function () {
            Route::get('/', [WarehouseLocationController::class, 'findAllLocations'])->name('warehouse_locations.findAll');
            Route::post('/', [WarehouseLocationController::class, 'createLocation'])->name('warehouse_locations.create');
            Route::prefix('{location}')->group(function () {
                Route::get('/', [WarehouseLocationController::class, 'findOneLocation'])->name('warehouse_locations.findOne');
                Route::patch('/', [WarehouseLocationController::class, 'updateLocation'])->name('warehouse_locations.update');
                Route::delete('/', [WarehouseLocationController::class, 'deleteLocation'])->name('warehouse_locations.delete');
            });
        });

        Route::prefix('inventory')->group(function () {
            Route::prefix('stocks')->group(function () {
                Route::get('/', [InventoryStockController::class, 'findAllStocks'])->name('inventory_stocks.findAll');
                Route::prefix('{stock}')->group(function () {
                    Route::get('/', [InventoryStockController::class, 'findOneStock'])->name('inventory_stocks.findOne');
                });
            });

            Route::prefix('movements')->group(function () {
                Route::get('/', [InventoryMovementController::class, 'findAllMovements'])->name('inventory_movements.findAll');
                // Route::post('/', [InventoryMovementController::class, 'store'])->name('inventory_movements.store'); ???
                Route::post('/transfer', [InventoryMovementController::class, 'transfer'])->name('inventory_movements.transfer');
                Route::prefix('{movement}')->group(function () {
                    Route::get('/', [InventoryMovementController::class, 'findOneMovement'])->name('inventory_movements.findOne');
                });
            });

            Route::prefix('transfers')->group(function () {
                Route::get('/', [TransferController::class, 'index'])->name('inventory_transfers.index');
                Route::prefix('{id}')->group(function () {
                    Route::get('/', [TransferController::class, 'show'])->name('inventory_transfers.show');
                    Route::post('/receive', [TransferController::class, 'receive'])->name('inventory_transfers.receive');
                });
            });
        });

        Route::prefix('product-types')->group(function () {
            Route::get('/', [ProductTypeController::class, 'findAllProductTypes'])->name('product_types.findAll');
        });

        Route::prefix('currencies')->group(function () {
            Route::get('/', [CurrencyController::class, 'findAllCurrencies'])->name('currencies.findAll');
        });

        Route::get('measure-units', [MeasureUnitController::class, 'findAllMeasureUnits'])->name('measure_units.findAll');
    });
});
