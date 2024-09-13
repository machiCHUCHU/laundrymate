<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index($userId)
    {
        try {
            // Fetch CustomerID based on UserID
            $customer = DB::table('tbl_customers')->where('UserID', $userId)->first();
            
            if (!$customer) {
                return response()->json(['message' => 'Invalid UserID'], 400);
            }
    
            // Use the CustomerID to fetch bookings
            $bookings = DB::table('tbl_bookings')
                ->where('CustomerID', $customer->CustomerID)
                ->get();
    
            return response()->json(['bookings' => $bookings], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    

    public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'CustomerLoad' => 'required|numeric',
        'Schedule' => 'required|date',
        'DateIssued' => 'nullable|date',
        'CustomerID' => 'required|integer',
        'ShopID' => 'required|integer',
    ]);

    try {
        // Check if the customer exists
        $customer = DB::table('tbl_customers')->where('CustomerID', $request->input('CustomerID'))->first();

        if (!$customer) {
            return response()->json(['message' => 'Invalid CustomerID'], 400);
        }

        // Fetch ServiceID and LoadPrice based on ShopID and ServiceID
        $service = DB::table('tbl_laundry_services')
            ->where('ShopID', $request->input('ShopID'))
            ->first();

        if (!$service) {
            return response()->json(['message' => 'No services found for this ShopID and ServiceID'], 400);
        }

        // Calculate LoadCost based on the LoadPrice from the service
        $loadCost = $service->LoadPrice;

        // Set DateIssued to current timestamp if not provided
        $dateIssued = $request->input('DateIssued', now());

        // Create the booking
        $booking = Booking::create([
            'CustomerLoad' => $request->input('CustomerLoad'),
            'LoadCost' => $loadCost, 
            'Schedule' => $request->input('Schedule'),
            'DateIssued' => $dateIssued,
            'Status' => $request->input('Status', '0'), 
            'PaymentStatus' => $request->input('PaymentStatus', 'pending'),
            'CustomerID' => $customer->CustomerID,
            'ShopID' => $request->input('ShopID'),
            'ServiceID' => $service->ServiceID, 
        ]);

     
        return response()->json([
            'success' => true,
            'data' => $booking
        ], 201);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


    public function getBookings($userId)
    {
        try {
            \Log::info('Fetching bookings for UserID: ' . $userId);

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

            $bookings = DB::table('tbl_bookings')
                         ->join('tbl_shops', 'tbl_bookings.ShopID', '=', 'tbl_shops.ShopID')
                         ->where('tbl_bookings.CustomerID', $customerId)
                         ->select('tbl_bookings.*', 'tbl_shops.ShopName') 
                         ->get();

            if ($bookings->isEmpty()) {
                \Log::info('No bookings found for CustomerID: ' . $customerId);
                return response()->json(['message' => 'No bookings found for this customer'], 404);
            }

            return response()->json($bookings);

        } catch (\Exception $e) {
            \Log::error('Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function update(Request $request, $id)
{
    // Validate the request data
    $request->validate([
        'status' => 'required|integer|in:0,1,2,3,4,5,6', 
    ]);

    try {
        // Find the booking by BookingID
        $booking = Booking::where('BookingID', $id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Update the booking status
        $booking->status = $request->input('status');
        $booking->save();

     
        return response()->json([
            'success' => true,
            'data' => $booking
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

}
