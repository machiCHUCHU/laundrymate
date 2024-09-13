<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class tbl_inventory extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $primaryKey = 'InventoryID';

    protected $fillable = [
        'ItemName',
        'ItemQty',
        'ItemVolume',
        'VolumeUse',
        'RemainingVolume',
        'ShopID'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function shop(){
        return $this->belongsTo(tbl_shop::class, 'ShopID');
    }
}
