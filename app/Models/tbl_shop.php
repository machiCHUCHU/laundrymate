<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_shop extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = 'ShopID';

    protected $fillable = [
        'ShopName',
        'ShopAddress',
        'MaxLoad',
        'RemainingLoad',
        'WorkDay',
        'WorkHour',
        'ShopStatus',
        'ShopCode',
        'ShopServiceID',
        'ShopMachineID',
        'OwnerID'
    ];

    public function owner()
    {
        return $this->hasOne(tbl_owner::class, 'OwnerID');
    }

    public function inventory(){
        return $this->hasMany(tbl_inventory::class, 'ShopID');
    }

    public function bookings(){
        return $this->hasMany(tbl_booking::class, 'ShopID');
    }

    public function shopServices(){
        return $this->hasMany(tbl_shop_service::class, 'ShopID');
    }

    public function shopMachines(){
        return $this->hasMany(tbl_shop_machine::class, 'ShopID');
    }

}
