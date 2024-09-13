<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddedShop extends Model
{
    use HasFactory;

    protected $table = 'tbl_added_shops';
    protected $primaryKey = 'AddedShopID';
    public $timestamps = false;

    protected $fillable = [
        'IsValued',
        'Date',
        'ShopID',
        'CustomerID',
    ];

}
