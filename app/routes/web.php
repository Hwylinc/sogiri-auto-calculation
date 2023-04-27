<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/**
 * Undocumented function
 *
 * @param [type] $request require　リクエスト内容
 * @param [type] $controller require　　routeのcontroller
 * @param [type] $exe       require 実行メソッド
 * @param [type] $value　　　パラメータ
 * @return void
 */
function routeStore($request, $controller, $exe, $value=null) {
    $instance = new $controller();
    return $instance->store($request, $exe, $value);
}

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

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/cal-test', [App\Http\Controllers\CsvCalculatorController::class, 'calTest']);
Route::get('/cal-second-test', [App\Http\Controllers\CsvCalculatorController::class, 'calSecondTest']);

// 予備材一覧
Route::get('/spare/{screen}/{select_id}', function(Request $request, $screen, $select_id) {
    return routeStore($request, App\Http\Controllers\TestController::class, 'get', ['screen' => $screen, 'select_id' => $select_id]);
})->name('spare');
Route::post('/spare/{screen}/{select_id}', function(Request $request, $screen, $select_id) {
    return routeStore($request, App\Http\Controllers\TestController::class, 'edit', ['screen' => $screen, 'select_id' => $select_id]);
})->name('spare-edit');