<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UnitsController;

use App\Exports\UnitsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\CurrenciesController;

use App\Http\Controllers\YearsController;
use App\Http\Controllers\ProductCountriesController;

use App\Http\Controllers\BrandsController;
use App\Http\Controllers\MonthsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\StockTypesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;



use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UserAssignRoleController;
use App\Http\Controllers\ManagePermissionController;

use App\Http\Middleware\CheckPermissions;

use  App\Http\Controllers\ItemsController;

use App\Http\Controllers\GodwanShopController;
use App\Http\Controllers\GodwanShopAshokController;

use App\Http\Controllers\ShopGodwanController;


use App\Http\Controllers\shopServiceGodwanController;
use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\ProformaController;

use App\Http\Controllers\InvoiceController;

use App\Http\Controllers\ExistenceController;


use App\Http\Middleware\AutoLogout;

use App\Http\Controllers\UploaderController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogsController;

// Public route: accessible by everyone
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes (Login, Register, etc.)
Auth::routes();

// Secured routes: only accessible if authenticated
Route::middleware(['auth',\App\Http\Middleware\UserActionLogger::class])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::prefix('export')->as('settings.')->group(function () {
        // Export routes
        Route::get('/units', [App\Http\Controllers\UnitsController::class, 'export'])->name('units.export');
        Route::get('/years', [App\Http\Controllers\YearsController::class, 'export'])->name('years.export');
        Route::get('/type', [App\Http\Controllers\StockTypesController::class, 'export'])->name('type.export');
        Route::get('/brands', [App\Http\Controllers\BrandsController::class, 'export'])->name('brands.export');
        Route::get('/categories', [App\Http\Controllers\CategoriesController::class, 'export'])->name('categories.export');
        Route::get('/countries', [App\Http\Controllers\ProductCountriesController::class, 'export'])->name('countries.export');
        Route::get('/currencies', [App\Http\Controllers\CurrenciesController::class, 'export'])->name('currencies.export');
        Route::get('/customers', [App\Http\Controllers\CustomersController::class, 'export'])->name('customers.export');
        Route::get('/months', [App\Http\Controllers\MonthsController::class, 'export'])->name('months.export');
        Route::get('/roles', [App\Http\Controllers\RoleController::class, 'export'])->name('roles.export');
        Route::get('/suppliers', [App\Http\Controllers\SuppliersController::class, 'export'])->name('suppliers.export');
        Route::get('/assignedRoles', [App\Http\Controllers\UserAssignRoleController::class, 'export'])->name('assignedRoles.export');
        Route::get('/users', [App\Http\Controllers\UsersController::class, 'export'])->name('users.export');

        Route::get('/items', [App\Http\Controllers\ItemsController::class, 'export'])->name('items.export');
        Route::get('/adjustments', [App\Http\Controllers\AdjustmentController::class, 'export'])->name('adjustments.export');
        Route::get('/adjustments/exportDetails/{id}', [App\Http\Controllers\AdjustmentController::class, 'exportDetails'])->name('adjustments.exportDetails');

        Route::get('/cash', [App\Http\Controllers\CashController::class, 'export'])->name('cash.export');
        Route::get('/cash/exportDetails/{id}', [App\Http\Controllers\CashController::class, 'exportDetails'])->name('cash.exportDetails');


        //credit
        Route::get('/credit', [App\Http\Controllers\CreditController::class, 'export'])->name('credit.export');
        Route::get('/credit/exportDetails/{id}', [App\Http\Controllers\CreditController::class, 'exportDetails'])->name('credit.exportDetails');

        Route::get('/proforma', [App\Http\Controllers\ProformaController::class, 'export'])->name('proforma.export');
        Route::get('/proforma/exportDetails/{id}', [App\Http\Controllers\ProformaController::class, 'exportDetails'])->name('proforma.exportDetails');




        Route::get('/godownshop', [App\Http\Controllers\GodwanShopController::class, 'export'])->name('proforma.export');
        Route::get('/godownshop/exportDetails/{id}', [App\Http\Controllers\GodwanShopController::class, 'exportDetails'])->name('proforma.exportDetails');


        Route::get('/godownShopAshok', [App\Http\Controllers\GodwanShopAshokController::class, 'export'])->name('godownShopAshok.export');
        Route::get('/godownShopAshok/exportDetails/{id}', [App\Http\Controllers\GodwanShopAshokController::class, 'exportDetails'])->name('godownShopAshok.exportDetails');


        Route::get('/shopGodown', [App\Http\Controllers\ShopGodwanController::class, 'export'])->name('shopGodown.export');
        Route::get('/shopGodown/exportDetails/{id}', [App\Http\Controllers\ShopGodwanController::class, 'exportDetails'])->name('shopGodown.exportDetails');


        Route::get('/services', [App\Http\Controllers\shopServiceGodwanController::class, 'export'])->name('services.export');
        Route::get('/services/exportDetails/{id}', [App\Http\Controllers\shopServiceGodwanController::class, 'exportDetails'])->name('services.exportDetails');


        Route::get('/existingTranfers', [App\Http\Controllers\ExistenceController::class, 'export'])->name('existingTranfers.export');
        Route::get('/existingTranfers/exportDetails/{id}', [App\Http\Controllers\ExistenceController::class, 'exportDetails'])->name('existingTranfers.exportDetails');


    });

    Route::get('/cash/{id}/pdf/{header}', [CashController::class, 'generatePdf']);
    Route::get('/credit/{id}/pdf/{header}', [CreditController::class, 'generatePdf']);
    Route::get('/proforma/{id}/pdf/{header}', [ProformaController::class, 'generatePdf']);

    Route::get('/godownshop/{id}/pdf/{header}', [GodwanShopController::class, 'generatePdf']);

    Route::get('/godownShopAshok/{id}/pdf/{header}', [GodwanShopAshokController::class, 'generatePdf']);


    Route::get('/shopGodown/{id}/pdf/{header}', [ShopGodwanController::class, 'generatePdf']);

    Route::get('/services/{id}/pdf/{header}', [shopServiceGodwanController::class, 'generatePdf']);


    Route::get('/existingTranfers/{id}/pdf/{header}', [ExistenceController::class, 'generatePdf']);





    Route::middleware([CheckPermissions::class . ':units'])->resource('units', UnitsController::class);
    Route::resource('currencies', CurrenciesController::class);
    Route::resource('years', YearsController::class);
    Route::resource('countries', ProductCountriesController::class);
    Route::resource('brands', BrandsController::class);
    Route::resource('months', MonthsController::class);
    Route::resource('customers', CustomersController::class);
    Route::resource('categories', CategoriesController::class);
    Route::resource('type', StockTypesController::class);
    Route::resource('users', UsersController::class);
    Route::resource('roles', RoleController::class);

    Route::resource('logs', LogsController::class);
    Route::post('/logs/revert/{id}', [LogsController::class, 'revert'])->name('logs.revert');

    Route::resource('suppliers', SuppliersController::class);
    Route::resource('assignedRoles', UserAssignRoleController::class);

    Route::resource('managePermissions', ManagePermissionController::class);
    Route::post('managePermissions/save', [ManagePermissionController::class, 'savePermissions'])->name('managePermissions.save');

    Route::resource('items', ItemsController::class);


    //transfert in progress
    Route::post('/upload', [UploaderController::class, 'upload'])->name('upload');


    //adjustment
    Route::resource('adjustments', AdjustmentController::class);
    Route::get('adjustments/{id}/details', [AdjustmentController::class, 'details'])->name('adjustments.details');
    Route::get('adjustments/{id}/edit', [AdjustmentController::class, 'edit'])->name('adjustments.edit');


    //sales
    Route::resource('cash', CashController::class);
    Route::get('cash/{id}/details', [CashController::class, 'details'])->name('cash.details');
    Route::get('cash/{id}/edit', [CashController::class, 'edit'])->name('cash.edit');
    //credit

    Route::resource('credit', CreditController::class);
    Route::get('credit/{id}/details', [CreditController::class, 'details'])->name('credit.details');
    Route::get('credit/{id}/edit', [CreditController::class, 'edit'])->name('credit.edit');

    //proforma
    Route::resource('proforma', ProformaController::class);
    Route::get('proforma/{id}/details', [ProformaController::class, 'details'])->name('proforma.details');
    Route::get('proforma/{id}/edit', [ProformaController::class, 'edit'])->name('proforma.edit');


    //godown to shop
    Route::resource('godownshop', GodwanShopController::class);
    Route::get('godownshop/{id}/details', [GodwanShopController::class, 'details'])->name('godownshop.details');
    Route::get('godownshop/{id}/edit', [GodwanShopController::class, 'edit'])->name('godownshop.edit');
    Route::put('godownshop/{id}/approve', [GodwanShopController::class, 'approve'])->name('godownshop.approve');

    //godown to shop ashok


    Route::resource('godownShopAshok', GodwanShopAshokController::class);
    Route::get('godownShopAshok/{id}/details', [GodwanShopAshokController::class, 'details'])->name('godownShopAshok.details');
    Route::get('godownShopAshok/{id}/edit', [GodwanShopAshokController::class, 'edit'])->name('godownShopAshok.edit');
    Route::get('godownShopAshok/{id}/edit', [GodwanShopAshokController::class, 'edit'])->name('godownShopAshok.edit');


    //shop to godown
    Route::resource('shopGodown', ShopGodwanController::class);
    Route::get('shopGodown/{id}/details', [ShopGodwanController::class, 'details'])->name('shopGodown.details');
    Route::get('shopGodown/{id}/edit', [ShopGodwanController::class, 'edit'])->name('shopGodown.edit');
    Route::get('shopGodown/{id}/approve', [ShopGodwanController::class, 'approve'])->name('shopGodown.approve');



    //shop service to godwiw
    Route::resource('services', shopServiceGodwanController::class);
    Route::get('services/{id}/details', [shopServiceGodwanController::class, 'details'])->name('services.details');
    Route::get('services/{id}/edit', [shopServiceGodwanController::class, 'edit'])->name('services.edit');

    //existence

    Route::resource('existingTranfers', ExistenceController::class);
    Route::get('existingTranfers/{id}/details', [ExistenceController::class, 'details'])->name('existingTranfers.details');
    Route::get('existingTranfers/{id}/edit', [ExistenceController::class, 'edit'])->name('existingTranfers.edit');


    Route::resource('invoice', InvoiceController::class);



    Route::resource('dashboard', DashboardController::class);
});
