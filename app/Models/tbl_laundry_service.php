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

    public function shop(){
        return $this->hasMany(tbl_shop::class, 'ShopID');
    }

    public function shopServices(){
        return $this->hasMany(tbl_shop_service::class, 'LaundryServiceID');
    }
}
