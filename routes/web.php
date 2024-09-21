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

use App\Http\Controllers\SuppliersController;

// Public route: accessible by everyone
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes (Login, Register, etc.)
Auth::routes();

// Secured routes: only accessible if authenticated
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/export/units', [UnitsController::class, 'export'])->name('settings.units.export');
    Route::resource('units', UnitsController::class);
    Route::resource('currencies', CurrenciesController::class);
    Route::resource('years', YearsController::class);
    Route::resource('countries', ProductCountriesController::class);
    Route::resource('brands', BrandsController::class);
    Route::resource('months', MonthsController::class);
    Route::resource('customers', CustomersController::class);
    Route::resource('categories', CategoriesController::class);
    Route::resource('type', StockTypesController::class);
    Route::resource('suppliers', SuppliersController::class);



});
