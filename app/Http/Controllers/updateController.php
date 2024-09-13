<?php

namespace App\Http\Controllers;
use App\Models\tbl_customer;
use App\Models\tbl_owner;
use App\Models\tbl_inventory;
use App\Models\tbl_shop_machine;
use App\Models\tbl_shop_service;
use App\Models\tbl_laundry_service;
use App\Models\tbl_shop;
use App\Models\tbl_billing;
use App\Models\tbl_booking;
use Illuminate\Http\Request;

class updateController extends Controller
{
    public function customer_info_update(Request $request, $id){
        $customer = tbl_customer::find($id);

        if(!$customer){
            return response([
                'message' => 'Customer not found'
            ], 403);
        }

        $info = $request->validate([
            'Name' => 'string',
            'Sex' => 'string',
            'Address' => 'string',
            'ContactNumber' => 'integer',
            'Email' => 'email',
            'Image' => 'image'
        ]);

        $customer->update([
            'Name' => $info['cusName'],
            'Sex' => $info['sex'],
            'Address' => $info['address'],
            'ContactNumber' => $info['cn'],
            'Email' => $info['email'],
            'Image' => $info['img']
        ]);
    }

    public function owner_info_update(Request $request, $id){
        $owner = tbl_owner::find($id);

        if(!$owner){
            return response([
                'message' => 'Owner not found'
            ], 403);
        }

        $info = $request->validate([
            'Name' => 'string',
            'Sex' => 'string',
            'Address' => 'string',
            'ContactNumber' => 'integer',
            'Email' => 'email',
            'Image' => 'image'
        ]);

        $owner->update([
            'Name' => $info['cusName'],
            'Sex' => $info['sex'],
            'Address' => $info['address'],
            'ContactNumber' => $info['cn'],
            'Email' => $info['email'],
            'Image' => $info['img']
        ]);
    }

    public function requested_shop_update(Request $request, $id){
        $add_shop = tbl_inventory::find($id);

        if(!$add_shop){
            return response([
                'message' => 'Shop not found'
            ], 403);
        }

        $add_shop->update([
           'IsValued' => 1
        ]);
    }

    public function inventory_update(Request $request, $id){
        $invent = tbl_inventory::find($id);

        if(!$invent){
            return response([
                'message' => 'Inventory not found'
            ], 403);
        }

        $info = $request->validate([
            'ItemName' => 'string',
            'ItemQty' => 'integer',
            'ItemVolume' => 'integer',
        ]);

        $invent->update([
            'ItemName' => $info['itmName'],
            'ItemQty' => $info['itmQty'],
            'ItemVolume' => $info['itmVol'],
        ]);
    }

    public function machine_update(Request $request, $id){
        $mach = tbl_shop_machine::find($id);

        if(!$mach){
            return response([
                'message' => 'Machine not found'
            ], 403);
        }

        $info = $request->validate([
            'WasherQty' => 'integer',
            'WasherTime' => 'integer',
            'DryerQty' => 'integer',
            'DryerTime' => 'integer',
        ]);

        $mach->update([
            'WasherQty' => $info['washQty'],
            'WasherTime' => $info['washTime'],
            'DryerQty' => $info['dryQty'],
            'DryerTime' => $info['dryTime'],
        ]);
    }

    public function shop_service_update(Request $request, $id){
        $shop_service = tbl_shop_service::find($id);

        if(!$shop_service){
            return response([
                'message' => 'Shop service not found'
            ], 403);
        }

        $info = $request->validate([
            'ServiceName' => 'string',
            'Description' => 'string',
            'LoadWeight' => 'integer',
            'LoadPrice' => 'integer'
        ]);

        $shop_service->update([
            'ServiceName' => $info['serveName'],
            'Description' => $info['desc'],
            'LoadWeight' => $info['loadWeight'],
            'LoadPrice' => $info['loadPrice']
        ]);
    }

    public function shop_laundry_service(Request $request, $id){
        $laundry_service = tbl_laundry_service::find($id);

        if(!$laundry_service){
            return response([
                'message' => 'Laundry Service not found'
            ], 403);
        }

        $info = $request->validate([
            'IsSelfService' => 'integer',
            'SelfServiceDeduct' => 'integer',
            'IsFullService' => 'integer',
        ]);

        $laundry_service->update([
            'IsSelfService' => $info['selfServe'],
            'SelfServiceDeduct' => $info['serveDed'],
            'IsFullService' => $info['fullService'],
        ]);
    }

    public function shop_info_update(Request $request, $id){
        $shop = tbl_shop::find($id);

        if(!$shop){
            return response([
                'message' => 'Shop not found'
            ], 403);
        }

        $info = $request->validate([
            'ShopName' => 'integer',
            'ShopAddress' => 'integer',
            'MaxLoad' => 'integer',
            'WorkDay' => 'string',
            'WorkHour' => 'string',
            'ShopStatus' => 'string'
        ]);

        $shop->update([
            'ShopName' => $info['shopName'],
            'ShopAddress' => $info['shopAdd'],
            'MaxLoad' => $info['maxLoad'],
            'WorkDay' => $info['workDay'],
            'WorkHour' => $info['Shop']
        ]);
    }

    public function shop_status_update(Request $request, $id){
        $stat = tbl_shop::find($id);
        $time = '';
        $date = '';
        $maxload = '';

        if(!$stat){
            return response([
                'message' => 'Shop not found'
            ], 403);
        }

        if($time != 'column_workhour' || $date != 'column_workdate'){
            $stat->update([
                'ShopStatus' => 'closed'
            ]);
        }

        if($maxload == 0){
            $stat->update([
                'ShopStatus' => 'full'
            ]);
        }

        
    }

    public function billing_update(Request $request, $id){
        $bill = tbl_billing::find($id);

        if(!$bill){
            return response([
                'message' => 'Shop not found'
            ], 403);
        }

        $info = $request->validate([
            'Amount' => 'integer',
            'PaymentStatus' => 'string'
        ]);

        $bill->update([
            'Amount' => $info['payAmt'],
            'PaymentStatus' => $info['payStat']
        ]);
        
    }

    public function booking_update(Request $request, $id){
        $book = tbl_booking::find($id);
        if(!$book){
            return response([
                'message' => 'Shop not found'
            ], 403);
        }

        $info = $request->validate([
            'Status' => 'integer'
        ]);
        
        $book->update([
            'Status' => $info['stat']
        ]);
    }
}
