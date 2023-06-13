<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    use HasFactory;

    protected $fillable = [
         "group_code"      //計算依頼グループコード   
       , "diameter_id"     //鉄筋径ID
       , "times"           //切断順番
       , "cutting_order"   //切断順番 
       , "length"          //長さ 
       , "set_number"      //同時切断セット本数	  
       , "port_id"         //吐出口ID 
    ];

    // *******************************************
    // 紐づく計算結果を取得する条件
    // *******************************************
    public function scopeGetCalculationResultListCondition($query, $params) {
        return $query->where('group_code', '=', $params['group_code'])
                     ->where('times', '!=', 0)
                     ->where('cutting_order', '!=', 0);
    }
    // *******************************************
    // 紐づく計算結果を取得する条件（例外処理）
    // *******************************************
    public function scopeGetExceptionListCondition($query, $params) {
        return $query->where('group_code', '=', $params['group_code'])
                     ->where('times', '=', 0)
                     ->where('cutting_order', '=', 0);
    }
}