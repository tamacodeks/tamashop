<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\PaymentReceived' => [
            'App\Listeners\SendPaymentReceivedNotification',
            'App\Listeners\SendPaymentSlackNotification',
        ],
        'App\Events\CallingCardPinUpload' => [
            'App\Listeners\CCUploadEmailNotification',
            'App\Listeners\CCUploadSlackNotification',
        ],
        'App\Events\CallingCardPinPrint' => [
            'App\Listeners\CCPrintSlackNotification',
        ],
        'App\Events\RateTablePush' => [
            'App\Listeners\PushPriceList',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
