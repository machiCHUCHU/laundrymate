<?php

namespace App\Http\Controllers;
use App\Models\tbl_added_shop;
use App\Models\tbl_booking;
use App\Models\tbl_laundry_service;
use App\Models\tbl_notif;
use App\Models\tbl_rating;
use App\Models\tbl_shop;
use App\Models\tbl_customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

class validationController extends Controller
{
    public function is_number_exist(Request $request){
    

        $isExist = User::where('contact',$request['contact'])->exists();

        return response([
            'message' => $isExist
        ]);
    }
}
