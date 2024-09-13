<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_owner extends Model
{
    use HasFactory;

    protected $primaryKey = 'OwnerID';
    public $timestamps = false;
    protected $fillable = [
        'OwnerName',
        'OwnerSex',
        'OwnerAddress',
        'OwnerContactNumber',
        'VerifiedAt',
        'OwnerImage',
        'UserID',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID'); 
    }

    public function shop(){
        return $this->hasOne(tbl_shop::class);
    }
}
