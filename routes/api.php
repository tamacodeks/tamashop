<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('docs', function(Request $request){
    return view('common.api_doc');
});


Route::get('test', function(Request $request){
    return $request->user();
})->middleware('auth:api');

Route::group(['middleware' => ['auth:api']], function () {
    //get balance and credit limit
    Route::get('heartbeat','Api\CommonController@getHeartBeat');

    //get telecom providers
    Route::get('telecom-providers','Api\TelecomProviderController@getTelecomProviders');

    //get calling cards for the selected telecom-provider
    Route::get('calling-cards/{id}','Api\CallingCardController@getCallingCards');
    Route::get('calling-cards/print/{id}','Api\CallingCardController@getPrintCard');
    Route::post('calling-cards/confirm','Api\CallingCardController@confirmPrint');

    Route::get('rates','Api\RatesController@getRates');

    Route::post('/webhook/services',"Api\WebHookController@updateServices");

});
Route::post('/payment', 'App\PaymentController@stripePost');
Route::get('/save_payments', 'App\PaymentController@save_Payment');
Route::post('/order/status/update-callback',"Api\WebHookController@updateOrderStatus");


Route::post('/payment/create', 'App\PaymentController@createPaymentIntent');
Route::post('/payment/retrieve', 'App\PaymentController@retrievePaymentDetails');
Route::post('/payment/save', 'App\PaymentController@savePaymentDetails');





