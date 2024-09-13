<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClothingType;
class ClothingTypeController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'clothingTypes' => 'required|array',
        'clothingTypes.*' => 'string'
    ]);

    $userID = $request->input('userID');
    
    foreach ($request->input('clothingTypes') as $type) {
        ClothingType::updateOrCreate(
            ['ClothingType' => $type]
        );
    }

    return response()->json(['message' => 'Clothing types saved successfully'], 201);
}
}
