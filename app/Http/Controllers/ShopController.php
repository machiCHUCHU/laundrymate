<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tbl_shop;
class ShopController extends Controller
{
    public function index()
    {
        try {
            $shops = tbl_shop::all(); // Correctly reference your model
            return response()->json(['shops' => $shops], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
