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

   
}
