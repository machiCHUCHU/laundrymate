<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_laundry_preference extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ServiceType',
        'WashingPref',
        'DryingPref',
    ];

    public function booking(){
        return $this->hasOne(tbl_booking::class, 'LaundryPreferenceID');
    }
}
