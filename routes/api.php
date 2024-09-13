<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegController;
use App\Http\Controllers\smsController;
use App\Http\Controllers\deleteController;
use App\Http\Controllers\authController;
use App\Http\Controllers\ownerShopController;
use App\Http\Controllers\testingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LaundryPreferenceController;
use App\Http\Controllers\ClothingTypeController;
use App\Http\Controllers\AddedShopController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ShopServiceController;

// Route::post('/register/customer', [RegController::class, 'customer_register']); //SECOND CHECKED!!
// Route::post('/register/owner', [RegController::class, 'owner_register']); //SECOND CHECKED!!
Route::post('/registration', [RegController::class, 'registration']);
Route::post('/login', [authController::class, 'login']);//SECOND CHECKED!!
Route::post('/shop-setup', [RegController::class, 'owner_shop_info'])->middleware('auth:sanctum');  //SECOND-CHECKED!!
Route::get('/shop-code/{id}', [ownerShopController::class, 'shop_code_display']); //SECOND CHECKED!!
Route::post('/verification/{number}', [RegController::class, 'sms_otp']);

Route::post('/shop-inventory/add', [RegController::class, 'inventory'])->middleware('auth:sanctum'); //SECOND CHECKED!!
Route::get('/shop-added-customer', [ownerShopController::class, 'added_shop_display'])->middleware('auth:sanctum'); //SECOND CHECKED!!
Route::put('/shop-added-customer/update/{id}', [ownerShopController::class, 'added_shop_status'])->middleware('auth:sanctum'); //SECOND CHECKED!!
Route::get('/shop-inventory', [ownerShopController::class, 'inventory_display'])->middleware('auth:sanctum'); //SECOND CHECKED!!

Route::post('shop-inventory/delete/{id}', [ownerShopController::class, 'inventory_delete'])->middleware('auth:sanctum'); //SECOND CHECKED!!

Route::put('/shop-inventory/update/{id}', [ownerShopController::class, 'inventory_edit']); //SECOND CHECKED!!
Route::get('/profile/{id}', [ownerShopController::class, 'profile_display']); //INITIAL CHECKED!!
 //SECOND CHECKED!!

Route::get('/bookings', [ownerShopController::class, 'bookings_display'])->middleware('auth:sanctum');
                          //{number}
Route::get('/bookings/pending', [ownerShopController::class, 'pending_booking_display'])->middleware('auth:sanctum'); //SECOND CHECKED
Route::put('/bookings/pending/update/{id}', [ownerShopController::class, 'pending_booking_stat'])->middleware('auth:sanctum'); //SECOND CHECKED

Route::get('/bookings/process', [ownerShopController::class, 'process_booking_display'])->middleware('auth:sanctum'); //SECOND CHECKED
Route::put('/bookings/process/update/{id}', [ownerShopController::class, 'process_booking_stat'])->middleware('auth:sanctum'); //SECOND CHECKED

Route::get('/bookings/finished', [ownerShopController::class, 'finished_booking_display'])->middleware('auth:sanctum'); //SECOND CHECKED

Route::get('/shop-match', [authController::class, 'shop_match'])->middleware('auth:sanctum'); //SECOND CHECKED

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/verify-token', function (Request $request) {
    return response()->json(['message' => 'Token is valid'], 200);
});

Route::get('/remember', [authController::class, 'remember_me'])->middleware('auth:sanctum');

Route::get('/rating', [ownerShopController::class, 'booking_rating_display'])->middleware('auth:sanctum'); //SECOND CHECKED
Route::get('/booking/weekly', [ownerShopController::class, 'booking_weekly_display'])->middleware('auth:sanctum'); //INITIAL CHECKED
Route::get('/booking/monthly', [ownerShopController::class, 'booking_monthly_display'])->middleware('auth:sanctum'); //INITIAL CHECKED
Route::get('/sales/weekly', [ownerShopController::class, 'sales_weekly_display'])->middleware('auth:sanctum');
Route::get('/sales/monthly', [ownerShopController::class, 'sales_monthly_display'])->middleware('auth:sanctum');
Route::get('/home', [ownerShopController::class, 'home_display'])->middleware('auth:sanctum');
Route::get('/appbar', [ownerShopController::class, 'appbar_display'])->middleware('auth:sanctum');

