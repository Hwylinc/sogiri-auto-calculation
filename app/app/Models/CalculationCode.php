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
                // , 'client_id'
                , 'client_name'
                , 'house_name'
                , 'factory_id'
                , 'calculation_status'
                , 'length'
            ];
    
    public static function insert(array $data)
    {
        return self::create([
            'code' => Str::uuid(),
            'client_name' => $data['client_name'],
            'house_name' => $data['house_name'],
            'factory_id' => $data['factory_id'],
            'length' => config('const.stadard_size'), // 生材の長さを変更できるようになったら変更必要
            'calculation_status' => 2,
        ]);
    }

    public static function get_by_code($code) {
        return self::where('code', '=', $code)->first();
    } 

    public static function get_by_codes($codes) {
        return self::select('*', 'client_name as name', 'calculation_codes.created_at as create')
            ->whereIn('code', $codes)
            ->orderby('create', 'ASC')
            ->get();
    } 

    public static function delete_by_ids($codes) {
        return self::whereIn('code', $codes)->delete();
    }

    // *******************************************
    // 計算対象一覧情報を取得する条件
    // *******************************************
    public function scopeGetCalculationRequestListCondition($query, $params) {
        // $query->select('*', 'calculation_codes.created_at as create', );
            // ->join('clients', 'calculation_codes.client_id', '=', 'clients.id');
        $query->select('*', 'client_name as name', 'calculation_codes.created_at as create' );
        foreach ($params as $key => $value) {
            $query = $query->where('calculation_codes.'.$key, '=', $value);
        }
        return $query;
    }
}
