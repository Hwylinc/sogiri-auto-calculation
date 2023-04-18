<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/cal-test', [App\Http\Controllers\CsvCalculatorController::class, 'calTest']);
Route::get('/cal-second-test', [App\Http\Controllers\CsvCalculatorController::class, 'calSecondTest']);



Route::get('/cal-third-test', [App\Http\Controllers\TestCalculatorController::class, 'index']);
