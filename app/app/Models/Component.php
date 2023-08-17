<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'port_id', 'external_component_id', 'factory_id'];

    public static function get_all()
    {
        return self::orderby('id')->get();
    }
}
