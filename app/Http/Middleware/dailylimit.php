<?php

namespace App\Http\Middleware;

use app\Library\AppHelper;
use Closure;
use Auth;

class dailylimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    if (in_array(auth()->user()->group_id, [4])) {
        $r_bal = (\app\Library\AppHelper::get_remaning_limit_balance(auth()->user()->id));
        $daily_limit = (\app\Library\AppHelper::get_daily_limit(auth()->user()->id));
        $getBalance = (\app\Library\AppHelper::getBalance(auth()->user()->id,auth()->user()->currency, false));
        $blink_limit = str_replace('-', '', $r_bal);
            $manager_id =(auth()->user()->parent_id);
        if($blink_limit <= 10 )
        {

            if($manager_id != '')
            {
                $result = \app\User::where('id', $manager_id)->orderBy('id', 'DESC')->first();
                $emails = [$result->email,'balaji@prepaysolution.in'];
            }
            else
            {
                $result = \app\User::where('id', 1)->orderBy('id', 'DESC')->first();
                $emails = [$result->email];
            }
//            dd($result->email);
            $send_email_data = array(
                'retailer_name' => auth()->user()->username,
                'manager_name' => $result->username,
                'current_bal' => $getBalance,
                'total_limit' => $daily_limit,
                'current_limit' => $blink_limit,
            );
            \Mail::send('emails.daily_limit_alert', $send_email_data, function ($message) use ($emails) {
                $message->from('noreply@tamaexpress.com', 'Tama Retailer');
                $message->to($emails)->subject('Tama Daily Limit Alert');
            });
            return $next($request);
        }
    }
    return $next($request);
    }
}
