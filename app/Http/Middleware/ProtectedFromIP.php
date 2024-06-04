<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Illuminate\Support\Facades\Log;
use Closure;
use Auth;

class ProtectedFromIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		try {
        $ip = AppHelper::getIP(true);
        $ip_address = AppHelper::iplocation($ip);
        //dd($ip_address);
          $code = $ip_address['geoplugin_countryCode'];
        $slack_data = [
            'Ip Address' =>  $ip_address['geoplugin_request'],
            'Country Code' => $ip_address['geoplugin_countryCode'],
            'Region' => $ip_address['geoplugin_regionName'],
			'City' => $ip_address['geoplugin_city']
        ];
//        $code = $ip_address['country_code'];
//        $slack_data = [
//            'Ip Address' =>  $ip_address['ip'],
//            'Country Code' => $ip_address['country_code'],
//            'Region' => $ip_address['region_name'],
//            'City' => $ip_address['city']
//        ];
        if(!in_array($code,['FR','IN','MA','AE'])){
            Log::emergency("We'r Being Attack On ".APP_NAME." (Carte) From This Country ".json_encode($slack_data));
            Auth::logout();
            return $next($request);
        }
        return $next($request);
		 }
        catch (\Exception $e) {
            return $next($request);
        }
    }
}
