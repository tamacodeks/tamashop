<?php

namespace App\Http\Controllers\App;

use App\DataTables\SearchDataTable;
use App\Models\Order;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class SearchController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index(SearchDataTable $dataTable)
    {
        return $dataTable->render('common.search');
    }

    public function appendValue($data, $type, $element)
    {
        // operate on the item passed by reference, adding the element and type
        foreach ($data as $key => & $item) {
            $item[$element] = $type;
        }
        return $data;
    }

    public function appendURL($data, $prefix,$search_qry)
    {
        // operate on the item passed by reference, adding the url based on slug
        foreach ($data as $key => & $item) {
            $item['url'] = secure_url('search?query='.$search_qry."&prefix=".$prefix);
        }
        return $data;
    }
    public function ajaxSearch(Request $request)
    {
        $search_qry = $request->input('query');
        if(!$search_qry && $search_qry == '') return response()->json(array(), 400);
        $query = User::join('user_groups', 'user_groups.id', 'users.group_id')
            ->select(array('users.id','users.username as name','users.cust_id'));
        if (auth()->user()->group_id == 1) {
            $query->where(function ($query) {
                $query->where('parent_id', '=', '0')
                    ->orWhereNull('parent_id');
            });
        } elseif (in_array(auth()->user()->group_id,[2,3])) {
            $query->where('parent_id', auth()->user()->id);
        } else {
            $query->where('parent_id', auth()->user()->id);
        }
        $query->where(function ($query) use ($search_qry) {
            $query->where('users.cust_id', 'like', "%{$search_qry}%")
                ->orWhere('users.username', 'like', "%{$search_qry}%");
        });
        $users = $query->take(5)->get()->toArray();

        $trans_query = Order::join('users', 'users.id', 'orders.user_id');
        $trans_query->join('services', 'services.id', 'orders.service_id');
        $trans_query->join('order_items', 'order_items.order_id', 'orders.id');
        $trans_query->join('order_status', 'order_status.id', 'orders.order_status_id');
        $trans_query->select(array('orders.id','orders.txn_ref as name','users.username'));
        if (auth()->user()->group_id == 1) {
            $trans_query->where(function ($trans_query) {
                $trans_query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            });
        } elseif (in_array(auth()->user()->group_id,[2,3])) {
            $trans_query->where('users.parent_id', auth()->user()->id);
        } else {
            $trans_query->where('users.id', auth()->user()->id);
        }
        $trans_query->where(function ($trans_query) use ($search_qry) {
            $trans_query->Where('orders.txn_ref', 'like',  "%".$search_qry."%")
                ->orwhere('order_items.tama_pin', 'like',  "%".$search_qry."%")
                ->orWhere('orders.id', 'like', "%".$search_qry."%")
                ->orWhere('services.name', 'like',  "%".$search_qry."%");
        });
        $transaction = $trans_query->take(5)->get()->toArray();
//        var_dump($transaction);
        Log::info('search res ',$transaction);
        // Data normalization
//        $transactions = $this->appendValue($transaction, asset('images/search_res.png'),'icon');
        $users_res 	= $this->appendURL($users, 'users',$search_qry);
        $transactions  = $this->appendURL($transaction, 'orders',$search_qry);
        // Add type of data to each item of each set of results
        $res_users = $this->appendValue($users_res, 'user', 'class');
        $res_cat = $this->appendValue($transactions, 'order', 'class');
        // Merge all data into one array
        $data = array_merge($res_users, $res_cat);
        return response()->json(array(
            'data'=>$data
        ));
    }

}
