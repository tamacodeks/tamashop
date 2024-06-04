@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('common.menu_my_services'),'url'=> "",'active' => 'no'],
        ['name' => "Aleda",'url'=> secure_url('aleda/manage'),'active' => 'no'],
        ['name' => trans('myservice.vw_statistics'),'url'=> secure_url('aleda/statistics'),'active' => 'no'],
        ['name' => $page_title,'url'=> '','active' => 'yes'],
    ]])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 m-t-20">
                <div class="panel">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <h4>{{ $card->name }}</h4>
                            <h5>{{ $card->description }}</h5>
                        </div>
                    </div>
                </div>
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="orders-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('common.lbl_date') }}</th>
                                    <th>{{ trans('users.lbl_user_name') }}</th>
                                    <th>{{ trans('common.transaction_serial') }}</th>
                                    <th>{{ trans('common.transaction_pin') }}</th>
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
    <script src="{{ secure_asset('vendor/date-picker/jquery-ui.js') }}"></script>
    <script src="{{ secure_asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $( function() {
            $( ".date" ).datepicker({
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                dateFormat : "yy-mm-dd",
                showAnim : "slideDown"
            });
        } );
        $(document).ready(function () {
            $(".select-picker").selectpicker();
            var oTable = $('#orders-table').DataTable({
                "autoWidth": false,
                searching: true,
//                "pageLength": "-1",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                serverSide: true,
                ajax: {
                    url: '{{ secure_url('aleda/statistics/usage/cc/fetch') }}',
                    data: function (d) {
                        d.cc_id = "{{ $card->id }}";
                    }
                },
                columns: [
                    {
                        "className":      '',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {data: 'date', name: 'aleda_statistics.date',searchable:false},
                    {data: 'username', name: 'users.username'},
                    {data: 'serial', name: 'aleda_statistics.serial',searchable:false,orderable:false},
                    {data: 'pin', name: 'aleda_statistics.pin',searchable:false,orderable:false}
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
                ],
                aaSorting: [[1, 'DESC']],
                drawCallback: function () {
                    $('[data-toggle="popover"]').popover();
                }
            });

            oTable.on('order.dt search.dt', function () {
                oTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            $('#search-form').on('submit', function(e) {
                oTable.draw();
                e.preventDefault();
            });
        });
    </script>
@endsection