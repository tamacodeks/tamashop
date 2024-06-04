@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => $page_title,'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('service.service_calling_cards') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" id="search-form" class="form-inline" role="form">
                            <div class="form-group">
                                <label for="provider_id">{{ trans('myservice.lbl_card_name') }}</label>
                                <select name="provider_id" data-live-search="true" id="provider_id"
                                        class="select-picker">
                                    <option value="">{{ trans('common.lbl_please_choose') }}</option>
                                    @foreach($telecom_providers as $telecom_provider)
                                        <option value="{{ $telecom_provider->id }}">{{ $telecom_provider->name }} {{ \app\Library\AppHelper::formatAmount('EUR',$telecom_provider->face_value) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="from_date">{{ trans('common.filter_lbl_from') }}</label>
                                <input type="text" class="form-control date" name="from_date" id="from_date">
                            </div>
                            <div class="form-group">
                                <label for="to_date">{{ trans('common.filter_lbl_to') }}</label>
                                <input type="text" class="form-control date" name="to_date" id="to_date">
                            </div>
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-filter"></i>&nbsp;{{ trans('myservice.btn_search') }}</button>
                        </form>
                    </div>
                </div>
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="cc_uploads" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('common.lbl_date') }}</th>
                                    <th>{{ trans('myservice.lbl_card_name') }}</th>
                                    <th>{{ trans('myservice.buying_price') }}</th>
                                    <th>{{ trans('service.total_no_cards') }}</th>
                                    <th>{{ trans('sale.total_amount') }}</th>
                                    <th>{{ trans('myservice.status') }}</th>
                                    <th>{{ trans('common.trans_tbl_action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <script id="details-template" type="text/x-handlebars-template">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                <tr>
                                    <th>{{ trans('myservice.uploaded_by') }}</th>
                                    <td>@{{  uploaded_by }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('myservice.rollback_at') }}</th>
                                    <td>@{{  rollback_at }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('myservice.rollback_by') }}</th>
                                    <td>@{{  rollback_by }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('myservice.description') }}</th>
                                    <td>@{{  rollback_note }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </script>
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
    <script src="{{ secure_asset('vendor/common/handlebars-v4.0.11.js') }}"></script>
    <script>
        $(function () {
            $(".date").datepicker({
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showAnim: "slideDown"
            });
        });
        $(document).ready(function () {
            var template = Handlebars.compile($("#details-template").html());
            $(".select-picker").selectpicker();
            var oTable = $('#cc_uploads').DataTable({
                "autoWidth": false,
                searching: false,
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                serverSide: true,
                ajax: {
                    url: '{{ secure_url('cc/reverse-transaction/fetch') }}',
                    data: function (d) {
                        d.provider_id = $('#provider_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                columns: [
                    {
                        "className": 'details-control',
                        "orderable": false,
                        "searchable": false,
                        "data": null,
                        "defaultContent": ''
                    },
                    {data: 'date', name: 'calling_card_uploads.date'},
                    {data: 'name', name: 'calling_cards.name'},
                    {data: 'buying_price', name: 'calling_card_uploads.buying_price'},
                    {data: 'no_of_pins', name: 'calling_card_uploads.no_of_pins'},
                    {data: 'total_amount', name: 'calling_card_uploads.total_amount'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'}
                ],
                dom: 'Bfrtip',
                // Configure the drop down options.
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 {{ trans('users.records') }}', '25 {{ trans('users.records') }}', '50 {{ trans('users.records') }}', '{{ trans('users.show_all') }}']
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
                aaSorting: [[1, 'DESC']]
            });

            $('#search-form').on('submit', function (e) {
                oTable.draw();
                e.preventDefault();
            });

            // Add event listener for opening and closing details
            $('#cc_uploads tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = oTable.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child(template(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });
    </script>
@endsection