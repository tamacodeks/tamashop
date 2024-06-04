@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('myservice.upload_statistics'),'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('service.service_calling_cards') }} {{ trans('myservice.upload_statistics') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" id="search-form" class="form-inline" role="form">
                            <div class="form-group">
                                <label for="telecom_provider_id">{{ trans('myservice.lbl_card_name') }}</label>
                                <select data-live-search="true" name="telecom_provider_id" id="telecom_provider_id" class="select-picker" multiple>
                                    <option value="">{{ trans('common.lbl_please_choose') }}</option>
                                    @if(isset($providers))
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}">{{ $provider->name }} {{ \app\Library\AppHelper::formatAmount('EUR',$provider->face_value) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="{{ trans('common.filter_lbl_from') }}" class="form-control date" name="from_date" id="from_date" >
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="{{ trans('common.filter_lbl_to') }}" class="form-control date" name="to_date" id="to_date" >
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="{{ trans('myservice.btn_search') }}" name="query" id="query" value="{{ old('query',request()->input('user')) }}">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i>&nbsp;{{ trans('myservice.btn_search') }}</button>
                        </form>
                    </div>
                </div>
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="pin-upload-stats-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('service.uploaded_date') }}</th>
                                    <th>{{ trans('myservice.lbl_card_name') }}</th>
                                    <th>{{ trans('common.lbl_desc') }}</th>
                                    <th>{{ trans('service.total_no_cards') }}</th>
                                    <th>{{ trans('service.total_no_cards_used') }}</th>
                                    <th>{{ trans('service.total_no_cards_unused') }}</th>
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
        $(function () {
            $('[data-toggle="popover"]').popover();
        });
        $(document).ready(function () {
            $(".select-picker").selectpicker();
            var oTable = $('#pin-upload-stats-table').DataTable({
                "autoWidth": false,
                searching: false,
                "pageLength": "-1",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                serverSide: true,
                ajax: {
                    url: '{{ secure_url('cc/report/upload-statistics/fetch') }}',
                    data: function (d) {
                        d.telecom_provider_id = $('#telecom_provider_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.query = $('#query').val();
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
                    {data: 'uploaded_date', name: 'uploaded_date',searchable:false,orderable:false},
                    {data: 'name', name: 'calling_cards.name'},
                    {data: 'description', name: 'description',searchable:false,orderable:false},
                    {data: 'total_cards', name: 'total_cards',searchable:false,orderable:false},
                    {data: 'total_used_cards', name: 'total_used_cards',searchable:false,orderable:false},
                    {data: 'total_unused_cards', name: 'total_unused_cards',searchable:false,orderable:false},
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
                "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
//                    console.log(aData['total_unused_cards'])
                    if ( aData['total_unused_cards'] <= 2 )
                    {
                        $('td', nRow).css('background-color', '#ff0000').css('color', '#fff');
                    }
                },
                drawCallback: function () {
                    $('[data-toggle="popover"]').popover();
                }
            });

            $('#search-form').on('submit', function(e) {
                oTable.draw();
                e.preventDefault();
            });

            oTable.on( 'order.dt search.dt', function () {
                oTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        });
    </script>
@endsection