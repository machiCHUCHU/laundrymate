<?php

namespace App\Console\Commands;

use App\Models\tbl_booking;
use App\Models\tbl_notif;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:booking-update-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $today = Carbon::now()->toDateString();
    $bookings = DB::table('tbl_bookings')
    ->join('tbl_shops', 'tbl_bookings.ShopID', '=', 'tbl_shops.ShopID')
    ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', '=', 'tbl_shop_machines.ShopMachineID')
    ->join('tbl_customers', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerID')
    ->where('tbl_bookings.Status', '0')
    ->where('tbl_bookings.deleted_at', null)
    ->select('tbl_bookings.BookingID', 'tbl_bookings.CustomerID', 'tbl_customers.CustomerContactNumber', 'tbl_bookings.updated_at', 
    'tbl_bookings.Status', 'tbl_shop_machines.WasherTime', 'tbl_shop_machines.DryerTime', 'tbl_shop_machines.FoldingTime', 
    'tbl_shops.ShopID', 'tbl_shop_machines.WasherQty', 'tbl_shop_machines.DryerQty', 'tbl_shop_machines.ShopMachineID', 'tbl_shops.ShopName',
    'tbl_bookings.Schedule')
    ->get();

        foreach ($bookings as $booking) {
            $issuedTime = Carbon::parse($booking->updated_at);

            
            $timeThresholds = [
                1 => $booking->WasherTime,  
                2 => $booking->DryerTime, 
                3 => $booking->FoldingTime 
            ];

            $currentStatus = $booking->Status;

            
            if ($currentStatus < 4 && Carbon::now() >= $issuedTime->addMinutes($timeThresholds[$currentStatus + 1])) {
                DB::table('tbl_bookings')
                    ->where('BookingID', $booking->BookingID)
                    ->update([
                        'Status' => (string)($currentStatus + 1),
                        'updated_at' => Carbon::now()
                    ]);
                    // $this->info("Walk-in ID {$booking->BookingID} at Shop ID {$booking->ShopID} status updated to " . ($currentStatus + 1));

                    if($currentStatus == 1){
                        DB::table('tbl_shop_machines')
                        ->where('ShopMachineID', $booking->ShopMachineID)
                        ->increment('WasherQty');
                        DB::table('tbl_shop_machines')
                        ->where('ShopMachineID', $booking->ShopMachineID)
                        ->decrement('DryerQty');
                    }

                    if($currentStatus == 2){
                        DB::table('tbl_shop_machines')
                        ->where('ShopMachineID', $booking->ShopMachineID)
                        ->increment('DryerQty');
                    }

                    if($currentStatus == 3){
                        tbl_notif::create([
                            'CustomerID' => $booking->CustomerID,
                            'BookingID' => $booking->BookingID,
                            'Title' => 'Laundry is Ready for Pick-up',
                            'Message' => 'Good day! Your laundry from '.$booking->ShopName.' is ready for pick-up.'
                        ]);

                        $apiKey = env('SEMAPHORE_API_KEY');
                    
                    $ch = curl_init();

                    $parameters = array(
                'apikey' => $apiKey,
                'number' => $booking->CustomerContactNumber,
                'message' => 'Good day! This is to inform you that your laundry is ready for pickup.',
                'sendername' => 'LAUNDRYMATE'
            );
            curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
            curl_setopt( $ch, CURLOPT_POST, 1 );

            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );


            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close ($ch);
                    }
            }

            $bookDate = Carbon::parse($booking->Schedule);

            if($booking->Status == '0'){
                $this->info('sdfdsf');
                if($bookDate->isPast()){
                    tbl_booking::where('BookingID',$booking->BookingID)->delete();
                    $this->info('afadf');
                }
            }
            
                
            }
            
           

}
}
