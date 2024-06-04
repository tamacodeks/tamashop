<?php

namespace App\Listeners;

use App\Events\CallingCardPinUpload;
use app\Library\AppHelper;
use App\Models\CallingCard;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maknz\Slack\Facades\Slack;

class CCUploadSlackNotification  implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CallingCardPinUpload  $event
     * @return void
     */
    public function handle(CallingCardPinUpload $event)
    {
        if(ENABLE_SLACK == 1) {
            $cc_upload_data = $event->upload;
            $user_info = User::find($cc_upload_data->uploaded_by);
            $provider_info = CallingCard::join('telecom_providers', 'telecom_providers.id', 'calling_cards.telecom_provider_id')
                ->where('calling_cards.id', $cc_upload_data->cc_id)
                ->select('telecom_providers.name as provider_name', 'calling_cards.name as name')
                ->first();
            $slack_data = [
                [
                    'title' => 'Retailer Name',
                    'value' => $user_info->username
                ],
                [
                    'title' => 'Card Name',
                    'value' => optional($provider_info)->name,
                    'short' => true
                ],
                [
                    'title' => 'Buying Price',
                    'value' => AppHelper::formatAmount("EUR", $cc_upload_data->buying_price),
                ],
                [
                    'title' => 'Total Pin',
                    'value' => $cc_upload_data->no_of_pins
                ]
            ];
            Slack::attach([
                'fallback' => "Calling Card Pins Upload",
                'text' => "Calling Card Pins Upload",
                'color' => '#3AA3E3',
                'fields' => $slack_data
            ])->send('TamaExpress'); // no message, but can be provided if you'd like
        }
    }
}
