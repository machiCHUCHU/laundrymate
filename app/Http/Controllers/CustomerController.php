<?php

namespace App\Http\Controllers;

use App\Models\tbl_added_shop;
use App\Models\tbl_booking;
use App\Models\tbl_laundry_service;
use App\Models\tbl_notif;
use App\Models\tbl_rating;
use App\Models\tbl_shop;
use Illuminate\Http\Request;
use App\Models\tbl_customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use PHPUnit\Framework\Constraint\IsEmpty;
class CustomerController extends Controller
{
   public function added_shops_display(){
        $user = Auth::user(); 
        $userContact = $user->contact;

        $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
        ->value('CustomerID');

        $added_shops = DB::table('tbl_shops')
        
        ->get();

        return response([
            'shop_request' => $added_shops
        ]);
   }

   public function request_shops(Request $request){
    $user = Auth::user(); 
        $userContact = $user->contact;

        $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
        ->value('CustomerID');

        $shopId = tbl_shop::where('ShopCode', $request['code'])
        ->value('ShopID');

        $addShop = tbl_added_shop::where('ShopID', $shopId)
        ->where('CustomerID', $customerId)
        ->first();

        $dateToday = Carbon::now()->toDateString();

        if(!$shopId){
            return response([
                'message' => 'Shop not found'
            ],404);
        }else{
            if(!$addShop){
                tbl_added_shop::insert([
                    'Date'=> $dateToday,
                    'ShopID'=> $shopId,
                    'CustomerID'=> $customerId
                ]);

                return response([
                    'message'=> 'Request has been sent'
                ],200);
            }else{
                return response([
                    'message' => 'Shop already been listed'
                ],409);
            }
              
        }

   }

   public function shop_unfollow($addshopid){
    $addedShop = tbl_added_shop::find($addshopid);

    $addedShop->update([
        'IsValued' => '4'
    ]);

    return response([
        'message' => 'You\'ve unfollowed the shop' 
    ]);
   }

   public function request_shops_display(Request $request){
    $user = Auth::user(); 
        $userContact = $user->contact;

        $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
        ->value('CustomerID');

        $shopInfo = DB::table('tbl_shops')
        ->where('tbl_shops.ShopID', $request['shopid'])
        ->get();

        $laundryService = tbl_laundry_service::where('ShopID', $request['shopid'])
        ->get();

        $ratings = DB::table('tbl_ratings')
        ->join('tbl_customers', 'tbl_ratings.CustomerID', 'tbl_customers.CustomerID')
        ->where('tbl_ratings.ShopID', $request['shopid'])
        ->get();

        $ratingSum = tbl_rating::where('ShopID', $request['shopid'])
        ->sum('Rate');

        $ratingCount = tbl_rating::where('ShopID', $request['shopid'])
        ->count();

        $isValued = DB::table('tbl_added_shops')
        ->where('ShopID', $request['shopid'])
        ->where('CustomerID', $customerId)
        ->value('IsValued');

        if($isValued == '1'){
            $message = 'valued';
        }elseif($isValued == '0'){
            $message = 'Request hasn\'t been approved yet';
        }else{
            $message = 'Request has been denied';
        }
       

        return response([
            'shop'=> $shopInfo,
            'service'=> $laundryService,
            'ratings'=> $ratings,
            'rateSum'=> (int)$ratingSum,
            'rateCount'=> $ratingCount,
            'message' => $isValued,
        ]);
   }

   public function laundry_display(Request $request){
    $user = Auth::user();
    $userContact = $user->contact;

    $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
    ->value('CustomerID');

    if($request['nav'] == null || $request['nav'] == '0'){
        $bookings = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
    ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
    ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
    ->select('tbl_shops.*', 'tbl_bookings.*', 'tbl_laundry_services.*', 'tbl_customers.*', DB::raw("DATE_FORMAT(Schedule, '%Y-%m-%d') as Schedule"))
    ->where('tbl_bookings.CustomerID', $customerId)
    ->orderBy('tbl_bookings.BookingID', 'desc')
    ->get();
    }elseif($request['nav'] == '1'){
        $bookings = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
    ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
    ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
    ->select('tbl_shops.*', 'tbl_bookings.*', 'tbl_laundry_services.*', 'tbl_customers.*', DB::raw("DATE_FORMAT(Schedule, '%Y-%m-%d') as Schedule"))
    ->where('tbl_bookings.CustomerID', $customerId)
    ->where('tbl_bookings.Status', '4')
    ->orderBy('tbl_bookings.BookingID', 'desc')
    ->get();
    }elseif($request['nav'] == '2'){
        $bookings = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
    ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
    ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
    ->select('tbl_shops.*', 'tbl_bookings.*', 'tbl_laundry_services.*', 'tbl_customers.*', DB::raw("DATE_FORMAT(Schedule, '%Y-%m-%d') as Schedule"))
    ->where('tbl_bookings.CustomerID', $customerId)
    ->whereIn('tbl_bookings.Status', ['5', '6'])
    ->orderBy('tbl_bookings.BookingID', 'desc')
    ->get();
    }else{
        $bookings = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
    ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
    ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
    ->select('tbl_shops.*', 'tbl_bookings.*', 'tbl_laundry_services.*', 'tbl_customers.*', DB::raw("DATE_FORMAT(Schedule, '%Y-%m-%d') as Schedule"))
    ->where('tbl_bookings.CustomerID', $customerId)
    ->whereNot('tbl_bookings.deleted_at', null)
    ->orderBy('tbl_bookings.BookingID', 'desc')
    ->get();
    }

    return response([
        'bookings' => $bookings
    ]);
   }

