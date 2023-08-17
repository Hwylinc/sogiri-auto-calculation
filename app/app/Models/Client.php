<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'external_client_id'];

    public static function get_all()
    {
        return self::orderby('id')->get();
    }

    public static function get_by_id($id)
    {
        return self::where('id', $id)->first();
    }
}
