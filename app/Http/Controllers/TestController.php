<?php

namespace App\Http\Controllers;

use app\Library\AppHelper;

class TestController extends Controller
{
    function index()
    {
//        dd("You cannot access this!");
        $otp = rand(1000, 9999);
        $message = 'Hello Testing Greetings From '.APP_NAME.' Your Verification Code is ' . $otp;
        $result = AppHelper::sendSms('33753702042', $message);
        dd($result);
    }


}
