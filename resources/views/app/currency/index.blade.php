@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
         ['name' => trans('common.currencies'),'url'=> '','active' => 'yes']
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>{{ trans('service.tama_services') }}</h4>
                        <div class="pull-right" style="margin-top: -35px">
                            <a onclick="AppModal(this.href,'{{ trans('common.btn_add') .' '.trans('common.currency') }}');return false;" href="{{ secure_url('currency/update') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i>&nbsp;{{ trans('common.btn_add') }} </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="currency-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>{{ trans('common.currency_tbl_title') }}</th>
                                    <th>{{ trans('common.iso') }}</th>
                                    <th>{{ trans('common.symbol') }}</th>
                                    <th>{{ trans('common.currency_tbl_value') }}</th>
                                    <th>{{ trans('common.currency_tbl_decimal_point') }}</th>
                                    <th>{{ trans('common.currency_tbl_thousand_point') }}</th>
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
    <script src="{{ secure_asset('vendor/datatables/app.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#currency-table').DataTable({
                "autoWidth": false,
                "pageLength": "{{ PER_PAGE }}",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                serverSide: true,
                ajax: '{{ secure_url('fetch/currencies') }}',
                columns: [
                    {data: 'title', name: 'title',orderable : false},
                    {data: 'code', name: 'code',orderable : false},
                    {data: 'symbol', name: 'symbol',orderable : false},
                    {data: 'value', name: 'value',orderable : false},
                    {data: 'decimal_point', name: 'decimal_point',orderable : false},
                    {data: 'thousand_point', name: 'thousand_point',orderable : false},
                    {data: 'action', name: 'users.action',orderable : false,searchable: false}
                ],
                dom: 'Bfrtip',
                // Configure the drop down options.
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 {{ trans('users.records') }}', '25 {{ trans('users.records') }}', '50 {{ trans('users.records') }}', '{{ trans('users.show_all') }}' ]
                ],
                // Add to buttons the pageLength option.
                buttons: [
                    'pageLength',
                    {
                        extend:    'excel',
                        text:      '<i class="fa fa-file-excel"></i>',
                        titleAttr: '{{ trans('common.download_as_excel') }}'
                    },
                    {
                        extend:    'reload',
                        text:      '<i class="fa fa-sync"></i>',
                        titleAttr: '{{ trans('common.refresh') }}'
                    }
                ]
            });
        });
    </script>
@endsection