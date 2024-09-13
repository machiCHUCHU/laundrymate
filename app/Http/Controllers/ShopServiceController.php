<?php
namespace App\Http\Controllers;

use App\Models\tbl_laundry_service;
use App\Models\tbl_shop;
use Illuminate\Http\Request;

class ShopServiceController extends Controller
{
    public function getShopServices($shopId)
    {
        // Fetch the shop
        $shop = tbl_shop::where('ShopID', $shopId)->first();

        if (!$shop) {
            return response()->json(['error' => 'Shop not found.'], 404);
        }

        // Fetch services based on the ShopID
        $services = tbl_laundry_service::where('ShopID', $shopId)->get();

        // Gather service information
        $serviceDetails = $services->map(function ($service) {
            return [
                'ServiceID' => $service->ServiceID,
                'ServiceName' => $service->ServiceName,
                'LoadWeight' => $service->LoadWeight,
                'LoadPrice' => $service->LoadPrice,
                'ShopID' => $service->ShopID,
            ];
        });

        // Return the collected service details as a JSON response
        return response()->json($serviceDetails);
    }
}
