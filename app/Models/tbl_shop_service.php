<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_shop_service extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'IsSelfService',
        'SelfServiceDeduct',
        'IsFullService'
    ];

    public function shop(){
        return $this->belongsTo(tbl_shop::class, 'ShopID');
    }

    public function laundryService(){
        return $this->belongsTo(tbl_laundry_service::class, 'LaundryServiceID');
    }

    public function reports(){
        return $this->hasMany(tbl_report::class, 'ShopServiceID');
    }
}
