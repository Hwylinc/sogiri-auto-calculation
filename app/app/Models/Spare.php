<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spare extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'priority_flg', 'diameters_id'];

    public function get_by_id($id) {
        return self::where('diameters_id', $id)->orderby('id')->get();
    }

    public function update_all_priority_reset($select_id) {
        self::where(['diameters_id' => $select_id])->update(['priority_flg' => 0]);
    }

    public function update_priority($ids) {
        self::whereIn('id', $ids)->update(['priority_flg' => 1]);
    }
    
    // *******************************************
    // 予備材一覧情報を取得する条件
    // *******************************************
    public function scopeGetSpareListCondition($query) {
        return $query->join('diameters', 'spares.diameters_id', '=', 'diameters.id');
    }
}
