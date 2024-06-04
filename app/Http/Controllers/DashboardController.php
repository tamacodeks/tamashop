<?php

namespace App\Http\Controllers;

use app\Library\AppHelper;
use App\Models\Order;
use App\Models\Transaction;
use App\User;
use App\Models\Banner;
use Illuminate\Http\Request;
use Carbon\Carbon;
class DashboardController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index(Request $request)
    {
        //total users
        $child = User::where('id', auth()->user()->id)->with('children')->first();
        $retailers = $child->children->pluck('id')->flatten()->toArray();
        $to_retrieve = $retailers;
        $total_user_query = User::select('id');
        if (auth()->user()->group_id == 2) {
            $total_user_query->whereIn('users.id', $to_retrieve);
        }elseif (auth()->user()->group_id == 3) {
            $total_user_query->whereIn('users.id', $to_retrieve);
        } else {
            $total_user_query->where(function ($query) {
                $query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            });
        }
        $total_user_query->where('status',1); //added only active users
        //total orders
        $orders = Order::join('order_items', 'order_items.id', '=', 'orders.order_item_id')
            ->join('users', 'users.id', 'orders.user_id')
            ->join('order_status', 'order_status.id', 'orders.order_status_id')
            ->join('services', 'services.id', 'orders.service_id');
        switch (DEFAULT_RECORD_METHOD) {
            case 1:
                break;
            case 2:
                $orders->whereMonth('orders.date', date('m'));
                break;
            case 3:
                $orders->whereBetween('orders.date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
        }
        if (auth()->user()->group_id == 2) {
            $orders->whereIn('users.id', $to_retrieve);
            $orders->where('orders.is_parent_order', '=', '1');
        }elseif (auth()->user()->group_id == 3) {
            $orders->whereIn('users.id', $to_retrieve);
            $orders->where('orders.is_parent_order', '=', '1');
        }elseif(auth()->user()->group_id == 4){
            $orders->whereIn('users.id',[auth()->user()->id]);
            $orders->where('orders.is_parent_order', '=', '0');
        } elseif(auth()->user()->group_id == 6){
            $orders->where('orders.service_id',1);
            $orders->where('orders.is_parent_order', '=', '0');
        } else {
            $orders->where(function ($query) {
                $query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            });
            $orders->where('orders.is_parent_order', '=', '0');
        }
        $orders->select([
            'orders.id',
            'orders.date',
            'users.id as user_id',
            'users.username',
            'services.id as service_id',
            'services.name as service_name',
            'orders.public_price',
            'orders.order_amount',
            'orders.grand_total',
            'orders.is_parent_order',
            'orders.order_item_id',
            'order_status.name as order_status_name',
        ])->orderBy('orders.id',"DESC");
        //transactions
        $today_date = date("Y-m-d");
        $today_trans_amount = Order::join('order_items', 'order_items.id', '=', 'orders.order_item_id')
            ->join('users', 'users.id', 'orders.user_id')
            ->join('order_status', 'order_status.id', 'orders.order_status_id')
            ->join('services', 'services.id', 'orders.service_id')->whereIn('users.id', $to_retrieve)->where('orders.is_parent_order', '=', '1')->whereBetween('orders.date', [$today_date." 00:00:00",$today_date." 23:59:59"])->sum('order_amount');
        $total_trans_amount = Order::join('order_items', 'order_items.id', '=', 'orders.order_item_id')
            ->join('users', 'users.id', 'orders.user_id')
            ->join('order_status', 'order_status.id', 'orders.order_status_id')
            ->join('services', 'services.id', 'orders.service_id')->whereIn('users.id', $to_retrieve)->where('orders.is_parent_order', '=', '1')->whereYear('orders.date', date('Y'))->whereMonth('orders.date', date('m'))->sum('order_amount');
        if(auth()->user()->group_id == 4)
        {
            $today_trans_amount = Order::join('order_items', 'order_items.id', '=', 'orders.order_item_id')
                ->join('users', 'users.id', 'orders.user_id')
                ->join('order_status', 'order_status.id', 'orders.order_status_id')
                ->join('services', 'services.id', 'orders.service_id')->whereIn('users.id',[auth()->user()->id])->where('orders.is_parent_order', '=', '0')->whereBetween('orders.date', [$today_date." 00:00:00",$today_date." 23:59:59"])->sum('order_amount');
        }
        //products sale statistics
        $send_tama_product = [];
        $tamatopup_product = [];
        //backoffice
        $orders_in_progress = 0;
        $closed_orders = 0;
        if(auth()->user()->group_id == 6){
            $orders_in_progress = Order::whereIn('order_status_id',[1,2,3,4])->count();
            $closed_orders = Order::whereIn('order_status_id',[5])->count();
        }
        if(auth()->user()->group_id == 1 || auth()->user()->group_id ==2 ){
            $banner = Banner::where('user_id',auth()->user()->id)->whereDate('to_date', '>=', date("Y-m-d"))->get();
        }
        else{
            $users = User::where('id' , auth()->user()->parent_id)->first();
            $banner = Banner::wherein('user_id',[$users->parent_id,auth()->user()->parent_id])->whereDate('to_date', '>=', date("Y-m-d"))->get();
        }
        $page_data = [
            'page_title' => "Dashboard",
            'total_resellers' => $total_user_query->count(),
            'total_orders' => $orders->whereBetween('orders.date', [date('Y-m-d')." 00:00:00", date("Y-m-d")." 23:59:59"])->count(),
            'orders' => $orders->take(10)->get(),
            'today_transaction' => AppHelper::formatAmount('EUR',$today_trans_amount),
            'total_transaction' => AppHelper::formatAmount('EUR',$total_trans_amount),
            'top_sales_te' => $send_tama_product,
            'top_sales_tt' => $tamatopup_product,
            'orders_in_progress' => $orders_in_progress,
            'closed_orders' => $closed_orders,
            'banner' => $banner
        ];
        return view('dashboard', $page_data);
    }
}
