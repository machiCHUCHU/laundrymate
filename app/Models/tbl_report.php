<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_report extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'BookingID',
        'CustomerID',
        'ShopServiceID',
        'BillingID',
        'InventoryID',
        'RatingID',
        'DateIssued',
    ];

    public function booking(){
        return $this->belongsTo(tbl_booking::class, 'BookingID');
    }
    public function shopService(){
        return $this->belongsTo(tbl_shop_service::class, 'ShopServiceID');
    }

}
