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



// Public route: accessible by everyone
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes (Login, Register, etc.)
Auth::routes();

// Secured routes: only accessible if authenticated
Route::middleware(['auth'])->group(function () {
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


    });
    Route::resource('units', UnitsController::class);
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

    Route::resource('suppliers', SuppliersController::class);
    Route::resource('assignedRoles', UserAssignRoleController::class);




});
