<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalculationRequests extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $fillable = ['code', 'length', 'number', 'diameter_id', 'component_id', 'port_id', 'client_id', 'house_name', 'user_id', 'display_order'];

    public static function ins(array $data) 
    {
        return self::create([
            'length'            => (int)$data['length'],        // 長さ
            'number'            => (int)$data['number'],        // 本数
            'diameter_id'       => (int)$data['diameter_id'],   // 鉄筋径id
            'component_id'      => (int)$data['component_id'],  // 部材id
            'port_id'           => (int)$data['port_id'],       // 吐き出し口id
            'client_id'         => (int)$data['client_id'],     // メーカid
            'house_name'        => $data['house_name'],         // 邸名
            'user_id'           => (int)$data['user_id'],       // 登録者id
            'display_order'     => (int)$data['display_order'], // 表示順
            'code'              => $data['code'],               // 計算番号
        ]);
    }

    public static function getWhereCalucReq(array $where)
    {
        return self::where($where)
            ->orderby('component_id', 'asc')
            ->orderby('display_order', 'asc')
            ->get();
    }

    public static function updateById(int $id, array $set)
    {
        return self::where('id', $id)
            ->update($set);
    }

    public static function deleteById(int $id)
    {
        return self::where('id', $id)->delete();
    }

    // *******************************************
    // 計算対象一覧情報を取得する条件
    // *******************************************
    public function scopeGetCalculationRequestListCondition($query, $params) {
        return $query->join('diameters', 'calculation_requests.diameter_id', '=', 'diameters.id')
        ->where('calculation_requests.code', '=', $params['code']);
    }
}

?>