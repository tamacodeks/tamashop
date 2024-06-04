<?php

namespace App\Http\Controllers\Aleda;

use app\Library\AppHelper;
use App\Models\AledaStatistic;
use App\Models\AledaServiceRequest;
use App\Models\CallingCard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Order;
use App\User;

class ReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Report index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $this->data['page_title'] = "Aleda Service Statistics";
        $this->data['calling_cards'] = CallingCard::select([
            'id','name','description','face_value'
        ])->whereNotNull('aleda_product_code')->get();
        return view('myservice.calling-cards.aleda.index',$this->data);
    }
    function trans()
    {
        $this->data['page_title'] = "Aleda Service Transaction";
        $this->data['aleda_trans'] = '0';
        $this->data['total_trans'] = '0';
        $this->data['total_orders'] = '';
//        dd('khalid');
        return view('myservice.calling-cards.aleda.aleda_trans',$this->data);
    }

    function fetchAledaTransReports(Request $request)
    {
        $demat = 'sellDematModeXS';
        $child = User::where('id',auth()->user()->id)->with('children')->first();
        $retailers = $child->children->pluck('id')->flatten()->toArray();
        $from_date = $request->input('from_date').' 00:00:00';
        $to_date = $request->input('to_date').' 23:59:59';
        $this->data['aleda_trans'] = AledaServiceRequest::whereBetween('invoke_at',[$from_date,$to_date])->where('http_code', '200')->where('method', 'LIKE', "%$demat%")->count();
//        $this->data['total_trans'] = Order::whereBetween('date',[$from_date,$to_date])->where('service_id' , '7')->where('order_status_id', '7')->where('is_parent_order',0)->where('exclude',0)->count();
        $this->data['total_trans'] = Transaction::rightJoin('orders','orders.transaction_id','transactions.id')->whereBetween('transactions.date',[$from_date,$to_date])->where('transactions.type' , 'debit')->where('orders.is_parent_order',0)->where('orders.exclude',0)->count();

        $this->data['total_orders'] = Order::select([
            'order_items.tt_operator',
            'orders.buying_price',
            \DB::raw('count(orders.id) as total')])
            ->join('users','users.id','orders.user_id')
            ->join('order_status','order_status.id','orders.order_status_id')
            ->join('services','services.id','orders.service_id')
            ->join('order_items','order_items.id','orders.order_item_id')
            ->whereBetween('orders.date',[$from_date,$to_date])
            ->where('orders.service_id' , '7')
            ->where('orders.order_status_id', '7')
            ->where('orders.is_parent_order',1)
            ->where('orders.transaction_id',NULL)
            ->groupBy('order_items.tt_operator')
            ->get();
        return view('myservice.calling-cards.aleda.aleda_trans',$this->data);
    }
    /**
     * Ajax - fetch reports
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    function fetchAledaReports(Request $request)
    {
        $query = AledaStatistic::join('calling_cards','calling_cards.id','aleda_statistics.cc_id')->select([
            'calling_cards.name',
            'calling_cards.description',
            'aleda_statistics.cc_id',
            'aleda_statistics.date',
            \DB::raw('COUNT(aleda_statistics.id) as total_used_cards'),
            \DB::raw("SUM(calling_cards.face_value) as total_amount")
        ])
            ->groupBy('aleda_statistics.cc_id');
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            $query->whereDate('aleda_statistics.date',date('Y-m-d'));
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('aleda_statistics.date',[$from_date,$to_date]);
        }
        if (!empty($request->input('cc_id'))) {
            $query->whereIn('aleda_statistics.cc_id',$request->input('cc_id'));
        }
        $report = $query;
        return DataTables::of($report)
            ->addColumn('description', function ($report) {
                return '<span data-trigger="hover" data-container="body" data-toggle="popover" data-placement="top" data-content="' . $report->description . '" data-original-title="' . $report->name . '" title="">' . AppHelper::doTrim_text($report->description, 30, true) . '</span>';
            })
            ->addColumn('action', function ($report) {
                return '<a data-toggle="tooltip" data-placement="top" title="' . trans('common.view_all') . '" href="' . url('aleda/statistics/usage/'.$report->cc_id) . '" class="btn btn-xs btn-info"><i class="fa fa-chart-bar"></i>&nbsp;'.trans('common.lbl_view').' </a>';
            })
            ->rawColumns(['action', 'description'])
            ->make(true);
    }

    function usageHistory($id)
    {
        $card = CallingCard::find($id);
        $this->data['page_title'] = $card->name;
        $this->data['card'] = $card;
        return view('myservice.calling-cards.aleda.usage-history',$this->data);
    }

    function getFetchHistory(Request $request)
    {
//        dd($request->all());
        $query = AledaStatistic::join('users','users.id','aleda_statistics.used_by')->select([
            'aleda_statistics.cc_id',
            'aleda_statistics.date',
            'aleda_statistics.serial',
            'aleda_statistics.pin',
            'users.username',
        ]);
        $query->where('aleda_statistics.cc_id',$request->cc_id);
        $report = $query;
        return DataTables::of($report)
            ->make(true);
    }
}
