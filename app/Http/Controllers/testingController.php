<?php

namespace App\Http\Controllers;

use App\Models\delete_picture;
use App\Models\tbl_shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tbl_shop_machine;
use App\Models\tbl_booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Constraint\IsEmpty;
class testingController extends Controller
{
    public function testing(){
        $shops = tbl_shop::all();

        foreach ($shops as $shop) {
            $shopTime = $shop->WorkHour; // Assuming 'WorkHour' is the column name
        
            [$startTimeString, $endTimeString] = explode(" - ", $shopTime);
        
            $startTime = Carbon::createFromFormat("h:i A", $startTimeString);
            $endTime = Carbon::createFromFormat("h:i A", $endTimeString);
        
            $formattedShopData[] = [
                'shop_id' => $shop->ShopID,
                'start_time' => $startTime->format('h:i A'),
                'end_time' => $endTime->format('h:i A'),
            ];

            $currentTime = Carbon::now()->format('h:i A');

            if($startTime->format('h:i A') == $currentTime){
                $shop->update([
                    'ShopStatus' => 'open'
                ]);
            }
           
        }

        return response([
            'test' => $formattedShopData,
            'time' => $currentTime
        ]);
    }

    public function picture(Request $request){
        $imageBase64 = $request->input('image');

    if ($imageBase64) {
        // Decode and save the image
        $image = base64_decode($imageBase64);
        $imageName = uniqid() . '.jpg';
        Storage::disk('public')->put('images/' . $imageName, $image);

        return response()->json(['path' => 'images/' . $imageName], 200);
    } else {
        return response()->json(['message' => 'Image is null or not provided'], 400);
    }

        
    }

    public function getImage(Request $request) {
        $request->validate([
            'image' => 'required|string', // Validate that the image is a string (base64)
        ]);

        $image = $request->input('image'); // Get the base64 string

        // Decode the base64 string
        $image = base64_decode($image);

        // Generate a unique name for the image
        $imageName = uniqid() . '.jpg'; // Assuming the image is a JPG. Adjust the extension accordingly.

        // Store the image in the 'public/images' folder
        Storage::disk('public')->put('images/' . $imageName, $image);

        // Return a response with the image path
        return response()->json(['path' => 'images/' . $imageName], 200);
    }

    public function send_sms(){
        $apiKey = env('SEMAPHORE_API_KEY');
        $ch = curl_init();

        $parameters = array(
    'apikey' => $apiKey,
    'number' => '09655543516',
    'message' => 'I just sent my first message with Semaphore',
    'sendername' => 'LAUNDRYMATE'
);
curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
curl_setopt( $ch, CURLOPT_POST, 1 );

curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );


curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );
curl_close ($ch);


return response([
    'code' => 'hello world'
]);
    }

    public function bar_test($id){
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $results = DB::table('tbl_bookings')
            ->select(DB::raw('DAYNAME(DATE(Schedule)) as day'), DB::raw('COUNT(*) as daycount'))
            ->whereBetween('Schedule', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw('DAYNAME(DATE(Schedule))'))
            ->where('Status', '5')
            ->where('ShopID', $id)
            ->get();

        $mostLaundryDay = DB::table('tbl_bookings')
        ->select(DB::raw('COUNT(*) as daycount'), DB::raw('DAYNAME(DATE(Schedule)) as dayname'))
        ->whereBetween('Schedule', [$startOfWeek, $endOfWeek])
        ->where('ShopID', $id)
        ->groupBy(DB::raw('DAYNAME(DATE(Schedule))'))
        ->orderBy('daycount', 'desc')
        ->first();

        $avgLaundry = DB::table('tbl_bookings')
        ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
        ->select(DB::raw('SUM(tbl_bookings.CustomerLoad) as customerload'), DB::raw('COUNT(*) as totalbooks'))
        ->whereBetween('tbl_bookings.Schedule', [$startOfWeek, $endOfWeek])
        ->where('tbl_bookings.ShopID', $id)
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

    public function bar_test2($id){
        
        $results = DB::table('tbl_bookings')
            ->select(DB::raw('MONTHNAME(DATE(Schedule)) as month'), DB::raw('COUNT(*) as monthcount'))
            ->groupBy(DB::raw('MONTHNAME(DATE(Schedule))'))
            ->where('Status', '5')
            ->where('ShopID', $id)
            ->get();

            $mostLaundryMonth = DB::table('tbl_bookings')
        ->select(DB::raw('COUNT(*) as monthcount'), DB::raw('MONTHNAME(DATE(Schedule)) as monthname'))
        ->where('Status', '5')
        ->where('ShopID', $id)
        ->groupBy(DB::raw('MONTHNAME(DATE(Schedule))'))
        ->orderBy('monthcount', 'desc')
        ->first();

        $avgLaundry = DB::table('tbl_bookings')
        ->join('tbl_shops', 'tbl_bookings.ShopID', 'tbl_shops.ShopID')
        ->select(DB::raw('SUM(tbl_bookings.CustomerLoad) as customerload'), DB::raw('COUNT(*) as totalbooks'))
        ->where('tbl_bookings.Status', '5')
        ->where('tbl_bookings.ShopID', $id)
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
            'avg' => $avgLaundry
        ]);
    }

    public function test_data(){
        $today = Carbon::now()->toDateString();
        $bookings = DB::table('tbl_bookings')
            ->join('tbl_shops', 'tbl_bookings.ShopID', '=', 'tbl_shops.ShopID')
            ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', '=', 'tbl_shop_machines.ShopMachineID')
            ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
            ->where('tbl_bookings.Status', '>', '0')
            ->where(DB::raw("DATE_FORMAT(tbl_bookings.Schedule, '%Y-%m-%d')"), $today)
            ->where('tbl_bookings.deleted_at', null)
            ->select('tbl_bookings.BookingID', 'tbl_customers.ContactNumber', 'tbl_bookings.updated_at', 
            'tbl_bookings.Status', 'tbl_shop_machines.WasherTime', 'tbl_shop_machines.DryerTime', 'tbl_shop_machines.FoldingTime', 
            'tbl_shops.ShopID', 'tbl_shop_machines.WasherQty', 'tbl_shop_machines.DryerQty')
            ->get();

            

            return response(
                $bookings,
                
            );
    }
}
