<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationGroupCalculationCode extends Model
{
    use HasFactory;

    protected $table = 'calculation_group_calculation_code';

    protected $fillable = [
        "group_code"   //紐づく計算結果番号 
      , "code"         //紐づく計算番号
    ];

    // *******************************************
    // 紐づく計算結果を取得する条件
    // *******************************************
    public function scopeGetCalGroupCodeByFactIdCondition($query, $params) 
    {
        return $query->select('*', 'calculation_codes.created_at as created', 'calculation_groups.id as group_id')
                     ->join('calculation_groups', 'calculation_group_calculation_code.group_code', '=', 'calculation_groups.group_code')
                     ->join('calculation_codes', 'calculation_group_calculation_code.code', '=', 'calculation_codes.code')
                     ->join('clients', 'calculation_codes.client_id', '=', 'clients.id')
                     ->where('calculation_codes.factory_id', '=', $params['factory_id'])
                     ->orderby('calculation_groups.id', 'DESC');
    }
}
