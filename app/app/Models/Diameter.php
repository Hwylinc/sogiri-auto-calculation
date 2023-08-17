<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diameter extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['size'];

    public static function get_all() {
        return self::orderby('id')->get();
    }

    public static function get_first($offset)
    {
        return self::orderby('id')
            ->offset($offset)
            ->limit(1)
            ->first();
    }

    public static function get_by_id($id)
    {
        return self::where('id', $id)->first();
    }
}
