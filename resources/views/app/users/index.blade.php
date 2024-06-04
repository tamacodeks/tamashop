@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Users",'url'=> '','active' => 'yes']
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
                                    <a href="{{ secure_url('user/update') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i>&nbsp;{{ trans('users.btn_add_user') }}</a>
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="users-table" class="table table-condensed">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ trans('common.order_tbl_sl') }}</th>
                                            <th>{{ trans('users.lbl_user_status') }}</th>
                                            <th>{{ trans('users.lbl_tbl_cust_id') }}</th>
                                            <th>{{ trans('users.lbl_user_name') }}</th>
                                            <th>{{ trans('users.lbl_tbl_user_acc_type') }}</th>
                                            {{--<th>{{ trans('users.lbl_tbl_user_rep') }}</th>--}}
                                            <th>{{ trans('users.lbl_tbl_user_balance') }}</th>
                                            <th>{{ trans('users.lbl_user_credit_limit') }}</th>
                                            <th>{{ trans('users.lbl_tbl_user_created_on') }}</th>
                                            <th>{{ trans('common.mr_tbl_action') }}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <script id="details-template" type="text/x-handlebars-template">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>{{ trans('users.last_activity') }}</td>
                                            <td>@{{ last_online_at }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('users.lbl_tbl_user_credit') }}</td>
                                            <td>@{{ credit_limit }}</td>
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
    <script src="{{ secure_asset('vendor/datatables/Buttons-1.5.1/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/Buttons-1.5.1/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/app.js') }}"></script>
    <script src="{{ secure_asset('vendor/common/handlebars-v4.0.11.js') }}"></script>
    <script>
        @if(auth()->user()->group_id == 2)
        function syncPriceLists(url,anim)
        {
            $("#users-table").LoadingOverlay('show');
            $(anim).html("<i class='fa fa-refresh fa-spin'></i>");
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                /**
                 * A function to be called if the request fails.
                 */
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#users-table").LoadingOverlay('hide');
                    console.log(jqXHR);
                    $(anim).html("<i class='fa fa-sync-alt'></i>");
                },

                /**
                 * A function to be called if the request succeeds.
                 */
                success: function(data, textStatus, jqXHR) {
                    console.log(data);
                    $("#users-table").LoadingOverlay('hide');
                    $(anim).html("<i class='fa fa-sync-alt'></i>");
                    $.alert({
                        title: "Information",
                        content: data.message,
                        buttons: {
                            "{{ trans('common.btn_close') }}": function () {

                            }
                        },
                        backgroundDismiss: true, // this will just close the modal
                        theme: 'material',
                        animation: 'zoom',
                        closeAnimation: 'bottom',
                        escapeKey: '{{ trans('common.btn_close') }}',
                        type: 'success',
                        icon: 'fa fa-check-circle'
                    });
                }
            });
        }
        @endif
        $(document).ready(function () {
            setTimeout(function () {
                $("#wrapper").addClass('toggled');
            },1000);
            var template = Handlebars.compile($("#details-template").html());

            var table = $('#users-table').DataTable({
                "autoWidth": false,
                "pageLength": "-1",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "{{ trans('common.processing') }}"
                },
                serverSide: true,
                ajax: '{{ secure_url('fetch/users') }}',
                columns: [
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {
                        "className":      '',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {data: 'status_indicator', name: 'users.status_indicator',"orderable" : false,"searchable": false,"className" : "text-center"},
                    {data: 'cust_id', name: 'users.cust_id'},
                    {data: 'username', name: 'users.username'},
                    {data: 'name', name: 'user_groups.name'},
//                    {data: 'representative', name: 'users.representative',orderable : false,searchable: false},
                    {data: 'balance', name: 'users.balance',orderable : false,searchable: false},
                    {data: 'credit_limit', name: 'credit_limit',orderable : false,searchable: false},
                    {data: 'created_at', name: 'users.created_at'},
                    {data: 'action', name: 'users.action',orderable : false,searchable: false}
                ],
                order: [[6, 'DESC']],
                dom: 'Bfrtip',
                // Configure the drop down options.
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 {{ trans('users.records') }}', '25 {{ trans('users.records') }}', '50 {{ trans('users.records') }}', '{{ trans('users.show_all') }}' ]
                ],
                // Add to buttons the pageLength option.
                buttons: [
                    {
                        extend:    'pageLength',
                    },
                    {
                        extend:    'excel',
                        text:      '<i class="fa fa-file-excel"></i>',
                        titleAttr: '{{ trans('common.download_as_excel') }}',
                        exportOptions: {
                            columns: [ 2,3,4,5,6]
                        }
                    },
                    {
                        extend:    'reload',
                        text:      '<i class="fa fa-sync"></i>',
                        titleAttr: '{{ trans('common.refresh') }}'
                    }
                ]
            });

            table.on('order.dt search.dt', function () {
                table.column(1, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
            // Add event listener for opening and closing details
            $('#users-table tbody').on('click', 'td.details-control', function () {
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