<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\tbl_customer;
use App\Models\tbl_owner;
class tbl_user extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;

    protected $fillable = [
        'Email',
        'Username',
        'UserType',
        'Password'
    ];

    public function customer(){
        return $this->hasOne(tbl_customer::class, 'UserID');
    }

    public function owner(){
        return $this->hasOne(tbl_owner::class, 'UserID');
    }

    
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    protected function casts(): array{
        return [
            'Password' => 'hashed',
        ];
    }
}
