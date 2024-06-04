<?php

namespace App\DataTables;

use app\Library\AppHelper;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class SearchDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $get = $this->query(request(), true);
//        dd($get);
        if ($get == 'user') {
            return datatables($query)
                ->addColumn('action', function ($users) {
                    return '<a href="' . url('user/view/' . $users->user_id) . '" class="btn btn-xs btn-success"><i class="fa fa-id-card"></i> ' . trans('common.lbl_view') . '</a>&nbsp;&nbsp;<a href="' . url('user/update/' . $users->user_id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> ' . trans('common.lbl_edit') . '</a>&nbsp;&nbsp;<a onclick="AppConfirmDelete(this.href,\'' . trans('common.lbl_remove') . ' ' . $users->username . '\',\'' . trans('common.ask_remove') . '\' );return false;" href="' . url('user/remove/' . $users->user_id) . '" class="btn btn-xs btn-danger"><i class="fa fa-times-circle"></i> ' . trans('common.btn_delete') . '</a>';
                })
                ->addColumn('balance',function($users){
                    if($users->group_id == 2){
                        return AppHelper::getAdminBalance(true);
                    }
                    return AppHelper::getBalance($users->user_id,$users->user_currency);
                })
                ->addColumn('credit_limit',function($users){
                    if($users->group_id == 2){
                        return AppHelper::getAdminBalance(false,true);
                    }
                    return AppHelper::get_credit_limit($users->user_id);
                });
        } else {
            return datatables($query)
                ->addColumn('action', function ($orders) {
                    return '<span class="label label-warning">' . $orders->order_status . '</span>';
                });
        }

    }

    /**
     * Get query source of dataTable.
     * @param Request $request
     * @param bool $check_for_column
     * @return mixed
     */
    public function query(Request $request, $check_for_column = false)
    {
        if(empty($request->query('query'))){
            return [];
        }
        $search_qry = $request->get('query');
//        dd($search_qry);
        //search for users
        $query = User::join('user_groups', 'user_groups.id', 'users.group_id')
            ->select(['users.id as user_id','users.group_id as group_id','users.currency as user_currency','users.cust_id as cust_id', 'users.username as username', 'user_groups.name as account_type', 'users.created_at as account_created_on', 'users.updated_at as updated_at', 'users.parent_id as parent_id', 'users.last_activity']);
        if (auth()->user()->group_id == 1) {
            $query->where(function ($query) {
                $query->where('users.parent_id', '=', '0')
                    ->orWhereNull('users.parent_id');
            });
        } elseif (in_array(auth()->user()->group_id,[2,3])) {
            $query->where('users.parent_id', auth()->user()->id);
        } else {
            $query->where('users.parent_id', auth()->user()->id);
        }
        if($search_qry == "%"){
            $query->where('users.status',0);
        }else{
            $query->where(function ($query) use ($search_qry) {
                $query->where('users.cust_id', 'like', "%{$search_qry}%")
                    ->orWhere('users.username', 'like', "%{$search_qry}%");
            });
        }
//        dd($request->input('query'));
        if ($query->count() != 0) {
            if ($check_for_column == true) return 'user';
            return $query;
        }
        //search for transactions
//        \DB::enableQueryLog();
        $trans_query = \DB::table('orders');
        $trans_query->join('users', 'users.id', 'orders.user_id');
        $trans_query->join('services', 'services.id', 'orders.service_id');
        $trans_query->join('order_items', 'order_items.order_id', 'orders.id');
        $trans_query->join('order_status', 'order_status.id', 'orders.order_status_id');
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
        $transaction = $trans_query->select('users.cust_id as cust_id', 'users.username as username', 'services.name as service_name', 'orders.txn_ref as txn_id', 'orders.public_price', 'orders.order_amount as reseller_price', 'orders.sale_margin as sale_margin', 'order_items.sender_first_name as sender_name', 'order_items.sender_mobile as sender_number', 'order_items.receiver_first_name as receiver_name', 'order_items.receiver_mobile as receiver_number', 'order_status.name as order_status');
        if ($check_for_column == true) return 'order';
//       $trans_query->get();
//        $rea = \DB::getQueryLog();
//        dd($rea);
        return $transaction;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction()
            ->parameters(
                [
                    "autoWidth" => false,
                    'dom' => 'Bfrtip',
                    'lengthMenu' => [
                        [10, 25, 50, -1],
                        ['10 ' . trans('users.records'), '25 ' . trans('users.records'), '50 ' . trans('users.records'), trans('users.show_all')]
                    ],
                    // Add to buttons the pageLength option.
                    'buttons' => ['pageLength', 'excel']
                ]
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $get = $this->query(request(), true);
        if ($get == 'user') {
            return [
                'cust_id' => ['title' => trans('users.lbl_tbl_cust_id')],
                'username' => ['title' => trans('users.lbl_user_name')],
                'account_type' => ['title' => trans('users.lbl_tbl_user_acc_type'),'orderable' => false,'searchable' => false],
                'account_created_on' => ['title' => trans('users.lbl_tbl_user_created_on'),'orderable' => false,'searchable' => false],
                'balance' => ['title' => trans('users.lbl_tbl_user_balance'),'orderable' => false,'searchable' => false],
                'credit_limit' => ['title' => trans('users.lbl_user_credit_limit'),'orderable' => false,'searchable' => false]
            ];
        } elseif($get == 'order') {
            return [
                'cust_id' => ['title' => trans('users.lbl_tbl_cust_id')],
                'username' => ['title' => trans('users.lbl_user_name'), 'orderable' => false],
                'service_name' => ['title' => trans('users.lbl_user_commission_service'), 'orderable' => false],
                'txn_id' => ['title' => trans('common.transaction_tbl_cust_id'), 'orderable' => false],
                'public_price' => ['title' => trans('common.transaction_tbl_pub_price'), 'orderable' => false],
                'reseller_price' => ['title' => trans('common.transaction_tbl_res_price'), 'orderable' => false],
                'sale_margin' => ['title' => trans('common.transaction_tbl_sale_margin'), 'orderable' => false],
                'sender_name' => ['title' => trans('common.transaction_tbl_sen_name'), 'orderable' => false],
                'sender_number' => ['title' => trans('common.transaction_tbl_sen_num'), 'orderable' => false],
                'receiver_name' => ['title' => trans('common.transaction_tbl_rec_name'), 'orderable' => false],
                'receiver_number' => ['title' => trans('common.transaction_tbl_rec_num'), 'orderable' => false]

            ];
        }else{
            return [];
        }
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Search_' . date('YmdHis');
    }
}
