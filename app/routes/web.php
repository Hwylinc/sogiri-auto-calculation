<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


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
Route::middleware('auth')->group(function(){
    // ホーム画面
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // 4_ * （鉄筋情報管理)
    Route::controller(App\Http\Controllers\RebarController::class)->group(function () {
        // 一覧画面
        Route::get('/rebar/list',                                'getList')->name('rebar.list');
        // 鉄筋情報入力方法・工場選択画面
        Route::get('/rebar/select',                              'getSelect')->name('rebar.select');
        // 鉄筋情報手入力画面
        Route::get('/rebar/register/{diameter}',                 'getRegister')->name('rebar.register');
        // 鉄筋情報手入力確認画面
        Route::get('/rebar/confirm/{diameter}',                  'getConfirm')->name('rebar.confirm');
        // 鉄筋情報手入力編集画面
        Route::get('/rebar/edit/{calculation_id}/{diameter}',    'getEdit')->name('rebar.edit');
        // 鉄筋情報CSVアップロード画面
        Route::get('/rebar/csv-register',                        'getCsvRegister')->name('rebar.csv-register');
        // 鉄筋情報CSV読み込みアップロード画面
        Route::post('/rebar/csv-upload',                         'postCsvUpload')->name('rebar.csv-upload');
        // 鉄筋情報CSVアップロード確認画面
        Route::get('/rebar/csv-confirm',                         'getCsvConfirm')->name('rebar.csv-confirm');
        // 鉄筋情報登録・編集完了処理
        Route::post('/rebar/complete',                           'postComplete')->name('rebar.complete');
        // 鉄筋情報登録・編集完了画面
        Route::get('/rebar/complete',                            'getComplete')->name('rebar.done');
        // 鉄筋情報詳細画面
        Route::get('/rebar/detail/{calculation_id}',             'getDetail')->name('rebar.detail');
    });

    // 5_ * （計算管理) 
    Route::controller(App\Http\Controllers\CalculatorController::class)->group(function () {
        // 計算開始確認画面
        Route::get('/calculate/ready',                     'getReady')->name('calculate.ready');
        // 計算結果確認画面
        Route::get('/calculate/result-confirm',            'getResultConfirm')->name('calculate.confirm');
        // 計算結果登録処理
        Route::post('/calculate/complete',                 'postComplete')->name('calculate.complete');
        // 計算結果完了画面
        Route::get('/calculate/complete',                  'getComplete')->name('calculate.done');
        // 計算結果履歴一覧画面
        Route::get('/calculate/list',                      'getList')->name('calculate.list');
        // 計算結果詳細画面
        Route::get('/calculate/detail/{calculation_id}',   'getDetail')->name('calculate.detail');
    });

    // 6_ * （予備材管理) 
    Route::controller(App\Http\Controllers\SpareController::class)->group(function () {
        // 工場別予備材一覧
        Route::get('/spare/{factry_id}',       'getList')->name('spare.list');
        // 予備材編集画面
        Route::get('/spare/edit/{factry_id}',  'getEdit')->name('spare.edit');
        // 編集完了処理
        Route::post('/spare/complete',         'postComplete')->name('spare.complete');
    });

    // Ajax系 
    Route::controller(App\Http\Controllers\AjaxController::class)->group(function () {
        // 鉄筋情報CSV読み込みアップロード画面
        Route::post('/ajax/rebar/csv-upload',                      'postCsvUpload')->name('ajax.rebar.csv-upload');
        // 鉄筋入力情報をセッションに保存
        Route::post('/ajax/rebar/update/{factry_id}/{diameter}',   'postRebarUpdate')->name('ajax.rebar.update');
    });
    
    // 計算テスト用（後程削除の必要あり）
    Route::get('/cal-test', [App\Http\Controllers\CsvCalculatorController::class, 'calTest']);
    Route::get('/cal-second-test', [App\Http\Controllers\CsvCalculatorController::class, 'calSecondTest']);
});


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

// 予備材一覧
Route::get('/spare/{screen}/{select_id}', function(Request $request, $screen, $select_id) {
    return routeStore($request, App\Http\Controllers\TestController::class, 'get', ['screen' => $screen, 'select_id' => $select_id]);
})->name('spare');
Route::post('/spare/{screen}/{select_id}', function(Request $request, $screen, $select_id) {
    return routeStore($request, App\Http\Controllers\TestController::class, 'edit', ['screen' => $screen, 'select_id' => $select_id]);
})->name('spare-edit');