<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UnitsController;

use App\Exports\UnitsExport;
use Maatwebsite\Excel\Facades\Excel;



// Public route: accessible by everyone
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes (Login, Register, etc.)
Auth::routes();

// Secured routes: only accessible if authenticated
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/brands', [App\Http\Controllers\BrandsController::class, 'index'])->name('brands');
    Route::get('/currencies', [App\Http\Controllers\CurrenciesController::class, 'index'])->name('currencies');
    Route::get('/customers', [App\Http\Controllers\CustomersController::class, 'index'])->name('customers');
    Route::get('/item-categories', [App\Http\Controllers\ItemCategoriesController::class, 'index'])->name('item-categories');
    Route::get('/months', [App\Http\Controllers\MonthsController::class, 'index'])->name('months');
    Route::get('/product-countries', [App\Http\Controllers\ProductCountriesController::class, 'index'])->name('product-countries');
    Route::get('/stock-types', [App\Http\Controllers\StockTypesController::class, 'index'])->name('stock-types');
    Route::get('/suppliers', [App\Http\Controllers\SuppliersController::class, 'index'])->name('suppliers');

    Route::get('/export/units', [UnitsController::class, 'export'])->name('settings.units.export');
    Route::resource('units', UnitsController::class);



    Route::get('/years', [App\Http\Controllers\YearsController::class, 'index'])->name('years');
});
