<?php

namespace App\Http\Controllers\MyService\CallingCard;

use app\Library\AppHelper;
use app\Library\SecurityHelper;
use App\Models\CallingCard;
use App\Models\CallingCardPin;
use App\Models\CallingCardUpload;
use App\Models\Order;
use App\Models\PinHistory;
use App\Models\TelecomProvider;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    private $log_title = "Calling Card Report";

    function __construct()
    {
        parent::__construct();
    }

    /**
     * View - Overall uploaded pin statistics
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function overall_upload_stats()
    {
        $this->data['page_title'] = "Overall uploaded pins statistics";
        $this->data['providers'] = TelecomProvider::select('id', 'name', 'face_value')->get();
        return view('myservice.calling-cards.reports.upload-stats', $this->data);
    }

    /**
     * Ajax - Fetch Overall uploaded pins statistics
     * @param Request $request
     * @return mixed
     */
    function fetch_overall_upload_stats(Request $request)
    {
        $query = CallingCard::select(
            'calling_cards.id',
            'calling_cards.name',
            'calling_cards.description as desc_note',
            'calling_cards.created_at',
            'calling_cards.updated_at',
            'calling_cards.telecom_provider_id'
        );
        return Datatables::of($query)
            ->addColumn('total_cards', function ($query) use ($request) {
                $total_pins_query = CallingCardPin::where('cc_id', $query->id);
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date') . ' 00:00:00';
                    $to_date = $request->input('to_date') . ' 23:59:59';
                    $total_pins_query->whereBetween('created_at', [$from_date, $to_date]);
                }
                return $total_pins_query->count();
            })
            ->addColumn('total_used_cards', function ($query) use ($request) {
                $total_pins_query = CallingCardPin::where('cc_id', $query->id)->where('is_used', 1);
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date') . ' 00:00:00';
                    $to_date = $request->input('to_date') . ' 23:59:59';
                    $total_pins_query->whereBetween('created_at', [$from_date, $to_date]);
                }
                return $total_pins_query->count();
            })
            ->addColumn('total_unused_cards', function ($query) use ($request) {
                $total_pins_query = CallingCardPin::where('cc_id', $query->id)->where('is_used', 0);
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date') . ' 00:00:00';
                    $to_date = $request->input('to_date') . ' 23:59:59';
                    $total_pins_query->whereBetween('created_at', [$from_date, $to_date]);
                }
                return $total_pins_query->count();
            })
            ->addColumn('uploaded_date', function ($query) {
                return $query->updated_at == '' ? $query->created_at : $query->updated_at;
            })
            ->addColumn('description', function ($query) {
                return '<span data-trigger="hover" data-container="body" data-toggle="popover" data-placement="top" data-content="' . $query->desc_note . '" data-original-title="' . $query->name . '" title="">' . AppHelper::doTrim_text($query->desc_note, 30, true) . '</span>';
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('query'))) {
                    $qry = $request->input('query');
                    $query->Where(function ($q) use ($qry) {
                        $q->Where('calling_cards.name', "like", "%{$qry}%");
                    });
                }
                if (!empty($request->input('telecom_provider_id'))) {
                    $query->whereIn('calling_cards.telecom_provider_id', $request->input('telecom_provider_id'));
                }
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date') . ' 00:00:00';
                    $to_date = $request->input('to_date') . ' 23:59:59';
