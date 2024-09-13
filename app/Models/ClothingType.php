<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    use HasFactory;

    protected $table = 'tbl_clothing_types';

    protected $fillable = [
        'ClothingType'
    ];

    public $timestamps = true;
}