//Route::get('/booking/bar/{id}', [testingController::class, 'bar_test']);
Route::get('/booking/charts/{id}', [testingController::class, 'bar_test2']);
Route::post('/logout', [authController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/settings', [ownerShopController::class, 'settings_display'])->middleware('auth:sanctum');
Route::put('/settings/profile/update/{id}', [ownerShopController::class, 'owner_user_update'])->middleware('auth:sanctum');
Route::post('/book/walkin', [ownerShopController::class, 'walk_in'])->middleware('auth:sanctum');
Route::get('/book/walkin/display', [ownerShopController::class, 'walk_in_display'])->middleware('auth:sanctum');
Route::put('/book/walkin/update/{id}', [ownerShopController::class, 'walkin_status'])->middleware('auth:sanctum');
Route::post('/reports', [ownerShopController::class, 'report_display'])->middleware('auth:sanctum');
Route::get('/customers', [ownerShopController::class, 'customer_display'])->middleware('auth:sanctum');
Route::post('/book/registered', [ownerShopController::class, 'registered'])->middleware('auth:sanctum');
Route::put('/bookings/update/{id}', [ownerShopController::class, 'booking_status'])->middleware('auth:sanctum');
Route::get('/wash/display', [ownerShopController::class, 'wash_display'])->middleware('auth:sanctum');
Route::get('/dry/display', [ownerShopController::class, 'dry_display'])->middleware('auth:sanctum');
Route::get('/fold/display', [ownerShopController::class, 'fold_display'])->middleware('auth:sanctum');
Route::get('/pickup/display', [ownerShopController::class, 'pickup_display'])->middleware('auth:sanctum');
Route::get('/complete/display', [ownerShopController::class, 'complete_display'])->middleware('auth:sanctum');
Route::put('/payment/update/{id}', [ownerShopController::class, 'payment_update'])->middleware('auth:sanctum');
Route::put('/complete/update/{id}', [ownerShopController::class, 'complete_update'])->middleware('auth:sanctum');
Route::put('/shop/update', [ownerShopController::class, 'shop_info_update'])->middleware('auth:sanctum');

//TESTING ROUTES--------------------------------------------------------------------------------------------
Route::post('/picture', [testingController::class, 'picture']);
Route::get('/images/{id}', [testingController::class, 'getImage']);
Route::post('/sms', [testingController::class, 'send_sms']);

Route::get('/test/data', [testingController::class, 'test_data']);














/*-----------------------------------------------------------------------------------*/
Route::get('/shop-request/display', [CustomerController::class, 'added_shops_display'])->middleware('auth:sanctum');
Route::post('/shop-request', [CustomerController::class, 'request_shops'])->middleware('auth:sanctum');
Route::post('/shop/display', [CustomerController::class, 'request_shops_display'])->middleware('auth:sanctum');
Route::post('/laundry/status/display', [CustomerController::class, 'laundry_display'])->middleware('auth:sanctum');
Route::get('/laundry/summary/{id}', [CustomerController::class, 'summary_display'])->middleware('auth:sanctum');
Route::post('/laundry/cancellation/{bookId}', [CustomerController::class, 'laundry_cancellation'])->middleware('auth:sanctum');
Route::post('/laundry/completion/{bookId}', [CustomerController::class, 'laundry_completion'])->middleware('auth:sanctum');
Route::post('/laundry/review/submit', [CustomerController::class, 'submit_rating'])->middleware('auth:sanctum');
Route::post('/laundry/review', [CustomerController::class, 'view_rating'])->middleware('auth:sanctum');

Route::get('/laundry-service/{shopid}', [CustomerController::class, 'laundry_service'])->middleware('auth:sanctum');
Route::post('/laundry-service/avail', [CustomerController::class, 'avail_laundry_service'])->middleware('auth:sanctum');

Route::get('/laundry/notifications', [CustomerController::class, 'laundry_notification'])->middleware('auth:sanctum');
Route::post('/laundry/notifications/read/{notifid}', [CustomerController::class, 'laundry_notification_read'])->middleware('auth:sanctum');
Route::get('/customer/profile', [CustomerController::class, 'customer_profile'])->middleware('auth:sanctum');
Route::get('/customer/profile/update', [CustomerController::class, 'customer_profile'])->middleware('auth:sanctum');