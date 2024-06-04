<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyTamaMasterRetailerOrdersReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:TamaMasterRetailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Orders report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $child = \App\User::where('group_id',2)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $today_date = date("Y-m-d",strtotime("yesterday"));
            $query  = \App\Models\Order::join('users','users.id','orders.user_id')
                ->join('transactions','transactions.id','orders.transaction_id')
                ->select([
                    \DB::raw('count(transactions.user_id) AS total_transaction'),
                    \DB::raw('transactions.prev_bal AS prev_bal'),
                    \DB::raw("SUM(orders.buying_price) as total_amount")
                ])
                ->whereIn('users.id',$retailers)
                ->where('transactions.type','debit')
                ->where('orders.service_id', '=', '2')
                ->where('orders.is_parent_order', '=', '1')
                ->where('orders.exclude', '=', '1')
                ->whereBetween('transactions.date',[$today_date." 00:00:00",$today_date." 23:59:59"])
                ->get();
            $emails_tmp = "sydkhalid7@gmail.com,sivaguru.nagapoulle@tamaexpress.com,karim@kdoexpress.net,balaji@prepaysolution.in";
            $emails = explode(',', $emails_tmp);
            $email_data = array(
                'user_data' => $query
            );
            \Mail::send('emails.reports.master_retailer', $email_data, function ($message) use ($emails) {
                $message->to($emails)->subject('Tama Orders Report');
            });
            Log::info("Tama Generated Report");
        } catch (\Exception $exception) {
            Log::warning("Tama Daily Payments Report Exception => " . $exception->getMessage());
        }
    }
}
