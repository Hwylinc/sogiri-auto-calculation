<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationRequest extends Model
{
    use HasFactory;


    // *******************************************
    // 計算対象一覧情報を取得する条件
    // *******************************************
    public function scopeGetCalculationRequestListCondition($query, $params) {
        return $query->join('diameters', 'calculation_requests.diameter_id', '=', 'diameters.id')
        ->where('calculation_requests.code', '=', $params['code']);
    }

}
