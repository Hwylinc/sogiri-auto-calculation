<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationCode extends Model
{
    use HasFactory;
    protected $fillable = [
                  'code'
                , 'client_id'
                , 'house_name'
                , 'factory_id'
                , 'calculation_status'
            ];


    // *******************************************
    // 計算対象一覧情報を取得する条件
    // *******************************************
    public function scopeGetCalculationRequestListCondition($query, $params) {
        $query->join('clients', 'calculation_codes.client_id', '=', 'clients.id');
        foreach ($params as $key => $value) {
            $query = $query->where('calculation_codes.'.$key, '=', $value);
        }
        return $query;
    }
}
