<?php

namespace App\Http\Controllers;

use App\Models\tbl_shop;
use App\Models\tbl_owner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
class authController extends Controller
{
    public function login(Request $request){
        $attrs = $request->validate([
            'contact' => 'required',
            'password' => 'required'
        ]);

        
        if(!Auth::attempt($attrs)){
            return response([
                'message' => 'Invalid Credentials'
            ]);
        }

        $user = Auth::user();
            $tokenKey = $user->createToken('secret');
            $token = $tokenKey->plainTextToken;
            $tokenKey->accessToken->expires_at = Carbon::now()->addDays(7);
            $tokenKey->accessToken->save();

            $expiration = Carbon::parse($tokenKey->accessToken->expires_at);

            $expireDate = $expiration->format('Y-m-d H:i:s');

        
        return response([
            'user' => $user,
            'token' => $token,
            'expires_at' => $expireDate
        ], 200);
        
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response([
            'message' => 'Successfully logged out'
        ]);
    }

    public function shop_match(Request $request){
        $user = Auth::user(); 
        $userContact = $user->contact;

        
        $shop = DB::table('tbl_shops')
        ->join('tbl_owners', 'tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->join('users', 'tbl_owners.OwnerContactNumber', 'users.contact')
        ->select('*')
        ->where('tbl_owners.OwnerContactNumber', $userContact)
        ->get();

        if($shop->isEmpty()){
            return response([
                'response' => 'empty'
            ]);
        }else{
            return response([
                'response' => 'found'
            ]);
        }
        
    }

    public function try(){
        $shop = DB::table('tbl_shops')
        ->join('tbl_owners', 'tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->join('users', 'tbl_owners.UserID', 'users.id')
        ->select('*')
        ->where('tbl_owners.UserID', '15')
        // ->where('tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->get();

        return response([
            'try' => $shop
        ]);
    }

    public function remember_me(){
        $user = Auth::user();
        $userType = $user->usertype;

        return response([
            'message' => $userType
        ]);
    }
}
