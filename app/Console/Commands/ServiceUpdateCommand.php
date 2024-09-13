<?php

namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_owner;
use App\Models\tbl_shop;

class ServiceUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:service-update-command';

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
    $walkins = DB::table('tbl_walkins')
            ->join('tbl_shops', 'tbl_walkins.ShopID', '=', 'tbl_shops.ShopID')
            ->join('tbl_shop_machines', 'tbl_shops.ShopMachineID', '=', 'tbl_shop_machines.ShopMachineID')
            ->where('tbl_walkins.Status', '>', '0')
            ->where(DB::raw("DATE_FORMAT(tbl_walkins.DateIssued, '%Y-%m-%d')"), $today)
            ->where('tbl_walkins.deleted_at', null)
            ->select('tbl_walkins.WalkinID', 'tbl_walkins.ContactNumber', 'tbl_walkins.updated_at', 
            'tbl_walkins.Status', 'tbl_shop_machines.WasherTime', 'tbl_shop_machines.DryerTime', 'tbl_shop_machines.FoldingTime', 
            'tbl_shops.ShopID', 'tbl_shop_machines.WasherQty', 'tbl_shop_machines.DryerQty')
            ->get();

        foreach ($walkins as $walkin) {
            $issuedTime = Carbon::parse($walkin->updated_at);

            // Calculate the time thresholds for each status update based on the machine set of the specific shop
            $timeThresholds = [
                2 => $walkin->WasherTime,  // Status 2 after washing
                3 => $walkin->DryerTime, // Status 3 after drying
                4 => $walkin->FoldingTime // Status 4 after folding
            ];

            $currentStatus = $walkin->Status;

            // Check if the current time exceeds the threshold for the next status
            if ($currentStatus < 4 && Carbon::now() >= $issuedTime->addMinutes($timeThresholds[$currentStatus + 1])) {
                DB::table('tbl_walkins')
                    ->where('WalkinID', $walkin->WalkinID)
                    ->update([
                        'Status' => (string)($currentStatus + 1),
                        'updated_at' => Carbon::now()
                    ]);
                    $this->info("Walk-in ID {$walkin->WalkinID} at Shop ID {$walkin->ShopID} status updated to " . ($currentStatus + 1));

                    if($currentStatus == 1){
                        DB::table('tbl_shop_machines')
                        ->where('ShopMachineID', $walkin->ShopMachineID)
                        ->increment('WasherQty');
                        DB::table('tbl_shop_machines')
                        ->where('ShopMachineID', $walkin->ShopMachineID)
                        ->decrement('DryerQty');
                    }

                    if($currentStatus == 2){
                        DB::table('tbl_shop_machines')
                        ->where('ShopMachineID', $walkin->ShopMachineID)
                        ->increment('DryerQty');
                    }

                    if($currentStatus == 3){
                        $apiKey = env('SEMAPHORE_API_KEY');
                    
                    $ch = curl_init();

                    $parameters = array(
                'apikey' => $apiKey,
                'number' => $walkin->ContactNumber,
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

            
                
            }
            
           

}

}
