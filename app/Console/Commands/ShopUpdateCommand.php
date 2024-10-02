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
        $today = Carbon::now();
        $isWeekend = $today->isWeekend();
        $isWeekday = $today->isWeekday();


        foreach ($shops as $shop) {
            $shopTime = $shop->WorkHour;
            $shopDays = $shop->WorkDay;
        
            [$startTimeString, $endTimeString] = explode(" - ", $shopTime);
        
            $startTime = Carbon::createFromFormat("h:i A", $startTimeString);
            $endTime = Carbon::createFromFormat("h:i A", $endTimeString);
        
            $currentTime = Carbon::now()->format('h:i A');

            if($shopDays == 'weekend' && $isWeekend){
                if($startTime->format('h:i A') == $currentTime){
                    $shop->update([
                        'ShopStatus' => 'open'
                    ]);
                    
                }

                if($currentTime == $endTime->format('h:i A')){
                    $shop->update([
                        'ShopStatus' => 'closed'
                    ]);
                }

                if($today->between($startTime,$endTime)){
                    if($shop->RemainingLoad == 0){
                        $shop->update([
                            'ShopStatus' => 'full'
                            ]);
                    }

                    if($shop->RemainingLoad > 0){
                        $shop->update([
                            'ShopStatus' => 'open'
                            ]);
                    }
                }
            }else if($shopDays == 'weekend' && !$isWeekend){
                $shop->update([
                    'ShopStatus' => 'closed'
                ]);
            }

            if($shopDays == 'weekdays' && $isWeekday){
                if($startTime->format('h:i A') == $currentTime){
                    $shop->update([
                        'ShopStatus' => 'open'
                    ]);
                    $this->info('adfadfa');
                }

                if($currentTime == $endTime->format('h:i A')){
                    $shop->update([
                        'ShopStatus' => 'closed'
                    ]);
                }

                if($today->between($startTime,$endTime)){
                    if($shop->RemainingLoad == 0){
                        $shop->update([
                            'ShopStatus' => 'full'
                            ]);
                    }

                    if($shop->RemainingLoad > 0){
                        $shop->update([
                            'ShopStatus' => 'open'
                            ]);
                    }
                }
            }else if($shopDays == 'weekdays' && !$isWeekday){
                $shop->update([
                    'ShopStatus' => 'closed'
                ]);
            }


            

            
            if($today->isMidnight()){
                $shop->update([
                    'RemainingLoad' => $shop->MaxLoad
                ]);
            }

           
        }
    }
}
