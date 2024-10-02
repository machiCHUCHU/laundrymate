<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_shop extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = 'ShopID';

    protected $fillable = [
        'ShopName',
        'ShopImage',
        'ShopAddress',
        'MaxLoad',
        'RemainingLoad',
        'WorkDay',
        'WorkHour',
        'ShopStatus',
        'ShopCode',
        'ShopServiceID',
        'ShopMachineID',
        'OwnerID'
    ];

    
}
