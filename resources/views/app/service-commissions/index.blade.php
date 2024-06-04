@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
         ['name' => trans('common.menu_setup_commission'),'url'=> '','active' => 'yes']
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>{{ trans('service.tama_services') }} {{ trans('common.menu_setup_commission') }}</h4>
                        <div class="pull-right" style="margin-top: -35px">
                            <a onclick="AppModal(this.href,'{{ trans('common.btn_add') .' '.trans('common.order_tbl_service') }}');return false;" href="{{ secure_url('service-commission/update') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i>&nbsp;{{ trans('common.btn_add') }} </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="service-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>{{ trans('myservice.name') }}</th>
                                    <th>{{ trans('service.prev_commission') }}</th>
                                    <th>{{ trans('service.current_commission') }}</th>
                                    <th>{{ trans('service.def_mgr_com') }}</th>
                                    <th>{{ trans('service.def_ret_com') }}</th>
                                    <th>{{ trans('common.updated_at') }}</th>
                                    <th>{{ trans('common.mr_tbl_action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="{{ secure_asset('vendor/datatables/datatables.css') }}" rel="stylesheet">
    <script src="{{ secure_asset('vendor/datatables/datatables.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#service-table').DataTable({
                "autoWidth": false,
                "pageLength": "-1",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                searching: false,
                paging: false,
                serverSide: true,
                ajax: '{{ secure_url('service-commissions/fetch') }}',
                columns: [
                    {data: 'name', name: 'name',orderable : false,searchable: false},
                    {data: 'prev_com', name: 'prev_com',orderable : false,searchable: false},
                    {data: 'commission', name: 'commission',orderable : false,searchable: false},
                    {data: 'mgr_def_com', name: 'app_commissions.mgr_def_com',orderable : false,searchable: false},
                    {data: 'retailer_def_com', name: 'app_commissions.retailer_def_com',orderable : false,searchable: false},
                    {data: 'updated_at', name: 'updated_at',orderable : false,searchable: false},
                    {data: 'action', name: 'action',orderable : false,searchable: false}
                ],
                order: [[1, 'asc']]
            });
        });
    </script>
@endsection