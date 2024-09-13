<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AddedShop;
class AddedShopController extends Controller
{
    public function index($userId)
    {
        try {
            // Fetch CustomerID based on UserID
        $customer = DB::table('tbl_customers')->where('UserID', $userId)->first();
        
        if (!$customer) {
            return response()->json(['message' => 'Invalid UserID'], 400);
        }// Use the CustomerID to fetch added shops
        $addedShops = AddedShop::where('CustomerID', $customer->CustomerID)->get();
        
        return response()->json(['addedshops' => $addedShops], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
    }   
    }


    public function store(Request $request)
{
    $request->validate([
        'IsValued' => 'required|in:0,1,2',
        'Date' => 'required|date',
        'ShopID' => 'required|integer',
        'CustomerID' => 'required|integer',
    ]);

    
    $customer = DB::table('tbl_customers')->where('CustomerID', $request->input('CustomerID'))->first();

    if (!$customer) {
        return response()->json(['message' => 'Invalid CustomerID'], 400);
    }

    
    $addedShop = AddedShop::create([
        'IsValued' => $request->input('IsValued'),
        'Date' => $request->input('Date'),
        'ShopID' => $request->input('ShopID'),
        'CustomerID' => $customer->CustomerID,
    ]);

    return response()->json([
        'success' => true,
        'data' => $addedShop
    ], 201);
}
public function getAddedShops($userId)
{
    try {
        \Log::info('Fetching added shops for UserID: ' . $userId);

        $customer = DB::table('tbl_customers')->where('UserID', $userId)->first();

        if (!$customer) {
            \Log::error('Customer not found for UserID: ' . $userId);
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customerId = $customer->CustomerID;
        \Log::info('CustomerID: ' . $customerId);

        if (!$customerId) {
            \Log::error('CustomerID is null for UserID: ' . $userId);
            return response()->json(['error' => 'CustomerID is null'], 500);
        }

        $shops = DB::table('tbl_added_shops')
                   ->join('tbl_shops', 'tbl_added_shops.ShopID', '=', 'tbl_shops.ShopID')
                   ->where('tbl_added_shops.CustomerID', $customerId)
                   ->select('tbl_shops.*')
                   ->get();

        if ($shops->isEmpty()) {
            \Log::info('No shops found for CustomerID: ' . $customerId);
            return response()->json(['message' => 'No shops found for this customer'], 404);
        }

        return response()->json($shops);

    } catch (\Exception $e) {
        \Log::error('Exception: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}
}
