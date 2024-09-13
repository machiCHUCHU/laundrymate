<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_clothing_types extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'ClothingTypeID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ClothingType',
    ];

    protected $casts = [
        'ClothingType' => 'string',
    ];
}
