<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delete_picture extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'Picture'
    ];
}
