<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Change 
use Illuminate\Support\Str;

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
    
    public static function insert(array $data)
    {
        return self::create([
            'code' => Str::uuid(),
            'client_id' => $data['client_id'],
            'house_name' => $data['house_name'],
            'factory_id' => $data['factory_id'],
            'calculation_status' => 0,
        ]);
    }


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
