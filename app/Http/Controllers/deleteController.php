<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class deleteController extends Controller
{
    public function image_insert(Request $request){
        $request->validate([
            'image' => 'image|mimes:png,jpg'
        ]);

        $imagePath = $request->file('image')->store('images');

        return response()->json(['success' => true, 'imagePath' => $imagePath]);
    }
}
