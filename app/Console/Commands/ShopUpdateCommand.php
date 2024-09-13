<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tbl_shop;
use Carbon\Carbon;

class ShopUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:shop-update-command';

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
        $shops = tbl_shop::all();

        foreach ($shops as $shop) {
            $shopTime = $shop->WorkHour;
        
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
    }
}
