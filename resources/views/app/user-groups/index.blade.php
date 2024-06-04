@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "User Groups",'url'=> '','active' => 'yes']
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right m-b-20">
                                    <a href="{{ secure_url('user-group/update') }}" class="btn btn-primary" onclick="AppModal(this.href,'{{ trans('common.btn_add').' '.trans('users.lbl_user_group') }}');return false;"><i class="fa fa-plus-circle"></i>&nbsp;{{ trans('common.btn_add').' '.trans('users.lbl_user_group') }}</a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="user-groups-table" class="table table-condensed">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ trans('common.trans_tbl_name') }}</th>
                                            <th>{{ trans('common.lbl_desc') }}</th>
                                            <th>{{ trans('service.tamatopup_tbl_status') }}</th>
                                            <th>{{ trans('common.created_at') }}</th>
                                            <th>{{ trans('common.updated_at') }}</th>
                                            <th>{{ trans('common.mr_tbl_action') }}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <script id="details-template" type="text/x-handlebars-template">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>{{ trans('users.level_access') }}</td>
                                            <td>@{{  level_access }}</td>
                                        </tr>
                                    </table>
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="{{ secure_asset('vendor/datatables/datatables.css') }}" rel="stylesheet">
    <script src="{{ secure_asset('vendor/datatables/datatables.js') }}"></script>
    <script src="{{ secure_asset('vendor/common/handlebars-v4.0.11.js') }}"></script>
    <script>
        $(document).ready(function () {
            var template = Handlebars.compile($("#details-template").html());
            var table = $('#user-groups-table').DataTable({
                "autoWidth": false,
                "pageLength": "{{ PER_PAGE }}",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                serverSide: true,
                ajax: '{{ secure_url('fetch/user-groups') }}',
                columns: [
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description',orderable : false,searchable: false},
                    {data: 'status', name: 'status',orderable : false,searchable: false},
                    {data: 'created_at', name: 'created_at',orderable : false,searchable: false},
                    {data: 'updated_at', name: 'updated_at',orderable : false,searchable: false},
                    {data: 'action', name: 'action',orderable : false,searchable: false}
                ]
            });
            // Add event listener for opening and closing details
            $('#user-groups-table tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( template(row.data()) ).show();
                    tr.addClass('shown');
                }
            });
        });
    </script>
@endsection