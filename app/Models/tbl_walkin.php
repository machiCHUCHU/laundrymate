<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class tbl_walkin extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'WalkinID';
    protected $fillable = [
        'ContactNumber',
        'WalkinLoad',
        'LaundryPrefID',
        'ServiceID',
        'ShopID',
        'DateIssued',
        'Total',
        'Status',
        'updated_at',
        'PaymentStatus'
    ];
}
