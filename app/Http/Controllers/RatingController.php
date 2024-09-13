<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'Rate' => 'required|in:1,2,3,4,5',
            'Comment' => 'required|string',
            'RatingImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'BookingID' => 'required|integer',
            'ShopID' => 'required|integer',
            'CustomerID' => 'required|integer',
        ]);

        // Handle the image upload if available
        $imagePath = null;
        if ($request->hasFile('RatingImage')) {
            $imagePath = $request->file('RatingImage')->store('ratings', 'public');
        }

        $rating = Rating::create([
            'Rate' => $request->input('Rate'),
            'Comment' => $request->input('Comment'),
            'RatingImage' => $imagePath,
            'DateIssued' => now(),
            'BookingID' => $request->input('BookingID'),
            'ShopID' => $request->input('ShopID'),
            'CustomerID' => $request->input('CustomerID'),
        ]);

        return response()->json(['success' => true, 'rating' => $rating], 201);
    }
     // Fetch ratings for a specific shop
     public function getRatingsByShop($shopId)
{
    try {
        // Fetch ratings for the given ShopID and include customer names
        $ratings = DB::table('tbl_ratings')
            ->join('tbl_customers', 'tbl_ratings.CustomerID', '=', 'tbl_customers.CustomerID')
            ->where('tbl_ratings.ShopID', $shopId)
            ->get([
                'tbl_ratings.Rate',
                'tbl_ratings.Comment',
                'tbl_ratings.RatingImage',
                'tbl_ratings.DateIssued',
                'tbl_customers.Name as username' // Fetch customer's name as 'username'
            ]);

        if ($ratings->isEmpty()) {
            return response()->json(['message' => 'No ratings found for this shop'], 404);
        }

        return response()->json($ratings, 200);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


     public function getImage($filename)
{
    // Fetch image data from database
    $rating = Rating::where('RatingImage', $filename)->first();

    if (!$rating) {
        abort(404, 'Image not found');
    }

    $imageData = $rating->RatingImage; // Assuming this is a BLOB field

    return response($imageData, 200)
        ->header('Content-Type', 'image/jpeg'); // Change content type if needed
}

}

