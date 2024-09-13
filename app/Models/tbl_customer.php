<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_customer extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'CustomerName',
        'CustomerSex',
        'CustomerAddress',
        'CustomerContactNumber',
        'VerifiedAt',
        'CustomerImage',
        'UserID'
    ];

    public function bookings(){
        return $this->hasMany(tbl_booking::class, 'CustomerID');
    }
    
}
