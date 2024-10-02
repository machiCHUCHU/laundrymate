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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{
    public function donut_chart(){
        $user = Auth::user(); 
        $userContact = $user->contact;
    
        // Get the owner and shop IDs
        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)->value('OwnerID');
        $shopId = tbl_shop::where('OwnerID', $ownerId)->value('ShopID');
    
        // Get services from tbl_bookings
        $bookingService = DB::table('tbl_bookings')
            ->join('tbl_laundry_services', 'tbl_bookings.ServiceID', '=', 'tbl_laundry_services.ServiceID')
            ->where('tbl_bookings.ShopID', $shopId)
            ->whereIn('tbl_bookings.Status', ['5', '6']) 
            ->select('tbl_laundry_services.ServiceName', DB::raw("COUNT(tbl_laundry_services.ServiceName) as count"))
            ->groupBy('tbl_laundry_services.ServiceName');
    
        
        $walkinService = DB::table('tbl_walkins')
            ->join('tbl_laundry_services', 'tbl_walkins.ServiceID', '=', 'tbl_laundry_services.ServiceID')
            ->where('tbl_walkins.ShopID', $shopId)
            ->whereIn('tbl_walkins.Status', ['5', '6'])
            ->select('tbl_laundry_services.ServiceName', DB::raw("COUNT(tbl_laundry_services.ServiceName) as count"))
            ->groupBy('tbl_laundry_services.ServiceName');
    
        
        $combinedServices = DB::query()
            ->fromSub(function ($query) use ($bookingService, $walkinService) {
                $query->select('ServiceName', DB::raw('SUM(count) as count'))
                      ->from($bookingService->unionAll($walkinService), 'combined')
                      ->groupBy('ServiceName');
            }, 'final_combined')
            ->get();
    
        
        $combinedServices = $combinedServices->map(function($service) {
            $service->count = (int) $service->count;
            return $service;
        });
    
       
        $totalServicesMade = DB::table('tbl_bookings')
            ->where('ShopID', $shopId)
            ->whereIn('Status', ['5', '6'])
            ->count() +
            DB::table('tbl_walkins')
            ->where('ShopID', $shopId)
            ->whereIn('Status', ['5', '6'])
            ->count();
    
        return response([
            'services' => $combinedServices,
            'servicemade' => $totalServicesMade
        ]);
    }
    
    public function inventory_chart(){
        $user = Auth::user(); 
        $userContact = $user->contact;
    
        
        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)->value('OwnerID');
        $shopId = tbl_shop::where('OwnerID', $ownerId)->value('ShopID');

        $inventory = tbl_inventory::where('ShopID', $shopId)
        ->select('ItemName', 'ItemQty')
        ->get();

        $inventoryQty = $inventory->sum('ItemQty');
        
        return response([
            'inventory' => $inventory,
            'count' => $inventoryQty
        ]);
    }
    
    public function sales_monthly(){
        $user = Auth::user(); 
        $userContact = $user->contact;
    
        $ownerId = tbl_owner::where('OwnerContactNumber', $userContact)
                            ->pluck('OwnerID');
    
        $shopId = tbl_shop::where('OwnerID', $ownerId)
                          ->pluck('ShopID');
    
        $startingMonth = Carbon::now()->startOfYear();
        $endingMonth = Carbon::now()->endOfYear();
    
        // Query for tbl_bookings
        $bookingSales = DB::table('tbl_bookings')
            ->select(DB::raw('MONTHNAME(Schedule) as day, SUM(LoadCost) as made'))
            ->whereIn('ShopID', $shopId)
            ->where('PaymentStatus', 'paid')
            ->whereBetween('Schedule', [$startingMonth, $endingMonth])
            ->groupBy(DB::raw('MONTHNAME(Schedule)'))
            ->pluck('made', 'day');
    
        // Query for tbl_walkins
        $walkinSales = DB::table('tbl_walkins')
            ->select(DB::raw('MONTHNAME(DateIssued) as day, SUM(Total) as made'))
            ->whereIn('ShopID', $shopId)
            ->where('PaymentStatus', 'paid')
            ->whereBetween('DateIssued', [$startingMonth, $endingMonth])
            ->groupBy(DB::raw('MONTHNAME(DateIssued)'))
            ->pluck('made', 'day');
    
        
        $months = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0,
        ];
    
        // Sum the sales from both bookingSales and walkinSales
        foreach ($months as $month => $value) {
            $monthRange[$month] = (isset($bookingSales[$month]) ? $bookingSales[$month] : 0) + 
                                (isset($walkinSales[$month]) ? $walkinSales[$month] : 0);
        }
    
        // Calculate total sales
        $totalSales = array_sum($monthRange);
    
        return response([
            'jan' => $monthRange['January'],
            'feb' => $monthRange['February'],
            'mar' => $monthRange['March'],
            'apr' => $monthRange['April'],
            'may' => $monthRange['May'],
            'jun' => $monthRange['June'],
            'jul' => $monthRange['July'],
            'aug' => $monthRange['August'],
            'sep' => $monthRange['September'],
            'oct' => $monthRange['October'],
            'nov' => $monthRange['November'],
            'dec' => $monthRange['December'],
            'total' => $totalSales,
        ]);
    }
    
}
