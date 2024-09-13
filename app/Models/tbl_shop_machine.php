<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_shop_machine extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'ShopMachineID';

    protected $fillable = [
        'WasherQty',
        'WasherTime',
        'DryerQty',
        'DryerTime',
        'FoldingTime'
    ];

    public function shop(){
        return $this->belongsTo(tbl_shop::class, 'ShopID');
    }
}
