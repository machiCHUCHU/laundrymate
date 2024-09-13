<?php

namespace App\Http\Controllers;
use App\Models\tbl_customer;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class smsController extends Controller
{
    public function sms(){
        $basic  = new \Vonage\Client\Credentials\Basic("d42154ce", "gyx9BiXO291pmwfM");
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS("639353551807", 'JowaFinder', 'Wala kang jowa')
        );
        
        $message = $response->current();
        
        if ($message->getStatus() == 0) {
            echo "The message was sent successfully\n";
        } else {
            echo "The message failed with status: " . $message->getStatus() . "\n";
        }

        return response([
            'response' => 'The message was sent successfully'
        ], 200);
    }

    public function verify_sms(){
        $basic  = new \Vonage\Client\Credentials\Basic("d42154ce", "gyx9BiXO291pmwfM");
        $client = new \Vonage\Client(new \Vonage\Client\Credentials\Container($basic));

        $request = new \Vonage\Verify\Request('639655543516', "Vonage");
        $response = $client->verify()->start($request);
        $reqId = $response->getRequestId();
        echo "Started verification, `request_id` is " . $response->getRequestId();
        
        $result = $client->verify()->check($reqId, '2698');
        var_dump($result->getResponseData());
    }

    public function code_verify(){
        $basic  = new \Vonage\Client\Credentials\Basic("d42154ce", "gyx9BiXO291pmwfM");
        $client = new \Vonage\Client(new \Vonage\Client\Credentials\Container($basic));
        
        try {   
            $result = $client->verify()->check('', 4609);
        } catch (\Exception $e) {
            var_dump($result->getResponseData());
        }
    }

}
