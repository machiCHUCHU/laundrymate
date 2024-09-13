<?php

namespace App\Http\Controllers;
use App\Models\tbl_billing;
use App\Models\tbl_laundry_preference;
use App\Models\tbl_booking;
use App\Models\tbl_rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class bookController extends Controller
{
    /*public function bookingReq(Request $request){
        $pref = $request->validate([
            'ServiceType' => 'required|string',
            'WashingPref' => 'required|string',
            'DryingPref' =>'required string'
        ]);

        $book = $request->validate([
            'CustomerLoad' => 'required|integer',
            'LoadCost' =>'required|integer',
            'Notes' => 'string',
            'Schedule' => 'date_format:Y-m-d H:i:s',
            'CustomerID' => 'integer',
            'LaundryPrefID' => 'integer',
            'ShopID' => 'integer',
            'ServiceID' => 'integer'
        ]);

        $bill = $request->validate([
            'PaymentMethod' => 'string',
            'Amount' => 'integer',
            'CustomerID' => 'integer',
            'BookingID' => 'integer'
        ]);

        tbl_laundry_preference::create([
            'ServiceType' => $pref['serviceType'],
            'WashingPref' => $pref['washPref'],
            'DryingPref' => $pref['dryPref'],
        ]);

        tbl_booking::create([
            'CustomerLoad' => $book['cusLoad'],
            'LoadCost' => $book['loadCost'],
            'Notes' => $book['note'],
            'Schedule' => $book['sched'],
            'DateIssued' => date('Y-m-d'),
            'CustomerID' => $book['cusId'],
            'LaundryPrefID' => $book['laundryprefId'],
            'ShopID' => $book['shopId'],
            'Service' => $book['serviceId']
        ]);

        tbl_billing::create([
            'PaymentMethod' => $bill['paymode'],
            'Amount' => $bill['amt'],
            'DateIssued' => date('Y-m-d'),
            'CustomerID' => $bill['cusId'],
            'BookingID' => $bill['bookId']
        ]);
    }*/

    /*public function rating(Request $request){
        $rate = $request->validate([
            'Rate' => 'integer',
            'Comment' => 'string',
            'RatingImage' => 'image',
            'BookingID' => 'integer',
            'ShopID' => 'integer',
            'CustomerID' => 'integer'
        ]);

        tbl_rating::create([
            'Rate' => $rate['star'],
            'Comment' => $rate['comment'],
            'RatingImage' => $rate['rateImage'],
            'DateIssued' => date('Y-m-d'),
            'BookingID' => $rate['bookId'],
            'ShopID' => $rate['shopId'],
            'CustomerID' => $rate['cusId']
        ]);
    }*/

    public function bookingReq(Request $request)
    {
        $pref = $request->validate([
            'ServiceType' => 'required|string',
            'WashingPref' => 'required|string',
            'DryingPref' => 'required|string',
        ]);

        $book = $request->validate([
            'CustomerLoad' => 'required|numeric',
            'LoadCost' => 'required|numeric',
            'Notes' => 'nullable|string',
            'Schedule' => 'required|date_format:Y-m-d H:i:s',
            'LaundryPrefID' => 'nullable|integer',
            'ShopID' => 'nullable|integer',
            'ServiceID' => 'required|integer',
        ]);

        $bill = $request->validate([
            'PaymentMethod' => 'required|string',
            'Amount' => 'required|numeric',
        ]);

        $customerId = Auth::id(); // Get logged-in user's ID

        // Create laundry preference
        $laundryPref = tbl_laundry_preference::create([
            'ServiceType' => $pref['ServiceType'],
            'WashingPref' => $pref['WashingPref'],
            'DryingPref' => $pref['DryingPref'],
        ]);

        // Create booking
        $booking = tbl_booking::create([
            'CustomerLoad' => $book['CustomerLoad'],
            'LoadCost' => $book['LoadCost'],
            'Notes' => $book['Notes'],
            'Schedule' => $book['Schedule'],
            'DateIssued' => now(),
            'CustomerID' => $customerId,
            'LaundryPrefID' => $laundryPref->id, // Use ID from the created laundry preference
            'ShopID' => $book['ShopID'], // Use provided ShopID
            'ServiceID' => $book['ServiceID'],
        ]);

        // Create billing
        tbl_billing::create([
            'PaymentMethod' => $bill['PaymentMethod'],
            'Amount' => $bill['Amount'],
            'DateIssued' => now(),
            'CustomerID' => $customerId,
            'BookingID' => $booking->id, // Use ID from the created booking
        ]);

        return response()->json(['message' => 'Booking created successfully'], 201);
    }

    public function rating(Request $request)
    {
        $rate = $request->validate([
            'Rate' => 'required|integer',
            'Comment' => 'nullable|string',
            'RatingImage' => 'nullable|image',
            'BookingID' => 'required|integer',
            'ShopID' => 'required|integer',
            'CustomerID' => 'required|integer',
        ]);

        tbl_rating::create([
            'Rate' => $rate['Rate'],
            'Comment' => $rate['Comment'],
            'RatingImage' => $rate['RatingImage'] ? $rate['RatingImage']->store('ratings') : null,
            'DateIssued' => now(),
            'BookingID' => $rate['BookingID'],
            'ShopID' => $rate['ShopID'],
            'CustomerID' => $rate['CustomerID'],
        ]);

        return response()->json(['message' => 'Rating added successfully'], 201);
    }
}
