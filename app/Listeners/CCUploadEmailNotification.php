<?php

namespace App\Listeners;

use App\Events\CallingCardPinUpload;
use app\Library\AppHelper;
use App\Models\CallingCard;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CCUploadEmailNotification  implements ShouldQueue
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
        $cc_upload_data = $event->upload;
        if(ENABLE_EMAIL == 1){
            $emails = ['prabakaran@prepaysolution.in'];
            $user_info = User::find($cc_upload_data->uploaded_by);
            $provider_info = CallingCard::join('telecom_providers','telecom_providers.id','calling_cards.telecom_provider_id')
                ->where('calling_cards.id',$cc_upload_data->cc_id)
                ->select('telecom_providers.name as provider_name','calling_cards.name as name')
                ->first();
            $send_email_data = array(
                'manager_name' => $user_info->username,
                'total_pins' => $cc_upload_data->no_of_pins,
                'buying_price' => $cc_upload_data->buying_price,
                'provider_name' => optional($provider_info)->provider_name,
                'card_name' => optional($provider_info)->name
            );
            try{
                Mail::send('emails.pin_uploads', $send_email_data, function($message) use ($emails,$send_email_data)
                {
                    $message->to($emails)->from('noreply@tamaexpress.com', 'TamaExpress Reseller System')->subject("New MyService Pins added by " . $send_email_data['manager_name']);
                });
                AppHelper::logger('success','New Pins Upload Notification',"Email has been successfully!");
            }catch (\Exception $e){
                AppHelper::logger('warning','Pins Upload  Email Exception',$e->getMessage());
            }
        }

    }
}
