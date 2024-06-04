<?php
namespace App\Http\Controllers\Bimedia;

use App\Http\Controllers\Controller;

class BaseSoapController extends Controller
{
    protected static $options;
    protected static $context;
    protected static $wsdl;

    public function __construct() {
    }

    public static function setWsdl($service) {
        return self::$wsdl = $service;
    }

    public static function getWsdl(){
        return self::$wsdl;
    }
    public static function getLocation(){
        return self::setWsdl(config('bimedia.web_service_location'));
    }


    protected static function generateContext(){

        self::$options = [
            'http' => [
                'user_agent' => 'IPCARTEClient',
                'header' => 'Authorization:Basic '.AUTHORIZATION
            ],
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        ];
        return self::$context = stream_context_create(self::$options);
    }

    public function loadXmlStringAsArray($xmlString)
    {
        $array = (array) @simplexml_load_string($xmlString);
        if(!$array){
            $array = (array) @json_decode($xmlString, true);
        } else{
            $array = (array)@json_decode(json_encode($array), true);
        }
        return $array;
    }
}
