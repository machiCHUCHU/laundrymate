<?php

use App\Http\Controllers\smsController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/sms','App\Http\Controllers\smsController@sms');
