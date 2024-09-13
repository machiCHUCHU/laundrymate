<?php

namespace App\Http\Controllers;
use App\Models\tbl_added_shop;
use App\Models\tbl_customer;
use App\Models\tbl_inventory;
use App\Models\tbl_booking;
use App\Models\tbl_laundry_preference;
use App\Models\tbl_laundry_service;
use App\Models\tbl_owner;
use App\Models\tbl_rating;
use App\Models\tbl_shop;
use App\Models\tbl_shop_machine;
use App\Models\User;
use App\Models\tbl_walkin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ownerShopController extends Controller
{
    public function shop_code_display($id){
        $shop = DB::table('tbl_shops')
        ->join('tbl_owners', 'tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->join('users', 'tbl_owners.UserID', 'users.id')
        ->where('tbl_owners.UserID', $id)
        // ->where('tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->value('ShopCode');

        $newlyregisterShopID = DB::table('tbl_shops')
        ->join('tbl_owners', 'tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->join('users', 'tbl_owners.UserID', 'users.id')
        ->where('tbl_owners.UserID', $id)
        ->value('ShopID');

        return response([
            'shopcode' => $shop,
            'newshopid' => $newlyregisterShopID
        ],200);
    }

    public function added_shop_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->value('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

        $added_shop = DB::table('tbl_added_shops')
        ->join('tbl_customers', 'tbl_added_shops.CustomerID', '=', 'tbl_customers.CustomerID')
        ->select('tbl_customers.*', 'tbl_added_shops.*')
        ->where('tbl_added_shops.ShopID', $shopId)
        ->whereNot('tbl_added_shops.IsValued', '2')
        ->get();

        return response([
            'addedshop' => $added_shop,
        ]);
        
    }

    public function added_shop_status($id, Request $request){
        $added_shop = tbl_added_shop::find($id);

            $added_shop->update([
                'IsValued' => $request['stat'],
                'Date' => date('Y-m-d')
            ]);

            return response([
                'message' => 'Request Accepted'
            ]);
       
    }

    public function inventory_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $inventory = DB::table('tbl_inventories')
        ->join('tbl_shops', 'tbl_inventories.ShopID', '=', 'tbl_shops.ShopID')
        ->select('tbl_inventories.ItemName', 'tbl_inventories.ItemQty', 
        'tbl_inventories.RemainingVolume', 'tbl_inventories.InventoryID',
        'tbl_inventories.VolumeUse', 'tbl_inventories.ItemVolume')
        ->where('tbl_inventories.ShopID', $shopId)
        ->whereNull('tbl_inventories.deleted_at')
        ->orderBy('tbl_inventories.ItemQty', 'desc')
        ->get();

        $total_item =  DB::table('tbl_inventories')
        ->join('tbl_shops', 'tbl_inventories.ShopID', '=', 'tbl_shops.ShopID')
        ->where('tbl_inventories.ShopID', $shopId)
        ->whereNull('tbl_inventories.deleted_at')
        ->sum('tbl_inventories.ItemQty');

        $out_item = DB::table('tbl_inventories')
        ->join('tbl_shops', 'tbl_inventories.ShopID', '=', 'tbl_shops.ShopID')
        ->where('tbl_inventories.ShopID', $shopId)
        ->where('tbl_inventories.ItemQty', '0')
        ->whereNull('tbl_inventories.deleted_at')
        ->count('tbl_inventories.ItemQty');

        return response([
            'inventory' => $inventory,
            'total' => $total_item,
            'out' => $out_item
        ]);
        
    }

    public function inventory_delete($id){
        $inv_delete = tbl_inventory::find($id);
        $inv_delete->delete();
        return response([
            'response' => 'Item deleted'
        ],200);
        
    }

    public function inventory_edit(Request $request, $id){
        $inventory = tbl_inventory::find($id);

        if(!$inventory){
            return response([
                'message' => 'Request not found'
            ],403);
        }

        $inv = $request->validate([
            'ItemName' => 'required|string',
            'ItemQty' => 'required|integer',
            'itemVolume' => 'required|integer',
            'volumeuse' => 'required|integer'
        ]);

        $inventory->update([
            'ItemName' => $inv['ItemName'],
            'ItemQty' => $inv['ItemQty'],
            'ItemVolume' => $inv['itemVolume'],
            'VolumeUse' => $inv['volumeuse']
        ]);

        return response([
            'response' => 'success'
        ], 200);
        
    }

    // public function profile_display($id){
        
    //     return response([
    //         'profile' => DB::table('tbl_shops')
    //         ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', '=', 'tbl_shop_machines.ShopMachineID')
    //         ->join('tbl_owners', 'tbl_shops.OwnerID', '=', 'tbl_owners.OwnerID')
    //         ->join('tbl_shop_services', 'tbl_shops.ShopServiceID', '=', 'tbl_shop_services.ShopServiceID')
    //         ->select(
    //             'tbl_owners.Name', 'tbl_owners.Address', 'tbl_shops.ShopName', 'tbl_shops.ShopCode',
    //             'tbl_shop_machines.WasherQty', 'tbl_shop_machines.DryerQty', 'tbl_shops.OwnerID','tbl_shops.ShopAddress',
    //             'tbl_shops.ShopID'
    //         )
    //         ->where('tbl_shops.ShopID', $id)
    //         ->get()
    //     ],200);
        
    // }

    // public function pending_booking_display(){
    //     $user = Auth::user(); 
    //     $userId = $user->id;

    //     $ownerId = tbl_owner::where('UserID', $userId)
    //                         ->pluck('OwnerID');

    //     $shopId = tbl_shop::where('OwnerID', $ownerId)
    //                       ->pluck('ShopID');

    //     $pending = DB::table('tbl_bookings')
    //     ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
    //     ->join('tbl_laundry_preferences', 'tbl_bookings.LaundryPrefID', '=', 'tbl_laundry_preferences.LaundryPrefID')
    //     ->select(
    //         'tbl_customers.Name', 'tbl_customers.Name', 'tbl_customers.ContactNumber' , 'tbl_bookings.CustomerLoad',
    //         'tbl_laundry_preferences.WashingPref', 'tbl_laundry_preferences.DryingPref',
    //         'tbl_bookings.DateIssued', 'tbl_bookings.Status', 'tbl_bookings.BookingID',
    //          DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%h:%i %p') as TimeIssued"),
    //         'tbl_bookings.DateIssued'
    //         )
    //     ->where('tbl_bookings.Status', '0')
    //     ->where('tbl_bookings.ShopID', $shopId)
    //     ->get();

    //     $pending_count = DB::table('tbl_bookings')
    //     ->where('Status', '0')
    //     ->where('ShopID', $shopId)
    //     ->count();


    //     return response([
    //         'pending' => $pending,
    //         'pending_count' => $pending_count
    //     ]);
    // }

    public function bookings_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');

        $bookings = DB::table('tbl_bookings')
        ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('tbl_customers.*','tbl_laundry_services.*', 'tbl_bookings.*',
             DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"),
            )
        ->where('tbl_bookings.ShopID', $shopId)
        ->whereDate('tbl_bookings.Schedule', $now)
        ->get();

        return response([
            'bookings' => $bookings,
        ]);
    }

//     public function pending_booking_stat(Request $request, $id){
//     $user = Auth::user(); 
//     $userId = $user->id;

//     $ownerId = tbl_owner::where('UserID', $userId)->value('OwnerID');

//     $shopId = tbl_shop::where('OwnerID', $ownerId)->value('ShopID');

//     $shop = tbl_shop::find($shopId);

//     $pending_stat = tbl_booking::find($id);
//     $detergentID = tbl_inventory::find($request['detergentId']);

//     if (!$pending_stat) {
//         return response([
//             'message' => 'Request not found'
//         ], 403);
//     }

//     $detergent_use = $detergentID->VolumeUse;
//     $detergent_volume = $detergentID->ItemVolume;
//     $remaining_volume = $detergentID->RemainingVolume;
//     $detergent_quantity = $detergentID->ItemQty;
//     $shop_rem_load = $shop->RemainingLoad;

//     // Get the Laundry Machine ID
//     $laundryMachineID = DB::table('tbl_shops')
//         ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', '=', 'tbl_shop_machines.ShopMachineID')
//         ->join('tbl_bookings', 'tbl_shops.ShopID', '=', 'tbl_bookings.ShopID')
//         ->where('tbl_bookings.BookingID', $pending_stat->BookingID)
//         ->value('tbl_shop_machines.ShopMachineID');

//     // Retrieve the machine model instance
//     $machine_stat = tbl_shop_machine::find($laundryMachineID);
//     $machine_qty = $machine_stat->WasherQty;

//     if ($shop_rem_load == 0) {
//         $shop->update([
//             'ShopStatus' => 'full'
//         ]);

//         return response([
//             'message' => 'full'
//         ]);
//     } elseif ($machine_qty == 0) {
//         return response([
//             'status' => '0',
//             'message' => 'Machine is occupied'
//         ]);
//     } elseif ($detergent_quantity == 0 && $remaining_volume < $detergent_use) {
//         return response([
//             'message' => 'empty'
//         ]);
//     } else {
//         if ($remaining_volume == 0 || $remaining_volume < $detergent_use) {
//             $pending_stat->update([
//                 'Status' => $request['stat'],
//             ]);

//             $detergentID->update([
//                 'ItemQty' => $detergent_quantity - 1,
//                 'RemainingVolume' => $remaining_volume + $detergent_volume - $detergent_use
//             ]);

//             $shop->update([
//                 'RemainingLoad' => $shop_rem_load - 1
//             ]);

//             tbl_shop_machine::where('ShopMachineID', $laundryMachineID)
//                 ->decrement('WasherQty');
//         } else {
//             $pending_stat->update([
//                 'Status' => $request['stat'],
//             ]);

//             $detergentID->update([
//                 'RemainingVolume' => $remaining_volume - $detergent_use
//             ]);

//             $shop->update([
//                 'RemainingLoad' => $shop_rem_load - 1
//             ]);

//             tbl_shop_machine::where('ShopMachineID', $laundryMachineID)
//                 ->decrement('WasherQty');
//         }

//         return response([
//             'status' => $machine_qty,
//             'message' => 'Request Accepted'
//         ]);
//     }
// }


    // public function process_booking_display(){
    //     $user = Auth::user(); 
    //     $userId = $user->id;

    //     $ownerId = tbl_owner::where('UserID', $userId)
    //                         ->pluck('OwnerID');

    //     $shopId = tbl_shop::where('OwnerID', $ownerId)
    //                       ->pluck('ShopID');

    //     $process = DB::table('tbl_bookings')
    //     ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
    //     ->join('tbl_laundry_preferences', 'tbl_bookings.LaundryPrefID', '=', 'tbl_laundry_preferences.LaundryPrefID')
    //     ->select(
    //         'tbl_customers.Name', 'tbl_customers.Name', 'tbl_customers.ContactNumber' , 'tbl_bookings.CustomerLoad',
    //         'tbl_laundry_preferences.WashingPref', 'tbl_laundry_preferences.DryingPref',
    //         'tbl_bookings.DateIssued', 'tbl_bookings.Status', 'tbl_bookings.BookingID',

    //          DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%h:%i %p') as TimeIssued"),
    //         'tbl_bookings.DateIssued',
    //         )
    //     ->where('tbl_bookings.Status', '>', '0')
    //     ->where('tbl_bookings.Status', '<=', '4')
    //     ->where('tbl_bookings.ShopID', $shopId)
    //     ->get();

    //     $process_count = DB::table('tbl_bookings')
    //     ->where('Status', '>', '0')
    //     ->where('Status', '<=', '4')
    //     ->where('ShopID', $shopId)
    //     ->count();


    //     return response([
    //         'process' => $process,
    //         'process_count' => $process_count
    //     ]);
    // }

//     public function process_booking_stat(Request $request, $id){
//         $apiKey = env('SEMAPHORE_API_KEY ');
//         $process_stat = tbl_booking::find($id);

//         $status = $process_stat->Status;
//         $bookingID = $process_stat->BookingID;

//         $laundryMachineID = DB::table('tbl_shops')
//         ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', 'tbl_shop_machines.ShopMachineID')
//         ->join('tbl_bookings', 'tbl_shops.ShopID', 'tbl_bookings.ShopID')
//         ->where('tbl_bookings.BookingID', $process_stat->BookingID)
//         ->value('tbl_shop_machines.ShopMachineID');

//         $machine_stat = tbl_shop_machine::find($laundryMachineID);
//             $machine_qty = $machine_stat->DryerQty;

//         if($status == '1'){
//             if($machine_qty <= 0){
//                 return response([
//                     'status' => '0',
//                     'message' => 'All dryers are occupied!'
//                 ]);
//                }
//                else{
//                 tbl_shop_machine::where('ShopMachineID', $laundryMachineID)
//            ->increment('WasherQty');

//            tbl_shop_machine::where('ShopMachineID', $laundryMachineID)
//            ->decrement('DryerQty');
//                }
//         }
//         else if($status == '2'){
//                tbl_shop_machine::where('ShopMachineID', $laundryMachineID)
//                ->increment('DryerQty');
//             }
//         else if($status == '3'){
//             $apiKey = env('SEMAPHORE_API_KEY');
//             $customerNumber = DB::table('tbl_bookings')
//         ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
//         ->where('tbl_bookings.BookingID', $bookingID)
//         ->value('tbl_customers.ContactNumber');
        
//         $ch = curl_init();

//         $parameters = array(
//     'apikey' => $apiKey,
//     'number' => '09655543516',
//     'message' => 'Good day, customer! This is to inform you that your laundry is ready for pickup.',
//     'sendername' => 'LAUNDRYMATE'
// );
// curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
// curl_setopt( $ch, CURLOPT_POST, 1 );

// curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );


// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
// $output = curl_exec( $ch );
// curl_close ($ch);
        
//         $process_stat->update([
//             'Status' => $request['stat'],
//         ]);
        
//         }

//         if(!$process_stat){
//             return response([
//                 'message' => 'Request not found'
//             ],403);
//         }

//             $process_stat->update([
//                 'Status' => $request['stat'],
//             ]);

           

//             return response([
//                 'status' => '$status',
//                 'message' => 'All dryers are occupied!'
//             ]);
       
//     }

    // public function finished_booking_display(){
    //     $user = Auth::user(); 
    //     $userId = $user->id;

    //     $ownerId = tbl_owner::where('UserID', $userId)
    //                         ->pluck('OwnerID');

    //     $shopId = tbl_shop::where('OwnerID', $ownerId)
    //                       ->pluck('ShopID');

    //     $finish = DB::table('tbl_bookings')
    //     ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
    //     ->join('tbl_laundry_preferences', 'tbl_bookings.LaundryPrefID', '=', 'tbl_laundry_preferences.LaundryPrefID')
    //     ->select(
    //         'tbl_customers.Name', 'tbl_customers.Name', 'tbl_customers.ContactNumber' , 'tbl_bookings.CustomerLoad',
    //         'tbl_laundry_preferences.WashingPref', 'tbl_laundry_preferences.DryingPref',
    //         'tbl_bookings.DateIssued', 'tbl_bookings.Status', 'tbl_bookings.BookingID',

    //          DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%h:%i %p') as TimeIssued"),
    //         'tbl_bookings.DateIssued'
    //         )
    //     ->where('tbl_bookings.Status', '=','5')
    //     ->where('tbl_bookings.ShopID', $shopId)
    //     ->get();

    //     $finish_count = DB::table('tbl_bookings')
    //     ->where('tbl_bookings.Status', '=', '5')
    //     ->where('tbl_bookings.ShopID', $shopId)
    //     ->count();


    //     return response([
    //         'finish' => $finish,
    //         'finish_count' => $finish_count
    //     ]);
    // }

    public function booking_rating_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');


        $total_rating = DB::table('tbl_ratings')
        ->where('ShopID', $shopId)
        ->sum('Rate');

        $total_raters = DB::table('tbl_ratings')
        ->where('ShopID', $shopId)
        ->count();

        $five_star = DB::table('tbl_ratings')
        ->where('Rate', '5')
        ->where('ShopID', $shopId)
        ->count();

        $four_star = DB::table('tbl_ratings')
        ->where('Rate', '4')
        ->where('ShopID', $shopId)
        ->count();

        $three_star = DB::table('tbl_ratings')
        ->where('Rate', '3')
        ->where('ShopID', $shopId)
        ->count();

        $two_star = DB::table('tbl_ratings')
        ->where('Rate', '2')
        ->where('ShopID', $shopId)
        ->count();
        
        $one_star = DB::table('tbl_ratings')
        ->where('Rate', '1')
        ->where('ShopID', $shopId)
        ->count();
        
        $ratings = DB::table('tbl_ratings')
        ->join('tbl_customers', 'tbl_ratings.CustomerID', 'tbl_customers.CustomerID')
        ->select('tbl_ratings.*','tbl_customers.*')
        ->where('tbl_ratings.ShopID', $shopId)
        ->orderBy('DateIssued', 'desc')
        ->get();

        return response([
            'ratings' => $ratings,
            'star_counts' => [
            'five_star' => $five_star,
            'four_star' => $four_star,
            'three_star' => $three_star,
            'two_star' => $two_star,
            'one_star' => $one_star
        ]
        ]);
    }

    public function booking_weekly_display(){
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $user = Auth::user(); 
        $userId = $user->id;

        $ownerId = tbl_owner::where('UserID', $userId)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');
        
        $results = DB::table('tbl_bookings')
            ->select(DB::raw('DAYNAME(DATE(Schedule)) as day'), DB::raw('COUNT(*) as daycount'))
            ->whereBetween('Schedule', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw('DAYNAME(DATE(Schedule))'))
            ->where('Status', '5')
            ->where('ShopID', $shopId)
            ->get();

        $mostLaundryDay = DB::table('tbl_bookings')
        ->select(DB::raw('COUNT(*) as daycount'), DB::raw('DAYNAME(DATE(Schedule)) as dayname'))
        ->whereBetween('Schedule', [$startOfWeek, $endOfWeek])
        ->where('Status', '5')
        ->where('ShopID', $shopId)
        ->groupBy(DB::raw('DAYNAME(DATE(Schedule))'))
        ->orderBy('daycount', 'desc')
        ->first();

        $avgLaundry = DB::table('tbl_bookings')
        ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
        ->select(DB::raw('SUM(tbl_bookings.CustomerLoad) as customerload'), DB::raw('COUNT(*) as totalbooks'))
        ->whereBetween('tbl_bookings.Schedule', [$startOfWeek, $endOfWeek])
        ->where('tbl_bookings.Status', '5')
        ->where('tbl_bookings.ShopID', $shopId)
        ->get();

            $mon = 0;
            $tue = 0;
            $wed = 0;
            $thu = 0;
            $fri = 0;
            $sat = 0;
            $sun = 0;

            foreach($results as $day){
                switch($day->day){
                    case 'Monday':
                        $mon = $day->daycount;
                        break;
                    case 'Tuesday':
                        $tue = $day->daycount;
                        break;
                    case 'Wednesday':
                        $wed = $day->daycount;
                        break;
                    case 'Thursday':
                        $thu = $day->daycount;
                        break;
                    case 'Friday':
                        $fri = $day->daycount;
                        break;
                    case 'Saturday':
                        $sat = $day->daycount;
                        break;
                    case 'Sunday':
                        $sun = $day->daycount;
                        break;
                }
            }


        
        return response([
            'monday' => $mon,
            'tuesday' => $tue,
            'wednesday' => $wed,
            'thursday' => $thu,
            'friday' => $fri,
            'saturday' => $sat,
            'sunday' => $sun,
            'mostday' => $mostLaundryDay,
            'total' => $avgLaundry

        ]);
    }

    public function booking_monthly_display(){
        $user = Auth::user(); 
        $userId = $user->id;

        $ownerId = tbl_owner::where('UserID', $userId)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $results = DB::table('tbl_bookings')
            ->select(DB::raw('MONTHNAME(DATE(Schedule)) as month'), DB::raw('COUNT(*) as monthcount'))
            ->groupBy(DB::raw('MONTHNAME(DATE(Schedule))'))
            ->where('Status', '5')
            ->where('ShopID', $shopId)
            ->get();

            $mostLaundryMonth = DB::table('tbl_bookings')
        ->select(DB::raw('COUNT(*) as monthcount'), DB::raw('MONTHNAME(DATE(Schedule)) as monthname'))
        ->where('Status', '5')
        ->where('ShopID', $shopId)
        ->groupBy(DB::raw('MONTHNAME(DATE(Schedule))'))
        ->orderBy('monthcount', 'desc')
        ->first();

        $avgLaundry = DB::table('tbl_bookings')
        ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
        ->select(DB::raw('SUM(tbl_bookings.CustomerLoad) as customerload'), DB::raw('COUNT(*) as totalbooks'))
        ->where('tbl_bookings.Status', '5')
        ->where('tbl_bookings.ShopID', $shopId)
        ->get();

            $jan = 0; $feb = 0; $mar = 0; $apr = 0;
            $may = 0; $jun = 0; $jul = 0; $aug = 0;
            $sep = 0; $oct = 0; $nov = 0; $dec = 0;

            foreach($results as $month){
                switch($month->month){
                    case 'January':
                        $jan = $month->monthcount;
                        break;
                    case 'February':
                        $feb = $month->monthcount;
                        break;
                    case 'March':
                        $mar = $month->monthcount;
                        break;
                    case 'April':
                        $apr = $month->monthcount;
                        break;
                    case 'May':
                        $may = $month->monthcount;
                        break;
                    case 'June':
                        $jun = $month->monthcount;
                        break;
                    case 'July':
                        $jul = $month->monthcount;
                        break;
                    case 'August':
                        $aug = $month->monthcount;
                        break;
                    case 'September':
                        $sep = $month->monthcount;
                        break;
                    case 'October':
                        $oct = $month->monthcount;
                        break;
                    case 'November':
                        $nov = $month->monthcount;
                        break;
                    case 'December':
                        $dec = $month->monthcount;
                        break;
                }
            }
        
        return response([
            'jan' => $jan,
            'feb' => $feb,
            'mar' => $mar,
            'apr' => $apr,
            'may' => $may,
            'jun' => $jun,
            'jul' => $jul,
            'aug' => $aug,
            'sep' => $sep,
            'oct' => $oct,
            'nov' => $nov,
            'dec' => $dec,
            'monthly' => $mostLaundryMonth,
            'total' => $avgLaundry
        ]);
    }

    public function sales_weekly_display()
{
    $user = Auth::user(); 
    $userContact = $user->contact;

    $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                        ->pluck('OwnerID');

    $shopId = tbl_shop::where('OwnerID', $ownerId)
                      ->pluck('ShopID');

    $startingWeek = Carbon::now()->startOfWeek();
    $endingWeek = Carbon::now()->endOfWeek();

    // Query for tbl_bookings
    $bookingSales = DB::table('tbl_bookings')
        ->select(DB::raw('DAYNAME(Schedule) as day, SUM(LoadCost) as made'))
        ->whereIn('ShopID', $shopId)
        ->where('PaymentStatus', 'paid')
        ->whereBetween('Schedule', [$startingWeek, $endingWeek])
        ->groupBy(DB::raw('DAYNAME(Schedule)'))
        ->pluck('made', 'day');

    // Query for tbl_walkins
    $walkinSales = DB::table('tbl_walkins')
        ->select(DB::raw('DAYNAME(DateIssued) as day, SUM(Total) as made'))
        ->whereIn('ShopID', $shopId)
        ->where('PaymentStatus', 'paid')
        ->whereBetween('DateIssued', [$startingWeek, $endingWeek])
        ->groupBy(DB::raw('DAYNAME(DateIssued)'))
        ->pluck('made', 'day');

    // Combine results from both queries
    $daysOfWeek = [
        'Monday' => 0,
        'Tuesday' => 0,
        'Wednesday' => 0,
        'Thursday' => 0,
        'Friday' => 0,
        'Saturday' => 0,
        'Sunday' => 0,
    ];

    // Sum the sales from both bookingSales and walkinSales
    foreach ($daysOfWeek as $day => $value) {
        $daysOfWeek[$day] = (isset($bookingSales[$day]) ? $bookingSales[$day] : 0) + 
                            (isset($walkinSales[$day]) ? $walkinSales[$day] : 0);
    }

    // Calculate total sales
    $totalSales = array_sum($daysOfWeek);

    return response([
        'monday' => $daysOfWeek['Monday'],
        'tuesday' => $daysOfWeek['Tuesday'],
        'wednesday' => $daysOfWeek['Wednesday'],
        'thursday' => $daysOfWeek['Thursday'],
        'friday' => $daysOfWeek['Friday'],
        'saturday' => $daysOfWeek['Saturday'],
        'sunday' => $daysOfWeek['Sunday'],
        'total' => $totalSales,
    ]);
}


    public function sales_monthly_display(){
        $user = Auth::user(); 
        $userId = $user->id;

        $ownerId = tbl_owner::where('UserID', $userId)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $startingMonth = Carbon::now()->startOfMonth();
        $endingMonth = Carbon::now()->endOfMonth();

        $sales = DB::table('tbl_billings')
        ->join('tbl_bookings', 'tbl_billings.BookingID', 'tbl_bookings.BookingID')
        ->select(DB::raw('MONTHNAME(DATE(tbl_billings.PaidIssued)) as month'), DB::raw('SUM(tbl_billings.Amount) as made'))
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_billings.PaymentStatus', 'paid')
        ->whereBetween('tbl_billings.PaidIssued', [$startingMonth, $endingMonth])
        ->groupBy(DB::raw('MONTHNAME(DATE(tbl_billings.PaidIssued))'))
        ->get();

        $minSales = DB::table('tbl_billings')
        ->join('tbl_bookings', 'tbl_billings.BookingID', 'tbl_bookings.BookingID')
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_billings.PaymentStatus', 'paid')
        ->whereBetween('tbl_billings.PaidIssued', [$startingMonth, $endingMonth])
        ->min('tbl_billings.Amount');

        $highSales = DB::table('tbl_billings')
    ->join('tbl_bookings', 'tbl_billings.BookingID', '=', 'tbl_bookings.BookingID')
    ->where('tbl_bookings.ShopID', $shopId)
    ->whereBetween('tbl_billings.PaidIssued', [$startingMonth, $endingMonth])
    ->groupBy(DB::raw('MONTHNAME(DATE(tbl_billings.PaidIssued))'))
    ->orderBy(DB::raw('MONTHNAME(DATE(tbl_billings.PaidIssued))'), 'asc')
    ->sum('tbl_billings.Amount');


        $totalSales = DB::table('tbl_billings')
        ->join('tbl_bookings', 'tbl_billings.BookingID', 'tbl_bookings.BookingID')
        ->where('tbl_bookings.ShopID', $shopId)
        ->whereBetween('tbl_billings.PaidIssued', [$startingMonth, $endingMonth])
        ->sum('tbl_billings.Amount');

        $jan = 0; $feb = 0; $mar = 0; $apr = 0;
        $may = 0; $jun = 0; $jul = 0; $aug = 0;
        $sep = 0; $oct = 0; $nov = 0; $dec = 0;

        foreach($sales as $month){
            switch($month->month){
                case 'January':
                    $jan = $month->made;
                    break;
                case 'February':
                    $feb = $month->made;
                    break;
                case 'March':
                    $mar = $month->made;
                    break;
                case 'April':
                    $apr = $month->made;
                    break;
                case 'May':
                    $may = $month->made;
                    break;
                case 'June':
                    $jun = $month->made;
                    break;
                case 'July':
                    $jul = $month->made;
                    break;
                case 'August':
                    $aug = $month->made;
                    break;
                case 'September':
                    $sep = $month->made;
                    break;
                case 'October':
                    $oct = $month->made;
                    break;
                case 'November':
                    $nov = $month->made;
                    break;
                case 'December':
                    $dec = $month->made;
                    break;
            }
        }


        
        return response([
            'jan' => $jan,
            'feb' => $feb,
            'mar' => $mar,
            'apr' => $apr,
            'may' => $may,
            'jun' => $jun,
            'jul' => $jul,
            'aug' => $aug,
            'sep' => $sep,
            'oct' => $oct,
            'nov' => $nov,
            'dec' => $dec,
            'min' => $minSales,
            'high' => $highSales,
            'total' => $totalSales
        ]);
    }

    public function home_display(){ 
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');

        $revenueBook = DB::table('tbl_bookings')
        ->where('ShopID', $shopId)
        ->where('PaymentStatus', 'paid')
        ->where(DB::raw("DATE_FORMAT(Schedule, '%Y-%m-%d')"), $now)
        ->sum('LoadCost');

        $revenueWalk = DB::table('tbl_walkins')
        ->where('ShopID', $shopId)
        ->where('PaymentStatus', 'paid')
        ->where(DB::raw("DATE_FORMAT(DateIssued, '%Y-%m-%d')"), $now)
        ->sum('Total');
        

        $booked = DB::table('tbl_bookings')
        ->selectRaw('DATE(Schedule) as date')
        ->where('ShopID', $shopId)
        ->whereDate('Schedule', $now)
        ->count();

        $walked = DB::table('tbl_walkins')
        ->selectRaw('DATE(DateIssued) as date')
        ->where('ShopID', $shopId)
        ->whereDate('DateIssued', $now)
        ->count();

        $washBook = DB::table('tbl_bookings')
        ->selectRaw('DATE(Schedule) as date')
        ->where('ShopID', $shopId)
        ->whereDate('Schedule', $now)
        ->where('Status', '1')
        ->count();

        $washWalk = DB::table('tbl_walkins')
        ->selectRaw('DATE(DateIssued) as date')
        ->where('ShopID', $shopId)
        ->whereDate('DateIssued', $now)
        ->where('Status', '1')
        ->count();

        $dryBook = DB::table('tbl_bookings')
        ->selectRaw('DATE(Schedule) as date')
        ->where('ShopID', $shopId)
        ->whereDate('Schedule', $now)
        ->where('Status', '2')
        ->count();

        $dryWalk = DB::table('tbl_walkins')
        ->selectRaw('DATE(DateIssued) as date')
        ->where('ShopID', $shopId)
        ->whereDate('DateIssued', $now)
        ->where('Status', '2')
        ->count();

        $foldBook = DB::table('tbl_bookings')
        ->selectRaw('DATE(Schedule) as date')
        ->where('ShopID', $shopId)
        ->whereDate('Schedule', $now)
        ->where('Status', '3')
        ->count();

        $foldWalk = DB::table('tbl_walkins')
        ->selectRaw('DATE(DateIssued) as date')
        ->where('ShopID', $shopId)
        ->whereDate('DateIssued', $now)
        ->where('Status', '3')
        ->count();

        $pickBook = DB::table('tbl_bookings')
        ->selectRaw('DATE(Schedule) as date')
        ->where('ShopID', $shopId)
        ->whereDate('Schedule', $now)
        ->where('Status', '4')
        ->count();

        $pickWalk = DB::table('tbl_walkins')
        ->selectRaw('DATE(DateIssued) as date')
        ->where('ShopID', $shopId)
        ->whereDate('DateIssued', $now)
        ->where('Status', '4')
        ->count();

        $completeBook = DB::table('tbl_bookings')
        ->selectRaw('DATE(Schedule) as date')
        ->where('ShopID', $shopId)
        ->whereDate('Schedule', $now)
        ->where('Status', '5')
        ->count();

        $completeWalk = DB::table('tbl_walkins')
        ->selectRaw('DATE(DateIssued) as date')
        ->where('ShopID', $shopId)
        ->whereDate('DateIssued', $now)
        ->where('Status', '5')
        ->count();

        $revenue = $revenueBook + $revenueWalk;
        $pendings = $booked + $walked;
        $wash = $washBook + $washWalk;
        $dry = $dryBook + $dryWalk;
        $fold = $foldBook + $foldWalk;
        $pick = $pickBook + $pickWalk;
        $complete = $completeBook + $completeWalk;
        

        return response([
            'today' => $now,
            'revenue' => $revenue,
            'bookings' => $pendings,
            'wash' => $wash,
            'dry' => $dry,
            'fold' => $fold,
            'pick' => $pick,
            'complete' => $complete,
        ]);
    }

    public function appbar_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $shopName = DB::table('tbl_shops')
        ->where('ShopID', $shopId)
        ->value('ShopName');

        $image = DB::table('tbl_owners')
        ->where('OwnerID', $ownerId)
        ->value('OwnerImage');


        return response([
            'shopname' => $shopName,
            'pic' => $image
        ]);
    }

    public function settings_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $settingInfo = DB::table('tbl_shops')
        ->join('tbl_owners', 'tbl_shops.OwnerID', 'tbl_owners.OwnerID')
        ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', 'tbl_shop_machines.ShopMachineID')
        ->where('tbl_shops.ShopID', $shopId)
        ->get();

        $laundryService = tbl_laundry_service::where('ShopID', $shopId)->get();

        return response([
            'info' => $settingInfo,
            'service' =>$laundryService
        ]);
    }

    public function owner_user_update(Request $request, $id){
        $user = Auth::user(); 
        $userId = $user->id;

        $owners = tbl_owner::find($id);

        $user = User::find($userId);

        if(!$owners){
            return response([
                'message' => 'User not found'
            ],403);
        }

        $owner = $request->validate([
            'name' => 'required',
            'sex' => 'required',
            'address' => 'required',
            'image' => 'nullable'
        ]);

        if ($request->has('image')) {
            $imageData = $owner['image'];
            
            // Ensure it's base64 encoded before decoding
            if (strpos($imageData, 'base64,') !== false) {
                $imageData = explode('base64,', $imageData)[1];
            }
    
            $decodedImage = base64_decode($imageData, true);
    
            if ($decodedImage === false) {
                return response([
                    'message' => 'Invalid image data'
                ], 422);
            }
    
            $imageName = time() . '.jpg';
            Storage::disk('public')->put('images/' . $imageName, $decodedImage);
        } else {
            $imageName = $owners->OwnerImage; // Keep the existing image if no new image is provided
        }

        $owners->update([
            'OwnerName' => $owner['name'],
            'OwnerSex' => $owner['sex'],
            'OwnerAddress' => $owner['address'],
            'OwnerImage' => $imageName
        ]);

        $user->update([
            'contact' => $request['contact']
        ]);

        return response([
            'response' => 'success'
        ], 200);
    }

    public function shop_update(Request $request, $id){
        $shop = tbl_shop::find($id);

        if(!$shop){
            return response([
                'message' => 'User not found'
            ],403);
        }

        $user = $request->validate([
            'name' => 'required',
            'sex' => 'required',
            'address' => 'required',
            'contact' => 'required|integer',
            'image' => 'sometimes'
        ]);

        $shop->update([
            'Name' => $user['name'],
            'Sex' => $user['sex'],
            'Address' => $user['address'],
            'ContactNumber' => $user['contact'],
            'Image' => base64_decode($user['image'])
        ]);

        return response([
            'response' => 'success'
        ], 200);
    }

    public function walk_in(Request $request){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

        $machineId = tbl_shop::where('ShopID', $shopId)
                            ->value('ShopMachineID');                  

        $dateTime = Carbon::now();

        $total = DB::table('tbl_walkins')
                 ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
                 ->value('tbl_laundry_services.LoadPrice');
        
        $washer = tbl_shop_machine::where('ShopMachineID', $machineId);
        $washerQty = $washer->value('WasherQty');

        $shop = tbl_shop::where('ShopID', $shopId);
        $todayBooks = $shop->value('RemainingLoad');

        $item = tbl_inventory::where('InventoryID', $request['item']);
        $itemName = $item->value('ItemName');
        $itemQty = $item->value('ItemQty');
        $itemVol = $item->value('ItemVolume');
        $remVol = $item->value('RemainingVolume');
        $volUse = $item->value('VolumeUse');

        $walkin = [
            'ContactNumber' => $request['contact'],
            'WalkinLoad' => $request['load'],
            'DateIssued' => $dateTime,
            'ServiceID' => $request['service'],
            'Total' => $request['load'] * $total,
            'ShopID' => $shopId
        ];

        if($todayBooks > 0){ 
               
                $shop->decrement('RemainingLoad');
                tbl_walkin::create($walkin);

                return response([
                    'message' => 'Request has been accepted'
                ],201);  
        }else{
            return response([
                'message' => 'No available slots for today'
            ],409);
        }
    }

    public function walk_in_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->value('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

        $today = Carbon::now()->toDateString();

        $walkin = DB::table('tbl_walkins')
        ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
        ->where('tbl_walkins.ShopID', $shopId)
        ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
        ->get();

        return response([
            'walkin' => $walkin,
        ]);
    }

    public function walkin_status(Request $request, $id){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

        $machineId = tbl_shop::where('ShopID', $shopId)
        ->value('ShopMachineID');                  
        
        $washer = tbl_shop_machine::where('ShopMachineID', $machineId);
        $washerQty = $washer->value('WasherQty');

        $walkin = tbl_walkin::find($id);
        $shop = tbl_shop::where('ShopID', $shopId);
        $todayBooks = $shop->value('RemainingLoad');

        if(!$walkin){
            return response([
                'message'=> 'not found'
            ]);
        }

        if($washerQty == '0'){
            return response([
                'message' => 'Washing Machines are currently occupied'
            ]);
        }else{
            if($request['stat'] == '1'){
                $washer->decrement('WasherQty');
                $walkin->update([
                    'Status' => '1'
                ]);
                return response([
                    'message' => 'Request has been accepted'
                ]);
            }else{
                $walkin->delete();
                $shop->increment('RemainingLoad');
                return response([
                    'message' => 'Request has been canceled'
                ]);
            }
        }

        
    }

    public function report_display(Request $request){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->value('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

                          

        if($request['type'] == 'Booked' || empty($request['type'])){
                if(empty($request['start']) && empty($request['end']) && empty($request['stat'])){
                    $response = DB::table('tbl_bookings')
            ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
            ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
            ->select('tbl_bookings.*', 'tbl_customers.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"))
            ->where('tbl_bookings.ShopID', $shopId)
            ->orderBy('tbl_bookings.Schedule', 'desc')
            ->get();
                }
                else if(!empty($request['stat'])){
                    $response = DB::table('tbl_bookings')
            ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
            ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
            ->select('tbl_bookings.*', 'tbl_customers.*', 'tbl_laundry_services.*', 
            DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d')"))
            ->where('tbl_bookings.Status', $request['stat'])
            ->where('tbl_bookings.ShopID', $shopId)
            ->orderBy('tbl_bookings.Schedule', 'desc')
            ->get();
                }else if(!empty($request['start']) && !empty($request['end'])){
                    $response = DB::table('tbl_bookings')
                        ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
                        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
                        ->select('tbl_bookings.*', 'tbl_customers.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"))
                        ->whereBetween(DB::raw('DATE(tbl_bookings.Schedule)'), [$request['start'], $request['end']])
                        ->where('tbl_bookings.ShopID', $shopId)
                        ->orderBy('tbl_bookings.Schedule', 'desc')
                        ->get();
                }else{
                    $response = DB::table('tbl_bookings')
                        ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
                        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
                        ->select('tbl_bookings.*', 'tbl_customers.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"))
                        ->whereBetween(DB::raw('DATE(tbl_bookings.Schedule)'), [$request['start'], $request['end']])
                        ->where('tbl_bookings.Status', $request['stat'])
                        ->where('tbl_bookings.ShopID', $shopId)
                        ->orderBy('tbl_bookings.Schedule', 'desc')
                        ->get();
                }

        }else if($request['type'] == 'Walkin'){
            if(empty($request['start']) && empty($request['end']) && empty($request['stat'])){
                $response = DB::table('tbl_walkins')
            ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
            ->select('tbl_walkins.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
            ->where('tbl_walkins.ShopID', $shopId)
            ->where('tbl_walkins.deleted_at', null)
            ->orderBy('tbl_walkins.DateIssued', 'desc')
            ->get();
            }
            else if(!empty($request['stat'])){
                $response = DB::table('tbl_walkins')
            ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
            ->select('tbl_walkins.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
            ->where('tbl_walkins.ShopID', $shopId)
            ->where('tbl_walkins.Status', $request['stat'])
            ->where('tbl_walkins.deleted_at', null)
            ->orderBy('tbl_walkins.DateIssued', 'desc')
            ->get();
            }else if(!empty($request['start']) && !empty($request['end'])){
                $response = DB::table('tbl_walkins')
            ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
            ->select('tbl_walkins.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
            ->whereBetween(DB::raw('DATE(tbl_walkins.DateIssued)'), [$request['start'], $request['end']])
            ->where('tbl_walkins.ShopID', $shopId)
            ->where('tbl_walkins.deleted_at', null)
            ->orderBy('tbl_walkins.DateIssued', 'desc')
            ->get();
            }else{
                $response = DB::table('tbl_walkins')
            ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
            ->select('tbl_walkins.*', 'tbl_laundry_services.*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
            ->whereBetween(DB::raw('DATE(tbl_walkins.DateIssued)'), [$request['start'], $request['end']])
            ->where('tbl_wakins.Status', $request['stat'])
            ->where('tbl_walkins.ShopID', $shopId)
            ->where('tbl_walkins.deleted_at', null)
            ->orderBy('tbl_walkins.DateIssued', 'desc')
            ->get();

            }
        
        }

        $bookRev = tbl_booking::where('ShopID', $shopId)
        ->where('PaymentStatus','paid')
        ->sum('LoadCost');

        $walkRev = tbl_walkin::where('ShopID', $shopId)
        ->where('PaymentStatus', 'paid')
        ->sum('Total');

        $accuRev = $bookRev + $walkRev;


        return response([
            'data' => $response,
            'revenue' => $accuRev
        ], 200);
    }

    public function customer_display(){
        $customers = tbl_customer::get();

        return response([
            'message'=> $customers
        ]);
    }

    public function registered(Request $request){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->value('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->value('ShopID');

        $item = tbl_inventory::where('InventoryID', $request['item']);
        $itemName = $item->value('ItemName');
        $itemQty = $item->value('ItemQty');
        $itemVol = $item->value('ItemVolume');
        $remVol = $item->value('RemainingVolume');
        $volUse = $item->value('VolumeUse');
                  

        tbl_booking::create([
            'CustomerLoad' => $request['load'],
            'LoadCost' => $request['load'] * 10,
            'Schedule' => $request['sched'],
            'DateIssued' => Carbon::now(),
            'CustomerID' => $request['customerId'],
            'ShopID' => $shopId,
            'ServiceID' => $request['serviceId']
        ]);

        return response([
            'message' => 'Booked successfully'
        ],201);
        
    }

    public function booking_status(Request $request, $id){
        $booking = tbl_booking::find($id);

        if(!$booking){
            return response([
                'message'=> 'not found'
            ]);
        }

        if($request['stat'] == '1'){
            $booking->update([
                'Status' => '1'
            ]);
            return response([
                'message' => 'Request has been accepted'
            ]);
        }else{
            $booking->delete();
            return response([
                'message' => 'Request has been canceled'
            ]);
        }
    }

    public function wash_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');
        $today = Carbon::now()->toDateString();

        $bookings = DB::table('tbl_bookings')
        ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('tbl_customers.*','tbl_laundry_services.*', 'tbl_bookings.*',
             DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"),
            )
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_bookings.Status', '1')
        ->whereDate('tbl_bookings.Schedule', $now)
        ->get();

         $walkin = DB::table('tbl_walkins')
        ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
        ->where('tbl_walkins.ShopID', $shopId)
        ->where('tbl_walkins.Status', '1')
        ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
        ->get();

        return response([
            'bookings' => $bookings,
            'walkin' => $walkin
        ]);
    }

    public function dry_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');
        $today = Carbon::now()->toDateString();

        $bookings = DB::table('tbl_bookings')
        ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('tbl_customers.*','tbl_laundry_services.*', 'tbl_bookings.*',
             DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"),
            )
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_bookings.Status', '2')
        ->whereDate('tbl_bookings.Schedule', $now)
        ->get();

         $walkin = DB::table('tbl_walkins')
        ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
        ->where('tbl_walkins.ShopID', $shopId)
        ->where('tbl_walkins.Status', '2')
        ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
        ->get();

        return response([
            'bookings' => $bookings,
            'walkin' => $walkin
        ]);
    }

    public function fold_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');
        $today = Carbon::now()->toDateString();

        $bookings = DB::table('tbl_bookings')
        ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('tbl_customers.*','tbl_laundry_services.*', 'tbl_bookings.*',
             DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"),
            )
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_bookings.Status', '3')
        ->whereDate('tbl_bookings.Schedule', $now)
        ->get();

         $walkin = DB::table('tbl_walkins')
        ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
        ->where('tbl_walkins.ShopID', $shopId)
        ->where('tbl_walkins.Status', '3')
        ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
        ->get();

        return response([
            'bookings' => $bookings,
            'walkin' => $walkin
        ]);
    }

    public function pickup_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');
        $today = Carbon::now()->toDateString();

        $bookings = DB::table('tbl_bookings')
        ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('tbl_customers.*','tbl_laundry_services.*', 'tbl_bookings.*',
             DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"),
            )
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_bookings.Status', '4')
        ->whereDate('tbl_bookings.Schedule', $now)
        ->get();

         $walkin = DB::table('tbl_walkins')
        ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
        ->where('tbl_walkins.ShopID', $shopId)
        ->where('tbl_walkins.Status', '4')
        ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
        ->get();

        return response([
            'bookings' => $bookings,
            'walkin' => $walkin
        ]);
    }

    public function complete_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');

        $now = Carbon::today()->format('Y-m-d');
        $today = Carbon::now()->toDateString();

        $bookings = DB::table('tbl_bookings')
        ->join('tbl_customers', 'tbl_bookings.CustomerID', '=', 'tbl_customers.CustomerID')
        ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('tbl_customers.*','tbl_laundry_services.*', 'tbl_bookings.*',
             DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d') as Schedule"),
            )
        ->where('tbl_bookings.ShopID', $shopId)
        ->where('tbl_bookings.Status', '5')
        ->whereDate('tbl_bookings.Schedule', $now)
        ->get();

         $walkin = DB::table('tbl_walkins')
        ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', 'tbl_laundry_services.ServiceID')
        ->select('*', DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d') as DateIssued"))
        ->where('tbl_walkins.ShopID', $shopId)
        ->where('tbl_walkins.Status', '5')
        ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
        ->get();

        return response([
            'bookings' => $bookings,
            'walkin' => $walkin
        ]);
    }

    public function payment_update(Request $request,$id){
        $booking = tbl_booking::find($id);
        $walkin = tbl_walkin::find($id);
        if($request['type'] == 'booking'){
            $booking->update([
                'PaymentStatus' => 'paid'
            ]);
            return response([
                'message' => 'Marked as Paid'
            ],201);
        }else{
            $walkin->update([
                'PaymentStatus' => 'paid'
            ]);
            return response([
                'message' => 'Marked as Paid'
            ],201);
        }

        
    }

    public function complete_update(Request $request, $id){
        $booking = tbl_booking::find($id);
        $walkin = tbl_walkin::find($id);
        if($request['type'] == 'booking'){
            if($request['paid'] == 'paid'){
                $booking->update([
                    'Status' => '5'
                ]);
                return response([
                    'message' => 'Marked as Completed'
                ],201);
            }else{
                $booking->update([
                    'PaymentStatus' => 'paid',
                    'Status' => '5'
                ]);
                return response([
                    'message' => 'Marked as Paid & Completed'
                ],201);
            }
        }else{
            if($request['paid'] == 'paid'){
                $walkin->update([
                    'Status' => '5'
                ]);
                return response([
                    'message' => 'Marked as Completed'
                ],201);
            }else{
                $walkin->update([
                    'PaymentStatus' => 'paid',
                    'Status' => '5'
                ]);
                return response([
                    'message' => 'Marked as Paid & Completed'
                ],201);
            }
        }
    }

    public function shop_info_update(Request $request){
        $user = Auth::user();
        $userContact = $user->contact;

        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
        ->value('OwnerID');

        $shopId = tbl_shop::where('OwnerID', $ownerId)
        ->value('ShopID');

        $shop = tbl_shop::find($shopId);

        $service1 = tbl_laundry_service::where('ServiceID', $request['lightid'])->first();
        $service2 = tbl_laundry_service::where('ServiceID', $request['heavyid'])->first();
        $service3 = tbl_laundry_service::where('ServiceID', $request['comforterid'])->first();

        $shop->update([
            'ShopName' => $request['shopname'],
            'ShopAddress' => $request['shopadd'],
            'WorkDay' => $request['workday'],
            'WorkHour' => $request['workhour'],
        ]);

        $service1->update([
            'LoadWeight' => $request['lightload'],
            'LoadPrice' => $request['lightprice']
        ]);

        $service2->update([
            'LoadWeight' => $request['heavyload'],
            'LoadPrice' => $request['heavyprice']
        ]);

        $service3->update([
            'LoadWeight' => $request['comforterload'],
            'LoadPrice' => $request['comforterprice']
        ]);

        return response([
            'message' => 'Shop Information has been updated'
        ]);
    }
}