//                    $query->whereBetween('calling_cards.created_at',[$from_date,$to_date]);
                    $query->whereBetween('calling_cards.updated_at', [$from_date, $to_date]);
                }
            })
            ->rawColumns(['description'])
            ->make(true);
    }

    /**
     * View - Pin usage history
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function pin_usage_history()
    {
        $this->data['page_title'] = "Pin Usage History";
        $this->data['providers'] = TelecomProvider::select('id', 'name', 'face_value')->get();
        $this->data['retailers'] = User::where('parent_id', auth()->user()->id)->where('status',1)->select('id', 'username')->get();
        return view('myservice.calling-cards.reports.pin-usage-history', $this->data);
    }

    /**
     * Ajax - Fetch Used pins
     * @param Request $request
     * @return mixed
     */
    function fetch_pin_usage_history(Request $request)
    {
        $query = PinHistory::join('users', 'users.id', 'pin_histories.used_by')
            ->join('calling_cards','calling_cards.id','pin_histories.cc_id')
            ->join('telecom_providers','telecom_providers.id','calling_cards.telecom_provider_id')
            ->select(
                'pin_histories.name',
                'users.username',
                'users.id as user_id',
                'pin_histories.pin',
                'pin_histories.serial',
                'pin_histories.date',
                'pin_histories.cc_id',
                'calling_cards.telecom_provider_id',
                'calling_cards.description as card_desc'
            );
        if(auth()->user()->group_id == 2){

        }else{
            $query->where('users.parent_id', auth()->user()->id);
        }
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            $today_date = date("Y-m-d");
            switch (DEFAULT_RECORD_METHOD){
                case 1:
                    $query->whereBetween('pin_histories.date', [$today_date." 00:00:00",$today_date." 23:59:59"]);
                    break;
                case 2:
                    $query->whereMonth('pin_histories.date',date('m'));
                    break;
                case 3:
                    $query->whereBetween('pin_histories.date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                    break;
            }
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('pin_histories.date',[$from_date,$to_date]);
        }
        return Datatables::of($query)
            ->addColumn('created_at', function ($query) {
                $cc = CallingCardPin::where('serial',$query->serial)->first();
                return optional($cc)->created_at;
            })
            ->addColumn('description', function ($query) {
                return '<span data-trigger="hover" data-container="body" data-toggle="popover" data-placement="top" data-content="' . $query->card_desc . '" data-original-title="' . $query->name . '" title="">' . AppHelper::doTrim_text($query->card_desc, 30, true) . '</span>';
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('query'))) {
                    $qry = $request->input('query');
                    $query->Where(function ($q) use ($qry) {
                        $q->Where('pin_histories.name', "like", "%{$qry}%");
                    });
                }
                if (!empty($request->input('telecom_provider_id'))) {
                    $query->whereIn('calling_cards.telecom_provider_id', $request->input('telecom_provider_id'));
                }
                if (!empty($request->input('retailers'))) {
                    $query->whereIn('users.id', $request->input('retailers'));
                }
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date') . ' 00:00:00';
                    $to_date = $request->input('to_date') . ' 23:59:59';
                    $query->whereBetween('pin_histories.date', [$from_date, $to_date]);
                }
            })
            ->rawColumns(['description'])
            ->make(true);
    }

    /**
     * View - Pins Report
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function pins_report()
    {
        $this->data['page_title'] = "Pins Report";
        $this->data['providers'] = TelecomProvider::select('id', 'name', 'face_value')->get();
        return view('myservice.calling-cards.reports.pins-report', $this->data);
    }

    /**
     * Ajax - Fetch Pins Report
     * @param Request $request
     */
    function fetch_pins_report(Request $request)
    {
        $query = CallingCardUpload::join('calling_cards','calling_cards.id','calling_card_uploads.cc_id')
            ->select(
                'calling_cards.id',
                'calling_cards.name',
                'calling_card_uploads.date',
                'calling_card_uploads.no_of_pins as total_cards',
                'calling_card_uploads.total_amount as total_value',
                'calling_cards.description as desc_note'
            );
        $today_date = date("Y-m-d");
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            switch (DEFAULT_RECORD_METHOD){
                case 1:
                    $query->whereBetween('calling_card_uploads.date', [$today_date." 00:00:00",$today_date." 23:59:59"]);
                    break;
                case 2:
                    $query->whereMonth('calling_card_uploads.date',date('m'));
                    break;
                case 3:
                    $query->whereBetween('calling_card_uploads.date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                    break;
            }
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('calling_card_uploads.date',[$from_date,$to_date]);
        }
        return Datatables::of($query)
            ->addColumn('description', function ($query) {
                return '<span data-trigger="hover" data-container="body" data-toggle="popover" data-placement="top" data-content="' . $query->desc_note . '" data-original-title="' . $query->name . '" title="">' . AppHelper::doTrim_text($query->desc_note, 30, true) . '</span>';
            })
            ->rawColumns(['description'])
            ->make(true);
    }


    function margin_report()
    {
        $this->data['page_title'] = "Retailers Margin Report";
        $this->data['retailers'] = User::where('parent_id',auth()->user()->id)->select('id', 'username')->get();
        return view('myservice.calling-cards.reports.margin-report',$this->data);

    }

    function fetch_margin_report(Request $request)
    {
        $query = User::select([
            'users.id',
            'users.username',
            \DB::raw('max(orders.date) AS last_transaction'),
            \DB::raw('sum(orders.sale_margin) AS margin'),
            \DB::raw('sum(orders.buying_price) AS buying_price'),
            \DB::raw('sum(orders.order_amount) AS sale_price')
        ])->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('users.parent_id',auth()->user()->id)
            ->where('orders.is_parent_order',1)
            ->groupBy('users.id');
        return Datatables::of($query)
            ->addColumn('action', function ($users) use ($request) {
                $from_date = $request->from_date;
                $to_date = $request->to_date;
                return '<a href="' . secure_url('transactions?from='.$from_date.'&to='.$to_date.'&user='.$users->username) . '" class="btn btn-xs btn-success" target="_blank"><i class="fa fa-user-clock"></i> ' . trans('common.lbl_view') . '</a>';
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('retailers'))) {
                    $query->whereIn('users.id', $request->input('retailers'));
                }
                if (!empty($request->input('from_date')) && !empty($request->input('to_date'))) {
                    $from_date = $request->input('from_date') . ' 00:00:00';
                    $to_date = $request->input('to_date') . ' 23:59:59';
                    $query->whereBetween('orders.date', [$from_date, $to_date]);
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
