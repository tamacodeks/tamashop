<?php

namespace App\Listeners;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class PushPriceList implements ShouldQueue
{

    private $guzzleClient;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $rate_table = $event->rateTable;
        $user_id = $event->userID;
        $userInfo = User::find($user_id);
        $this->guzzleClient = new Client([
            'base_uri' => $userInfo->web_hook_url,
            'timeout'  => 120,
        ]);
        try{
            $params = [
                'cc_id' => $rate_table->cc_id,
                'price' => $rate_table->sale_price,
            ];
            $response = $this->guzzleClient->request('POST', $userInfo->web_hook_uri,[
                'headers'  => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer ". $userInfo->web_hook_token
                ],
                'form_params' => $params
            ]);
            if($response->getStatusCode() == 200){
                Log::info("Rate Table has been updated, params ",$params);
            }else{
                Log::warning("Unable to update the Rate Table Price in client web hook");
            }
        }catch (\Exception $e){
            Log::warning("Exception while update the job rate Table Push ".$e->getMessage());
        }
    }
}
