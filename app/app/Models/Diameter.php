<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diameter extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['size'];

    public function get_all() {
        return self::orderby('id')->get();
    }
}
