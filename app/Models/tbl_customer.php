<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_customer extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'CustomerID';
    protected $fillable = [
        'CustomerName',
        'CustomerSex',
        'CustomerAddress',
        'CustomerContactNumber',
        'VerifiedAt',
        'CustomerImage',
        'UserID'
    ];


    
}
