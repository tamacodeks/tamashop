@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('myservice.pin_print_requests'),'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 m-t-20">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('myservice.pin_print_requests') }}
                        <a href="{{ secure_url('tickets') }}" class="pull-right btn btn-primary btn-sm" style="margin-top: -5px"><i
                                    class="fa fa-list-ol"></i>&nbsp;View All Tickets</a>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="pin-history-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('common.payment_lbl_reseller') }}</th>
                                    <th>{{ trans('myservice.lbl_card_name') }}</th>
                                    <th>{{ trans('common.transaction_serial') }}</th>
                                    <th>{{ trans('common.transaction_pin') }}</th>
                                    <th>{{ trans('myservice.printed_at') }}</th>
                                    <th>{{ trans('myservice.status') }}</th>
                                    <th>{{ trans('common.trans_tbl_action') }}</th>
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
        var request,oTable;
        function process_pin_requests(req_id,pin_id,status) {
            $('body').append("<span class='loader'></span>");
            // Abort any pending request
            if (request) {
                request.abort();
            }
            // Serialize the data in the form
            var serializedData = { request_id: req_id,pin_id: pin_id,approve: status };

            // Fire off the request to /form.php
            request = $.ajax({
                url: "{{ secure_url('cc-print-requests/process') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: serializedData
            });

            // Callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR){
                // Log a message to the console
                console.log(response);
                if(response.data.status == 200){
                    $.alert({
                        content: response.data.message,
                        buttons: {
                            "{{ trans('common.btn_close') }}": function () {
                                oTable.ajax.reload();
                            }
                        },
                        title : "{{ trans('common.info') }}",
                        type : "blue",
                        icon : "fa fa-info-circle",
                        theme: 'material'
                    });
                }else{
                    $.alert({
                        content: response.data.message,
                        buttons: {
                            "{{ trans('common.btn_close') }}": function () {

                            }
                        },
                        title : "{{ trans('common.info') }}",
                        type : "red",
                        icon : "fa fa-exclamation-circle",
                        theme: 'material'
                    });
                }
            });

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown){
                // Log the error to the console
                console.error(
                    "The following error occurred: "+
                    textStatus, errorThrown
                );
            });

            // Callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // Reenable the inputs
                $(".loader").remove();
            });
        }
        $(document).ready(function () {
            oTable = $('#pin-history-table').DataTable({
                "autoWidth": false,
                pageLength : "-1",
                searching: true,
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>",
                    paginate: {
                        next: '{!!  trans('pagination.next') !!}', // or '→'
                        previous: '{!! trans('pagination.previous') !!}' // or '←'
                    }
                },
                serverSide: true,
                ajax: {
                    url: '{{ secure_url('cc-print-requests/fetch') }}'
                },
                columns: [
                    {
                        "className":      '',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {data: 'username', name: 'users.username'},
                    {data: 'name', name: 'name'},
                    {data: 'serial', name: 'serial',orderable:false},
                    {data: 'pin', name: 'pin',orderable:false,searchable:false},
                    {data: 'updated_at', name: 'updated_at',searchable:false},
                    {data: 'status', name: 'status',searchable:false,orderable:false},
                    {data: 'action', name: 'action',searchable:false,orderable:false},
                ],
                dom: 'Bfrtip',
                // Configure the drop down options.
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 {{ trans('users.records') }}', '25 {{ trans('users.records') }}', '50 {{ trans('users.records') }}', '{{ trans('users.show_all') }}' ]
                ],
                aaSorting: [[5, 'DESC']],
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
                ],
            });

            oTable.on( 'order.dt search.dt', function () {
                oTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        });
    </script>
@endsection