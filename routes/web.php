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

// Public route: accessible by everyone
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes (Login, Register, etc.)
Auth::routes();

// Secured routes: only accessible if authenticated
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


    Route::get('/customers', [App\Http\Controllers\CustomersController::class, 'index'])->name('customers');
    Route::get('/item-categories', [App\Http\Controllers\ItemCategoriesController::class, 'index'])->name('item-categories');
    Route::get('/months', [App\Http\Controllers\MonthsController::class, 'index'])->name('months');
    Route::get('/stock-types', [App\Http\Controllers\StockTypesController::class, 'index'])->name('stock-types');
    Route::get('/suppliers', [App\Http\Controllers\SuppliersController::class, 'index'])->name('suppliers');

    Route::get('/export/units', [UnitsController::class, 'export'])->name('settings.units.export');
    Route::resource('units', UnitsController::class);

    Route::resource('currencies', CurrenciesController::class);
    Route::resource('years', YearsController::class);
    Route::resource('countries', ProductCountriesController::class);
    Route::resource('brands', BrandsController::class);


});
