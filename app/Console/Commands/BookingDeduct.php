<?php

namespace App\Console\Commands;

use App\Models\tbl_billing;
use Illuminate\Console\Command;
use App\Models\tbl_booking;
use App\Models\tbl_shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingDeduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:booking-deduct';

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
        

        $dateToday = Carbon::now()->toDateString();

        $booking = DB::table('tbl_bookings')
        ->select('ShopID', DB::raw('COUNT(BookingID) as bookcount'))
        ->whereDate('Schedule', $dateToday)
        ->where('deleted_at',null)
        ->groupBy('ShopID')
        ->get();

        foreach($booking as $book){
            $remainingLoad = DB::table('tbl_shops')
            ->where('ShopID', $book->ShopID)
            ->value('RemainingLoad');

            DB::table('tbl_shops')
            ->where('ShopID', $book->ShopID)
            ->update([
                'RemainingLoad' => $remainingLoad - $book->bookcount
            ]);
        }
    }
}
