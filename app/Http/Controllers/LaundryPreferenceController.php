<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tbl_laundry_preference;
use App\Models\tbl_clothing_types;
use App\Models\tbl_booking;
class LaundryPreferenceController extends Controller
{
    public function index()
    {
        $preferences = tbl_laundry_preference::all();
        return response()->json($preferences);
    }

    public function addLaundryPreference(Request $request)
    {
        // Validate laundry preferences
        $laundryPref = $request->validate([
            'ServiceType' => 'required|in:full,self',
            'WashingPref' => 'required|in:hot,warm,cold,delicate',
            'DryingPref' => 'required|in:high,mid,low,none',
        ]);

        // Validate clothing types
        $clothingTypes = $request->validate([
            'ClothingTypes' => 'required|array',
            'ClothingTypes.*' => 'required|string',
        ]);

        // Validate booking information
        $booking = $request->validate([
            'CustomerLoad' => 'required|numeric',
            'LoadCost' => 'required|numeric',
            'Notes' => 'nullable|string',
            'Schedule' => 'required|date',
            'Status' => 'required|in:0,1,2,3,4,5,6',
            'CustomerID' => 'required|integer',
            'ShopID' => 'required|integer',
            'ServiceID' => 'required|integer',
        ]);

        // Save laundry preferences
        $laundryPreference = tbl_laundry_preference::create($laundryPref);

        // Save clothing types
        foreach ($clothingTypes['ClothingTypes'] as $type) {
            tbl_clothing_types::create([
                'ClothingType' => $type,
            ]);
        }

        // Save booking information
        $booking['LaundryPrefID'] = $laundryPreference->laundryPrefID;
        tbl_booking::create($booking);

        return response([
            'response' => 'success'
        ], 200);
    }
}
