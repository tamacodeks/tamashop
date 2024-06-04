<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use app\Library\AppHelper;
use App\Models\Transaction;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maknz\Slack\Facades\Slack;

class SendPaymentSlackNotification implements ShouldQueue
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
     * @param  PaymentReceived  $event
     * @return void
     */
    public function handle(PaymentReceived $event)
    {
        if(ENABLE_SLACK == 1){
            $payment = $event->payment;
            $transaction = Transaction::find($payment->transaction_id);
            $iso_code = optional(User::find($payment->user_id))->currency;
            $retailer_name = optional(User::find($payment->user_id))->username;
            $received_by = optional(User::find($payment->received_by))->username;
            $slack_data = [
                [
                    'title' => 'Retailer Name',
                    'value' => $retailer_name
                ],
                [
                    'title' => 'Amount',
                    'value' => AppHelper::formatAmount($iso_code, $payment->amount),
                ],
                [
                    'title' => 'Comment',
                    'value' => $payment->description
                ],
                [
                    'title' => 'Received By',
                    'value' => $received_by,
                    'short' => true
                ]
            ];
            Slack::attach([
                'fallback' => "User Payment Update",
                'text' => "User Payment Update",
                'color' => '#3AA3E3',
                'fields' => $slack_data
            ])->send('TamaExpress'); // no message, but can be provided if you'd like
            Log::info('slack notification sent');
        }
    }
}
