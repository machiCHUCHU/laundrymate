<?php

namespace App\Http\Controllers;

use App\Models\tbl_customer;
use App\Models\User;
use App\Models\tbl_owner;
use App\Models\tbl_laundry_service;
use App\Models\tbl_shop_machine;
use App\Models\tbl_shop_service;
use App\Models\tbl_shop;
use App\Models\tbl_inventory;
use App\Models\tbl_added_shop;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
class RegController extends Controller
{
    //all goods for customer register
    public function customer_register(Request $request){
    
            $infos = $request->validate([
                'Name' => 'required|string',
                'Sex' => 'required|string',
                'Address' => 'required|string',
                'ContactNumber' => 'required|string',
            ]);
    
            $credentials = $request->validate([
                'email' => 'required|email',
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
    
            $user = User::create([
                'email' => $credentials['email'],
                'username' => $credentials['username'],
                'password' => bcrypt($credentials['password']),
                'usertype' => 'customer'
            ]);

            
            tbl_customer::create([
                'Name' => $infos['Name'],
                'Sex' => $infos['Sex'],
                'Address' => $infos['Address'],
                'ContactNumber' => $infos['ContactNumber'],
                'Image' => 'imageName',
                'UserID' => $user->id
            ]);
    
            
            return response([
                'message' => 'success',
            ]);
        
    }

    public function registration(Request $request){
    
        $infos = $request->validate([
            'name' => 'required|string',
            'sex' => 'required|string',
            'address' => 'required|string',
            'image' => 'nullable|string'
        ]);

        $credentials = $request->validate([
            'contact' => 'required',
            'password' => 'required|string',
            'usertype' => 'required'
        ]);

        $user = User::create([
            'contact' => $credentials['contact'],
            'password' => bcrypt($credentials['password']),
            'usertype' => $credentials['usertype'],
        ]);
        $imageBase64 = $request->input('image');

        if(empty($imageBase64)){
            $imageName = null;
        }else{
            $image = base64_decode($imageBase64);
        $imageName = uniqid() . '.jpg';
        $directory = public_path('images');
        $imagePath = $directory . '/' . $imageName;
        file_put_contents($imagePath,$image);
        }

        if($credentials['usertype'] == 'customer'){
            tbl_customer::create([
                'CustomerName' => $infos['name'],
                'CustomerSex' => $infos['sex'],
                'CustomerAddress' => $infos['address'],
                'CustomerImage' => $imageName,
                'CustomerContactNumber' => $user->contact
            ]);
        }else{
            tbl_owner::create([
                'OwnerName' => $infos['name'],
                'OwnerSex' => $infos['sex'],
                'OwnerAddress' => $infos['address'],
                'OwnerImage' => $imageName,
                'OwnerContactNumber' => $user->contact
            ]);
        }

        return response([
            'response' => 'success',
        ], 200);
    
}

    //all goods for owner_register
    public function owner_register(Request $request){
        $infos = $request->validate([
            'Name' => 'required|string',
            'Sex' => 'required|string',
            'Address' => 'required|string',
            'ContactNumber' => 'required|string',
            'Image' => 'sometimes|string'
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'email' => $credentials['email'],
            'username' => $credentials['username'],
            'password' => bcrypt($credentials['password']),
            'usertype' => 'owner'
        ]);


        tbl_owner::create([
            'Name' => $infos['Name'],
            'Sex' => $infos['Sex'],
            'Address' => $infos['Address'],
            'ContactNumber' => $infos['ContactNumber'],
            'Image' => base64_decode($infos['Image']),
            'UserID' => $user->id
        ]);

        return response([
            'response' => 'success'
        ], 200);
    }

    //semi good for shop info registration
    public function owner_shop_info(Request $request){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->value('OwnerID');

                            $imageBase64 = $request['ShopImage'];

                            if(empty($imageBase64)){
                                $imageName = null;
                            }else{
                                $image = base64_decode($imageBase64);
        $imageName = uniqid() . '.jpg';
        $directory = public_path('images');
        $imagePath = $directory . '/' . $imageName;
        file_put_contents($imagePath,$image);
                            }

        

        $machine = $request->validate([
            'WasherQty' => 'required|integer',
            'WasherTime' => 'required|integer',
            'DryerQty' => 'required|integer',
            'DryerTime' => 'required|integer',
            'FoldingTime' => 'required|integer'
        ]);

        $laundry_service1 = $request->validate([
            'LightWeight' => 'required',
            'LightPrice' => 'required|integer',
        ]);

        $laundry_service2 = $request->validate([
            'HeavyWeight' => 'required',
            'HeavyPrice' => 'required|integer',
        ]);

        $laundry_service3 = $request->validate([
            'ComfWeight' => 'required',
            'ComfPrice' => 'required|integer',
        ]);

        $shop = $request->validate([
            'ShopName' => 'required|string',
            'ShopAddress' => 'required|string',
            'MaxLoad' => 'required|string',
            'WorkDay' => 'required|string',
            'WorkHour' => 'required|string',
        ]);


        

        $create_shop_machine = tbl_shop_machine::create([
           'WasherQty' => $machine['WasherQty'],
           'WasherTime' => $machine['WasherTime'],
           'DryerQty' => $machine['DryerQty'],
           'DryerTime' => $machine['DryerTime'],
           'FoldingTime' => $machine['FoldingTime'],
        ]);

        function generateShopCode() {
            $bytes = random_bytes(3); 
            return Str::upper(bin2hex($bytes));
        }
        
        $shopCode = generateShopCode();

         
         $shop = [
            'ShopName' => $request['ShopName'],
            'ShopImage' => $imageName,
            'ShopAddress' => $request['ShopAddress'],
            'MaxLoad' => $request['MaxLoad'],
            'RemainingLoad' => $request['MaxLoad'],
            'WorkDay' => $request['WorkDay'],
            'WorkHour' => $request['WorkHour'],
            'ShopMachineID' => $create_shop_machine->ShopMachineID,
            'ShopCode' => $shopCode,
            'OwnerID' => $ownerId
         ];
         $new_shop_id = tbl_shop::create($shop);

         $new_id = $new_shop_id->ShopID;
         

         tbl_laundry_service::insert([
            'ServiceName' => 'Light Load',
            'LoadWeight' => $laundry_service1['LightWeight'],
            'LoadPrice' => $laundry_service1['LightPrice'],
            'ShopID' =>   $new_id
         ]);

         tbl_laundry_service::insert([
            'ServiceName' => 'Heavy Load',
            'LoadWeight' => $laundry_service2['HeavyWeight'],
            'LoadPrice' => $laundry_service2['HeavyPrice'],
            'ShopID' =>   $new_id
         ]);

         tbl_laundry_service::insert([
            'ServiceName' => 'Comforter Load',
            'LoadWeight' => $laundry_service3['ComfWeight'],
            'LoadPrice' => $laundry_service3['ComfPrice'],
            'ShopID' =>   $new_id
         ]);

         $shop_code = DB::table('tbl_shops')
            ->where('OwnerID',$ownerId)
            ->pluck('ShopCode');

         return response([
            'response' => 'success',
            'shopcode' => $shop_code,
        ], 200);
    }

    public function inventory(Request $request){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->value('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

        $inv = $request->validate([
            'ItemName' => 'required|string',
            'ItemQty' => 'required|integer',
            'itemVolume' => 'required|integer',
            'volumeuse' => 'required|integer',
            'category' => 'required|string',
            'isuse' => 'sometimes'
        ]);

        tbl_inventory::create([
            'ItemName' => $inv['ItemName'],
            'ItemQty' => $inv['ItemQty'],
            'ItemVolume' => $inv['itemVolume'],
            'RemainingVolume' => '0',
            'VolumeUse' => $inv['volumeuse'],
            'Category' => $inv['category'],
            'IsUse' => $inv['isuse'],
            'ShopID' => $shopId
        ]);

        return response([
            'response' => 'Item added',
        ], 200);
    }

    public function req_shop(Request $request){

        $shopReq = $request->validate([
            'IsValued' => 'required|string',
            'ShopID' => 'required|integer',
            'CustomerID' =>'required integer'
        ]);

        tbl_added_shop::create([
            'IsValued' => $shopReq['isVal'],
            'Date' => date('Y-m-d'),
            'ShopID' => $shopReq['itemQty'],
            'CustomerID' => $shopReq['cusID'],
        ]);
    }

    public function sms_otp(Request $request){
        $apiKey = env('SEMAPHORE_API_KEY');
        if(!Session::has('otpcode')|| now()->greaterThan(Session::get('expiration'))){
            Session::put('otpcode', rand(1000,9999));
            Session::put('expiration', now()->addMinutes(5));
            Session::save();
        }else{
            Session::get('otpcode');
        }

        $otpCode = Session::get('otpcode');
        $message = 'Your OTP is '.$otpCode.'. Please do not share this with anyone. This will be valid for 5 minutes';

        $ch = curl_init();

        $parameters = array(
    'apikey' => $apiKey,
    'number' => $request['contact'],
    'message' => $message,
    'sendername' => 'LAUNDRYMATE'
);
curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
curl_setopt( $ch, CURLOPT_POST, 1 );

curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );


curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );
curl_close ($ch);


return response([
    'message' => 'OTP has been sent'
]);

    
        
    }
    
    public function verify_otp(Request $request){
        if(!Session::has('otpcode') || now()->greaterThan(Session::get('expiration'))){
            return response([
                'message' => 'Please request another one.'
            ],409);
        }

        $getOtp = Session::get('otpcode');

        if($request['otpinput'] == $getOtp){
            Session::forget('otpcode');
            Session::forget('expiration');
            return response([
                'message' => 'Registered Successfully'
            ]);
        }else{
            return response([
                'message' => 'Invalid OTP. Please try again.'
            ],409);
        }
    }

    public function change_password(Request $request){
        User::where('contact', $request['contact'])->update(['password' => bcrypt($request['password'])]);

        return response([
            'message' => 'Password has been updated'
        ]);
    }
}