   public function summary_display($id){
    $user = Auth::user();
    $userContact = $user->contact;

    $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
    ->value('CustomerID');

    $bookings = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
    ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', 'tbl_laundry_services.ServiceID')
    ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
    ->join('tbl_owners', 'tbl_shops.OwnerID', 'tbl_owners.OwnerID')
    ->select('tbl_shops.*', 'tbl_bookings.*', 'tbl_laundry_services.*', 
    'tbl_customers.*', 'tbl_owners.*', DB::raw("DATE_FORMAT(Schedule, '%Y-%m-%d') as Schedule"))
    ->where('tbl_bookings.CustomerID', $customerId)
    ->where('tbl_bookings.BookingID', $id)
    ->get();

    return response([
        'summary'=> $bookings
    ]);
   }

   public function laundry_cancellation($bookId){
    $book = tbl_booking::find($bookId);

    if(!$book){
        return response([
            'message' => 'Booking not found'
        ],409);
    }else{
        $book->delete();

        tbl_shop::increment('RemainingLoad');

        return response([
            'message' => 'Service has been cancelled'
        ]);
    }
   }

   public function laundry_completion($bookId){
    $book = tbl_booking::find($bookId);
    $notif = tbl_notif::where('BookingID', $bookId)->first();
    $paymentStat = tbl_booking::where('BookingID', $bookId)->value('PaymentStatus');

    $shopName = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
    ->where('tbl_bookings.BookingID', $bookId)
    ->value('tbl_shops.ShopName');
    

    if(!$book){
        return response([
            'message' => 'Booking not found'
        ],409);
    }else{
        if($paymentStat == 'paid'){
            $book->update([
                'Status' => '5',
            ]);

            $notif->update([
                'Title' => 'Laundry Service Completed',
                'Message' => 'Your Laundry Service from '.$shopName.'is Completed.',
                'is_read' => '0'
            ]);
    
            return response([
                'message' => 'Service Completed'
            ]);
        }else{
            $book->update([
                'Status' => '5',
                'PaymentStatus' => 'paid'
            ]);

            tbl_notif::update([
                'Title' => 'Laundry Service Completed',
                'Message' => 'Your Laundry Service from '.$shopName.'is Completed.',
                'is_read' => '0'
            ]);
    
            return response([
                'message' => 'Service Completed'
            ]);
        }
        
    }
   }

   public function submit_rating(Request $request){
    $user = Auth::user();
    $userContact = $user->contact;

    $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
    ->value('CustomerID');

    $dateToday = Carbon::now()->toDateString();

    tbl_booking::where('BookingID', $request['bookid'])->update([
        'Status' => '6'
    ]);

    tbl_rating::insert([
        'Rate' => $request['rate'],
        'Comment' => $request['comment'],
        'DateIssued' => $dateToday,
        'BookingID' => $request['bookid'],
        'ShopID' => $request['shopid'],
        'CustomerID' => $customerId
    ]);

    return response([
        'message' => 'Review has been submitted'
    ]);
   }

   public function view_rating(Request $request){
    $user = Auth::user();
    $customerContact = $user->contact;

    $customerId = tbl_customer::where('CustomerContactNumber', $customerContact)
    ->value('CustomerID');

    $ratingId = tbl_rating::where('BookingID', $request['bookid'])
    ->value('RatingID');

    $review = DB::table('tbl_ratings')
    ->join('tbl_customers', 'tbl_ratings.CustomerID', 'tbl_customers.CustomerID')
    ->where('tbl_ratings.RatingID', $ratingId)
    ->get();

    return response([
        'review' => $review
    ]);
   }

   public function laundry_service($shopid){
    $service = tbl_laundry_service::where('ShopID', $shopid)->get();

    return response([
        'service' => $service
    ]);
   }

