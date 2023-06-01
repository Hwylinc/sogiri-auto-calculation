<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        "group_code"   //計算結果番号
    ];

}
