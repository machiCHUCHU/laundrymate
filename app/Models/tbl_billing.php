<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_billing extends Model
{
    use HasFactory;

    public function booking(){
        return $this->belongsTo(tbl_booking::class, 'BookingID');
    }
}
