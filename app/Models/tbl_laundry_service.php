<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_laundry_service extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'ServiceID';

    protected $fillable = [
        'ServiceName',
        'Description',
        'LoadWeight',
        'LoadPrice',
        'ShopID'
    ];

    
}
