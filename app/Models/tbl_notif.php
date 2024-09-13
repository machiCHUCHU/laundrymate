<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_notif extends Model
{
    use HasFactory;

    protected $primaryKey = 'NotifID';

    protected $fillable = [
        'CustomerID',
        'BookingID',
        'Title',
        'Message',
        'is_read',
    ];
}