   public function avail_laundry_service(Request $request){
    $user = Auth::user();
    $userContact = $user->contact;

    $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
    ->value('CustomerID');

    $shop = DB::table('tbl_shops')
    ->select('MaxLoad')
    ->where('ShopID', $request['shopid'])
    ->first();

    $bookingCount = tbl_booking::where('ShopID', $request['shopid'])
                    ->whereDate('Schedule', $request['sched'])
                    ->count();

    $dateToday = Carbon::now()->toDateString();

    

    if($shop->MaxLoad == $bookingCount){
        return response([
            'message' => 'The selected date is fully booked. Please choose another date.',
        ],409);
    }else{
        tbl_booking::insert([
            'CustomerLoad' => $request['load'],
            'LoadCost' => $request['cost'],
            'Schedule' => $request['sched'],
            'DateIssued' => $dateToday,
            'CustomerID' => $customerId,
            'ShopID' => $request['shopid'],
            'ServiceID' => $request['serviceid']
        ]);

        if($request['sched'] == $dateToday){
            tbl_shop::where('ShopID', $request['shopid'])->decrement('RemainingLoad');
        }
    
        return response([
            'message' => 'Service request has been sent' 
        ]);
    }


   
   }

   public function laundry_notification(){
    $user = Auth::user();
    $userContact = $user->contact;

    $customerId = tbl_customer::where('CustomerContactNumber', $userContact)
    ->value('CustomerID');

    $bookingId = tbl_booking::where('CustomerID', $customerId)
    ->value('BookingID');

    $notif = tbl_notif::where('CustomerID', $customerId)
    
    ->get();

    return response([
        'notif' => $notif,
    ]);
   }

   public function laundry_notification_read($notifid){
    $notif = tbl_notif::find($notifid);

    $notif->update([
        'is_read' => '1'
    ]);

    return response([
        'message' => 'success'
    ]);
   }

   public function customer_profile(){
    $user = Auth::user();
    $userContact = $user->contact;

    $customerInfo = tbl_customer::where('CustomerContactNumber', $userContact)
    ->get();

    return response([
        'customer' => $customerInfo
    ]);
   }

   public function customer_profile_update(Request $request){
    $user = Auth::user();
    $userContact = $user->contact;

    $customer = tbl_customer::where('CustomerContactNumber', $userContact)->first();

    $usersInfo = User::find($userContact)->first();

    $usersInfo->update([
        'contact' => $request['contact']
    ]);

    $customer->update([
        'CustomerName' => $request['customername'],
        'CustomerSex' => $request['customersex'],
        'CustomerAddress' => $request['customeraddress'],
        'CustomerImage' => $request['customerimage']
    ]);
   }

   public function owner_user_update(Request $request, $id){
    $user = Auth::user(); 
    $userId = $user->id;

    $customers = tbl_customer::find($id);

    $user = User::find($userId);

    if(!$customers){
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

    $imageData = $owner['image'];
    if ($request->has('image')) {
       if(strpos($imageData, '.jpg') !== false){
        $imageName = $customers->CustomerImage;
       }else{
        $image = base64_decode($imageData);
    $imageName = uniqid() . '.jpg';
    Storage::disk('public')->put('images/' . $imageName, $image);
       }
        
    } else {
        $imageName = $customers->OwnerImage; // Keep the existing image if no new image is provided
    }

    $customers->update([
        'CustomerName' => $owner['name'],
        'CustomerSex' => $owner['sex'],
        'CustomerAddress' => $owner['address'],
        'CustomerImage' => $imageName
    ]);

    $user->update([
        'contact' => $request['contact']
    ]);

    return response([
        'response' => 'success'
    ], 200);
}

public function customer_user_update(Request $request, $id){
        $user = Auth::user(); 
        $userId = $user->id;

        $owners = tbl_customer::find($id);

        $user = User::find($userId);

        if(!$owners){
            return response([
                'message' => 'User not found'
            ],403);
        }

        $owner = $request->validate([
            'name' => 'sometimes|required',
            'sex' => 'sometimes|required',
            'address' => 'sometimes|required',
            'image' => 'sometimes|nullable'
        ]);

        $imageData = $owner['image'];
        if ($request->has('image')) {
           if(strpos($imageData, '.jpg') !== false){
            $imageName = $owners->CustomerImage;
           }else{
            $image = base64_decode($imageData);
        $imageName = uniqid() . '.jpg';
        Storage::disk('public')->put('images/' . $imageName, $image);
           }
            
        } else {
            $imageName = $owners->CustomerImage; // Keep the existing image if no new image is provided
        }

        $owners->update([
            'CustomerName' => $owner['name'],
            'CustomerSex' => $owner['sex'],
            'CustomerAddress' => $owner['address'],
            'CustomerImage' => $imageName
        ]);

        $user->update([
            'contact' => $request['contact']
        ]);

        return response([
            'message' => 'Profile has been updated'
        ], 200);
    }
}
