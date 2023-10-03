<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spare extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'priority_flg', 'diameters_id', 'order_number'];

    public function get_by_id($id) {
        return self::where('diameters_id', $id)->orderby('id')->get();
    }

    public function update_all_priority_reset($select_id) {
        self::where(['diameters_id' => $select_id])->update(['priority_flg' => 0, 'order_number' => 999]);
    }

    public function update_priority($id, $order) {
        self::where('id', $id)->update(['priority_flg' => 1, 'order_number' => $order]);
    }
    
    // *******************************************
    // 予備材一覧情報を取得する条件
    // *******************************************
    public function scopeGetSpareListCondition($query) {
        return $query->join('diameters', 'spares.diameters_id', '=', 'diameters.id')
        ->orderby('diameters_id', 'ASC')
        ->orderby('priority_flg', 'DESC')
        ->orderby('order_number', 'DESC')
        ->orderby('length', 'DESC');
    }
}
