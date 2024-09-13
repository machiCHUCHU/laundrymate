<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class tbl_booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'BookingID';

    protected $fillable = [
        'CustomerLoad',
        'LoadCost',
        'Notes',
        'Schedule',
        'DateIssued',
        'Status',
        'CustomerID',
        'LaundryPrefID',
        'ShopID',
        'ServiceID',
        'PaymentStatus'
    ];

    public function shop(){
        return $this->belongsTo(tbl_shop::class, 'ShopID');
    }

    public function customer(){
        return $this->belongsTo(tbl_customer::class, 'CustomerID');
    }

    public function laundryPreference(){
        return $this->belongsTo(tbl_laundry_preference::class, 'LaundryPreferenceID');
    }

    public function billing(){
        return $this->hasOne(tbl_billing::class, 'BookingID');
    }

    public function report(){
        return $this->hasOne(tbl_report::class, 'BookingID');
    }
}
