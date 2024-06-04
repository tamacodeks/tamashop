@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('common.breadcrumb_trans_history'),'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.dashboard_view_trans') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" class="form-inline" action="{{url('filter_transactions')}}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="from_date">{{ trans('common.filter_lbl_from') }}</label>
                                <input type="text" class="form-control date" name="from_date" id="from_date"
                                       value="{{ $from_date }}">
                            </div>
                            <div class="form-group">
                                <label for="to_date">{{ trans('common.filter_lbl_to') }}</label>
                                <input type="text" class="form-control date" name="to_date" id="to_date"
                                       value="{{ $to_date }}">
                            </div>
                            <div class="form-group">
                                <label for="group_id">{{ trans('common.filter_lbl_service') }}</label>
                                <select name="group_id" id="group_id" class="select-picker">
                                    <option value="3">Manager</option>
                                    <option value="4">Retailer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="query">{{ trans('myservice.btn_search') }}</label>
                                <input type="text" class="form-control" name="query" id="query"
                                       value="{{ old('query',request()->input('user')) }}">
                            </div>

                            <button style="margin: 10px;" type="submit" class="btn btn-primary"><i
                                        class="fa fa-filter"></i>&nbsp;{{ trans('myservice.btn_search') }}</button>
                        </form>
                    </div>
                </div>
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="transactions-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('common.order_tbl_retailer') }}</th>
                                    <th>{{ trans('common.lbl_date') }}</th>
                                    <th>Debit</th>
                                    <th>Previous Balance</th>
                                    <th>Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php  $sum = 0; @endphp
                                @foreach($data as $trans)
                                    @php $da = $trans['prev_bal']; @endphp
                                    <tr @if(in_array($da,$diff)) style="background: red;" @endif>
                                        <th> {{$loop->iteration  }}</th>
                                        <th>{{ $trans['username'] }}</th>
                                        <th>{{ $trans['date'] }}</th>
                                        <th>{{ $trans['debit'] }}</th>
                                        <th>{{ $trans['prev_bal'] }}</th>
                                        <th>{{ $trans['balance'] }}</th>
                                    </tr>
                                    @php $sum += $trans['debit'];@endphp
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3"></th>
                                    <th >{{ $sum }}</th>
                                    <th ></th>
                                    <th ></th>

                                </tr>
                                </tfoot>
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
    <script src="{{ secure_asset('vendor/common/handlebars-v4.0.11.js') }}"></script>
    <script>
        $(function () {
            $(".date").datepicker({
                changeYear: true,
                minDate: '-3M',
                maxDate: new Date(),
                showButtonPanel: true,
                dateFormat: "yy-mm-dd",
                showAnim: "slideDown"
            });
        });

    </script>
@endsection