<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    use HasFactory;

    protected $fillable = [
         "code"            //計算番号   
       , "diameter_id"     //鉄筋径ID
       , "times"           //切断順番
       , "cutting_order"   //切断順番 
       , "component_id"    //部材ID 
       , "length"          //長さ 
       , "set_number"      //同時切断セット本数	  
       , "port_id"         //吐出口ID 
    ];

    // *******************************************
    // 紐づく計算結果を取得する条件
    // *******************************************
    public function scopeGetCalculationResultListCondition($query, $params) {
        return $query->where('code', '=', $params['code']);
    }

}
