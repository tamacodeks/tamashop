<?php

namespace App\Http\Controllers\App;

use app\Library\AppHelper;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PinHistory;
use App\Models\Product;
use App\Models\Service;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index(Request $request){
        $from_date = !empty($request->input('from')) ? $request->input('from') : "";
        $to_date = !empty($request->input('to')) ? $request->input('to') : "";
        $page_data = [
            'page_title' =>  trans('common.breadcrumb_trans_history'),
            'services' => Service::select('id','name')->where('status','1')->get(),
            'from_date' => $from_date,
            'to_date' => $to_date
        ];
        return view('app.transactions.index',$page_data);
    }

    function getTransactions(Request $request){
        $query = Order::join('users','users.id','orders.user_id')
            ->join('order_status','order_status.id','orders.order_status_id')
            ->join('services','services.id','orders.service_id')
            ->select([
                'orders.id',
                'orders.date',
                'users.id as user_id',
                'users.cust_id as cust_id',
                'users.username',
                'services.id as service_id',
                'services.name as service_name',
                'orders.public_price',
                'orders.buying_price',
                'orders.sale_margin',
                'orders.order_amount',
                'orders.is_parent_order',
                'orders.order_item_id',
                'orders.txn_ref as txn_id',
                'orders.grand_total',
                'orders.sur_charge',
                'order_items.product_id',
                'order_items.sender_first_name',
                'order_items.sender_mobile',
                'order_items.receiver_first_name',
                'order_items.receiver_mobile',
                'order_items.tt_mobile',
                'order_items.tt_operator',
                'order_items.app_mobile',
                'order_items.app_amount_topup',
                'order_items.app_currency',
                'order_items.tama_pin',
                'order_items.tama_serial',
                'order_items.instructions',
                'order_items.link',
                'order_status.name as order_status_name',
            ]);
        if(auth()->user()->group_id == 2){
            $query->join('order_items','order_items.id','orders.order_item_id');
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $query->whereIn('users.id',$retailers);
            $query->where('orders.is_parent_order', '=', '1');
        }elseif(auth()->user()->group_id == 3){
            $query->join('order_items','order_items.id','orders.order_item_id');
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $query->whereIn('users.id',$retailers);
            $query->where('orders.is_parent_order', '=', '1');
        }elseif(auth()->user()->group_id == 4){
            $query->join('order_items','order_items.order_id','orders.id');
            $query->where('users.id',auth()->user()->id);
            $query->where('orders.is_parent_order', '=', '0');
        }else{
            $query->join('order_items','order_items.order_id','orders.id');
            $query->where('users.id',auth()->user()->id);
            $query->where('orders.is_parent_order', '=', '0');
        }
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            $today_date = date("Y-m-d");
            switch (DEFAULT_RECORD_METHOD){
                case 1:
                    $query->whereBetween('orders.date', [$today_date." 00:00:00",$today_date." 23:59:59"]);
                    break;
                case 2:
                    $query->whereMonth('orders.date',date('m'));
                    break;
                case 3:
                    $query->whereBetween('orders.date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                    break;
            }
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('orders.date',[$from_date,$to_date]);
        }
        $orders = $query;
        return Datatables::of($orders)
            ->addColumn('product_name', function ($orders) {
                if($orders->service_id == 1){
                    return optional(Product::find($orders->product_id))->name;
                }
                if($orders->service_id == 5){
                    return $orders->service_name.' '.AppHelper::formatAmount($orders->app_currency,$orders->app_amount_topup);
                }
                if($orders->service_id == 2 || $orders->service_id == 7){
                    $tt_op = OrderItem::find($orders->order_item_id);
                    return $orders->tt_operator == null ? optional($tt_op)->tt_operator :  $orders->tt_operator;
                }
                $iso_code = optional(User::find($orders->user_id))->currency;
                $price = $orders->public_price == "0.00" ? $orders->grand_total : $orders->public_price;
                return $orders->service_name.' '.AppHelper::formatAmount($iso_code,$price);
            })
            ->addColumn('public_price', function ($orders){
                return $orders->public_price;
            })
            ->addColumn('pin', function ($orders){
                if($orders->service_id == 7){
                    $pin_history = PinHistory::where('used_by',$orders->user_id)->where("date",$orders->date)->first();
                    return optional($pin_history)->pin;
                }else{
                    return $orders->tama_pin;
                }
            })
            ->addColumn('serial', function ($orders){
                if($orders->service_id == 7){
                    $pin_history = PinHistory::where('used_by',$orders->user_id)->where("date",$orders->date)->first();
                    return optional($pin_history)->serial;
                }else{
                    return $orders->tama_serial;
                }
            })
            ->addColumn('buying_price', function ($orders){
                return $orders->buying_price;
            })
            ->addColumn('order_amount', function ($orders){
                return $orders->order_amount;
            })
            ->addColumn('sale_margin', function ($orders){
                return $orders->sale_margin;
            })
            ->addColumn('mobile', function ($orders){
                if($orders->service_id == 1){
                    return $orders->receiver_mobile;
                }elseif ($orders->service_id == 2){
                    return $orders->tt_mobile;
                }else{
                    return $orders->app_mobile;
                }
            })
            ->filter(function ($query) use ($request) {
                if($request->service_id[0] == '101'){
                    if (!empty($request->input('service_id'))) {
                        $query->where('services.id','7');
                    }
                    if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                        $from_date = $request->input('from_date').' 00:00:00';
                        $to_date = $request->input('to_date').' 23:59:59';
                        $query->whereBetween('orders.date',[$from_date,$to_date]);
                    }
                    $qry = 'Lyca';
                    $query->Where(function ($q) use ($qry) {
                        $q->orWhere('order_items.tt_operator', "like", "%PrepaidCashService%");
                        $q->orWhere('order_items.tt_operator', "like", "%Google%");
                        $q->orWhere('order_items.tt_operator', "like", "%Paysafecard %");
                    });
                } elseif($request->service_id[0] == '7'){
                    if (!empty($request->input('service_id'))) {
                        $query->where('services.id','7');
                    }
                    if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                        $from_date = $request->input('from_date').' 00:00:00';
                        $to_date = $request->input('to_date').' 23:59:59';
                        $query->whereBetween('orders.date',[$from_date,$to_date]);
                    }
                    $qry = 'Lyca';
                    $query->Where(function ($q) use ($qry) {
                        $q->Where('order_items.sender_first_name', "like", "%{$qry}%");
                        $q->orWhere('order_items.receiver_first_name', "like", "%{$qry}%");
                        $q->orWhere('order_items.sender_mobile', "like", "%{$qry}%");
                        $q->orWhere('order_items.receiver_mobile', "like", "%{$qry}%");
                        $q->orWhere('order_items.tt_mobile', "like", "%{$qry}%");
                        $q->orWhere('order_items.tt_operator', "like", "%{$qry}%");
                        $q->orWhere('order_items.app_mobile', "like", "%{$qry}%");
                        $q->orWhere('orders.txn_ref', "like", "%{$qry}%");
                        $q->orWhere('users.username', "like", "%{$qry}%");
                        $q->orWhere('order_items.tt_operator', "not like", ["%Paysafecard %","%Google%","%PrepaidCashService%"]);
                    });
                }else{

                    if (!empty($request->input('query'))) {
                        $qry = $request->input('query');
                        $query->Where(function ($q) use ($qry) {
                            $q->Where('order_items.sender_first_name', "like", "%{$qry}%");
                            $q->orWhere('order_items.receiver_first_name', "like", "%{$qry}%");
                            $q->orWhere('order_items.sender_mobile', "like", "%{$qry}%");
                            $q->orWhere('order_items.receiver_mobile', "like", "%{$qry}%");
                            $q->orWhere('order_items.tt_mobile', "like", "%{$qry}%");
                            $q->orWhere('order_items.tt_operator', "like", "%{$qry}%");
                            $q->orWhere('order_items.app_mobile', "like", "%{$qry}%");
                            $q->orWhere('orders.txn_ref', "like", "%{$qry}%");
                            $q->orWhere('users.username', "like", "%{$qry}%");
                        });
                    }
                    if (!empty($request->input('service_id'))) {
                        $query->whereIn('services.id',$request->input('service_id'));
                    }
                    if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                        $from_date = $request->input('from_date').' 00:00:00';
                        $to_date = $request->input('to_date').' 23:59:59';
                        $query->whereBetween('orders.date',[$from_date,$to_date]);
                    }

                }
            })
            ->make(true);
    }
    function failed_transaction(Request $request){
        $from_date = !empty($request->input('from')) ? $request->input('from') : "";
        $to_date = !empty($request->input('to')) ? $request->input('to') : "";
        $page_data = [
            'page_title' =>  trans('common.breadcrumb_trans_history'),
            'services' => Service::select('id','name')->where('status','1')->get(),
            'from_date' => $from_date,
            'to_date' => $to_date
        ];
        return view('app.transactions.failed_transaction',$page_data);
    }

    function getfailed_transaction(Request $request){
        $query = Order::join('users','users.id','orders.user_id')
            ->join('order_status','order_status.id','orders.order_status_id')
            ->join('services','services.id','orders.service_id')
            ->select([
                'orders.id',
                'orders.date',
                'users.id as user_id',
                'users.cust_id as cust_id',
                'users.username',
                'services.id as service_id',
                'services.name as service_name',
                'orders.public_price',
                'orders.buying_price',
                'orders.sale_margin',
                'orders.order_amount',
                'orders.is_parent_order',
                'orders.order_item_id',
                'orders.txn_ref as txn_id',
                'orders.grand_total',
                'orders.sur_charge',
                'order_items.product_id',
                'order_items.sender_first_name',
                'order_items.sender_mobile',
                'order_items.receiver_first_name',
                'order_items.receiver_mobile',
                'order_items.tt_mobile',
                'order_items.tt_operator',
                'order_items.app_mobile',
                'order_items.app_amount_topup',
                'order_items.app_currency',
                'order_items.tama_pin',
                'order_items.tama_serial',
                'order_items.instructions',
                'order_items.link',
                'order_status.name as order_status_name',
            ]);
        if(auth()->user()->group_id == 2){
            $query->join('order_items','order_items.id','orders.order_item_id');
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $query->whereIn('users.id',$retailers);
            $query->where('orders.is_parent_order', '=', '1');
        }elseif(auth()->user()->group_id == 3){
            $query->join('order_items','order_items.id','orders.order_item_id');
            $child = User::where('id',auth()->user()->id)->with('children')->first();
            $retailers = $child->children->pluck('id')->flatten()->toArray();
            $query->whereIn('users.id',$retailers);
            $query->where('orders.is_parent_order', '=', '1');
        }elseif(auth()->user()->group_id == 4){
            $query->join('order_items','order_items.order_id','orders.id');
            $query->where('users.id',auth()->user()->id);
            $query->where('orders.is_parent_order', '=', '0');
        }else{
            $query->join('order_items','order_items.order_id','orders.id');
            $query->where('users.id',auth()->user()->id);
            $query->where('orders.is_parent_order', '=', '0');
        }
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            $today_date = date("Y-m-d");
            switch (DEFAULT_RECORD_METHOD){
                case 1:
                    $query->whereBetween('orders.date', [$today_date." 00:00:00",$today_date." 23:59:59"]);
                    break;
                case 2:
                    $query->whereMonth('orders.date',date('m'));
                    break;
                case 3:
                    $query->whereBetween('orders.date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                    break;
            }
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('orders.date',[$from_date,$to_date]);
        }
        $orders = $query;
        return Datatables::of($orders)
            ->addColumn('product_name', function ($orders) {
                if($orders->service_id == 1){
                    return optional(Product::find($orders->product_id))->name;
                }
                if($orders->service_id == 5){
                    return $orders->service_name.' '.AppHelper::formatAmount($orders->app_currency,$orders->app_amount_topup);
                }
                if($orders->service_id == 2 || $orders->service_id == 7){
                    $tt_op = OrderItem::find($orders->order_item_id);
                    return $orders->tt_operator == null ? optional($tt_op)->tt_operator :  $orders->tt_operator;
                }
                $iso_code = optional(User::find($orders->user_id))->currency;
                $price = $orders->public_price == "0.00" ? $orders->grand_total : $orders->public_price;
                return $orders->service_name.' '.AppHelper::formatAmount($iso_code,$price);
            })
            ->addColumn('public_price', function ($orders){
                return $orders->public_price;
            })
            ->addColumn('pin', function ($orders){
                if($orders->service_id == 7){
                    $pin_history = PinHistory::where('used_by',$orders->user_id)->where("date",$orders->date)->first();
                    return optional($pin_history)->pin;
                }else{
                    return $orders->tama_pin;
                }
            })
            ->addColumn('serial', function ($orders){
                if($orders->service_id == 7){
                    $pin_history = PinHistory::where('used_by',$orders->user_id)->where("date",$orders->date)->first();
                    return optional($pin_history)->serial;
                }else{
                    return $orders->tama_serial;
                }
            })
            ->addColumn('buying_price', function ($orders){
                return $orders->buying_price;
            })
            ->addColumn('order_amount', function ($orders){
                return $orders->order_amount;
            })
            ->addColumn('sale_margin', function ($orders){
                return $orders->sale_margin;
            })
            ->addColumn('mobile', function ($orders){
                if($orders->service_id == 1){
                    return $orders->receiver_mobile;
                }elseif ($orders->service_id == 2){
                    return $orders->tt_mobile;
                }else{
                    return $orders->app_mobile;
                }
            })
            ->filter(function ($query) use ($request) {
                if($request->service_id[0] == '9'){
                    $qry = '9';
                    $query->Where(function ($q) use ($qry) {
                        $q->orWhere('orders.order_status_id', "like", "$qry");
                    });
                }
            })
            ->make(true);
    }

    function myTransactions(){
        $page_data = [
            'page_title' =>  trans('common.breadcrumb_trans_history'),
            'services' => Service::select('id','name')->get()
        ];
        return view('app.transactions.mytransactions',$page_data);
    }

    function fetchMyTransactions(Request $request){
        $query = Order::join('users','users.id','orders.user_id')
            ->join('order_status','order_status.id','orders.order_status_id')
            ->join('services','services.id','orders.service_id')
            ->join('order_items','order_items.id','orders.order_item_id')
            ->select([
                'orders.date',
                'users.id as user_id',
                'users.cust_id as cust_id',
                'users.username',
                'services.id as service_id',
                'services.name as service_name',
                'orders.public_price',
                'orders.buying_price',
                'orders.sale_margin',
                'orders.order_amount',
                'orders.is_parent_order',
                'orders.order_item_id',
                'orders.txn_ref as txn_id',
                'orders.grand_total',
                'order_items.product_id',
                'order_items.sender_first_name',
                'order_items.sender_mobile',
                'order_items.receiver_first_name',
                'order_items.receiver_mobile',
                'order_items.tt_mobile',
                'order_items.tt_operator',
                'order_items.app_mobile',
                'order_items.app_amount_topup',
                'order_items.app_currency',
                'order_items.tama_pin',
                'order_items.tama_serial',
                'order_status.name as order_status_name',
            ]);
        $query->whereIn('users.id',[auth()->user()->id]);
        $query->where('orders.is_parent_order', '=', '0');
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            $today_date = date("Y-m-d");
            switch (DEFAULT_RECORD_METHOD){
                case 1:
                    $query->whereBetween('orders.date', [$today_date." 00:00:00",$today_date." 23:59:59"]);
                    break;
                case 2:
                    $query->whereMonth('orders.date',date('m'));
                    break;
                case 3:
                    $query->whereBetween('orders.date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                    break;
            }
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('orders.date',[$from_date,$to_date]);
        }
        $orders = $query;
        return Datatables::of($orders)
            ->addColumn('product_name', function ($orders) {
                if($orders->service_id == 1){
                    return optional(Product::find($orders->product_id))->name;
                }
                if($orders->service_id == 5){
                    return $orders->service_name.' '.AppHelper::formatAmount($orders->app_currency,$orders->app_amount_topup);
                }
                if($orders->service_id == 7){
                    $tt_op = OrderItem::find($orders->order_item_id);
                    return $orders->tt_operator == null ? optional($tt_op)->tt_operator :  $orders->tt_operator;
                }
                $iso_code = optional(User::find($orders->user_id))->currency;
                $price = $orders->public_price == "0.00" ? $orders->grand_total : $orders->public_price;
                return $orders->service_name.' '.AppHelper::formatAmount($iso_code,$price);
            })
            ->addColumn('public_price', function ($orders){
                return $orders->public_price;
            })
            ->addColumn('buying_price', function ($orders){
                return $orders->buying_price;
            })
            ->addColumn('order_amount', function ($orders){
                return $orders->order_amount;
            })
            ->addColumn('sale_margin', function ($orders){
                return $orders->sale_margin;
            })
            ->addColumn('mobile', function ($orders){
                if($orders->service_id == 1){
                    return $orders->receiver_mobile;
                }elseif ($orders->service_id == 2){
                    return $orders->tt_mobile;
                }else{
                    return $orders->app_mobile;
                }
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('query'))) {
                    $qry = $request->input('query');
                    $query->Where(function ($q) use ($qry) {
                        $q->Where('order_items.sender_first_name', "like", "%{$qry}%");
                        $q->orWhere('order_items.receiver_first_name', "like", "%{$qry}%");
                        $q->orWhere('order_items.sender_mobile', "like", "%{$qry}%");
                        $q->orWhere('order_items.receiver_mobile', "like", "%{$qry}%");
                        $q->orWhere('order_items.tt_mobile', "like", "%{$qry}%");
                        $q->orWhere('order_items.app_mobile', "like", "%{$qry}%");
                        $q->orWhere('orders.txn_ref', "like", "%{$qry}%");
                        $q->orWhere('users.username', "like", "%{$qry}%");
                    });
                }
                if (!empty($request->input('service_id'))) {
                    $query->whereIn('services.id',$request->input('service_id'));
                }
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date').' 00:00:00';
                    $to_date = $request->input('to_date').' 23:59:59';
                    $query->whereBetween('orders.date',[$from_date,$to_date]);
                }
            })
            ->make(true);
    }
    function system_transactions(Request $request){

        $today_date = date("Y-m-d");
        $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : $today_date;
        $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : $today_date;
        $query = !empty($request->input('query')) ? $request->input('query') : "";
        $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : "3";
        $system_transactions = \App\Models\Transaction::join('users','users.id','transactions.user_id')
            ->select([
                'transactions.balance',
                'transactions.debit',
                'transactions.prev_bal',
                'transactions.date',
                'users.username',
                'users.group_id',
            ])
            ->where('users.group_id', $group_id)
            ->where('transactions.type','debit')
            ->Where('users.username', "like", "%$query%")
            ->whereBetween('transactions.date',[$from_date." 00:00:00",$to_date." 23:59:59"])
            ->orderBy('transactions.date',"DESC")
            ->get();
        $user_groups = \App\Models\UserGroup::where('status',1)
            ->orderBy('id',"DESC")
            ->get();

        $decode = json_decode($system_transactions,true);
        $m ='';
        if($decode){
            foreach($decode as $index => $value )
            {
                $output[] = $value['prev_bal'];
            }
            $m = array_diff_assoc($output, array_unique($output));
        }
        $page_data = [
            'page_title' =>  trans('common.breadcrumb_trans_history'),
            'from_date' => $from_date,
            'to_date' => $to_date,
            'data' => $decode,
            'diff' => $m,
            'groups_id' => $user_groups,
        ];
        return view('app.transactions.system_transactions',$page_data);
    }
}
