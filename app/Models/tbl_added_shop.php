<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_added_shop extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'AddedShopID';
    
    protected $fillable = [
        'IsValued',
        'Date',
        'ShopID',
        'CustomerID'
    ];

    public function booking(){
        return $this->belongsTo(tbl_booking::class, 'BookingID');
    }

    public function shop(){
         $this->belongsTo(tbl_shop::class, 'ShopID');
        }
    public function customer(){
         $this->belongsTo(tbl_customer::class, 'CustomerID');
        }

        
}
