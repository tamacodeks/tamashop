@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('common.gen_invoice'),'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.gen_invoice') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form method="GET" id="search-form" class="form-inline" role="form"
                              action="{{ secure_url("payment_invoice") }}">
                            @if(auth()->user()->group_id == 3)
                                <div class="form-group">
                                    <label for="service_id">{{ trans('common.users') }}</label>
                                    <select name="service_id" id="service_id" class="select-picker" multiple
                                            data-live-search="true" title="{{ trans('common.lbl_please_choose') }}"
                                            data-actions-box="true">
                                        <option value="">{{ trans('common.lbl_please_choose') }}</option>
                                        @forelse($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="invoice_date">Invoice For </label>
                                <?php
                                $start = new \Carbon\Carbon('first day of last month');
                                $end = new \Carbon\Carbon('last day of last month');
                                $lastMonth = $start->toDateString()." ".$end->toDateString();

                                $first_day_of_the_current_month = \Carbon\Carbon::today()->startOfMonth();
                                $last_day_of_the_current_month = $first_day_of_the_current_month->copy()->endOfMonth();

                                $first_day_of_month_3 = $first_day_of_the_current_month->copy()->subMonth(3);
                                $first_day_of_month_6 = $first_day_of_the_current_month->copy()->subMonth(6);

                                $last3month =  $first_day_of_month_3->toDateString()." ".$last_day_of_the_current_month->toDateString();
                                $last6month =  $first_day_of_month_6->toDateString()." ".$last_day_of_the_current_month->toDateString();
                                ?>
                                <select name="period" id="period" class="form-control">
                                    <option value="">Select Period</option>
                                    <option value="{{ $lastMonth }}" @if($req_period == $lastMonth) selected @endif>Last month</option>
                                    <option value="{{ $last3month }}" @if($req_period == $last3month) selected @endif>Last 3 months</option>
                                    <option value="{{ $last6month }}" @if($req_period == $last6month) selected @endif>Last 6 months</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-filter"></i>&nbsp;{{ trans('common.filter_lbl_search') }}</button>
                        </form>
                    </div>
                </div>
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li><a href="{{ url('invoices') }}{{ request()->has('period') ? '?period=' . request('period') : '' }}">Invoice</a></li>
                            <li class="active"><a href="{{ url('payment_invoice') }}{{ request()->has('period') ? '?period=' . request('period') : '' }}">Payments</a></li>
                        </ul>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <td>Sl</td>
                                <td>Username</td>
                                <td>Customer ID</td>
                                <td>Payment Date</td>
                                <td>Amount</td>
                                <td>Previous Balance</td>
                                <td>Balance</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody>
                            @php($sl=1)
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $sl }}</td>
                                    <td>{{ $payment->username }}</td>
                                    <td>{{ $payment->cust_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ number_format($payment->prev_bal, 2) }}</td>
                                    <td>{{ number_format($payment->balance, 2) }}</td>
                                    <td>
                                        <a target="_blank"
                                           href="{{ secure_url('payments/download/'.$payment->id) }}"
                                           class="btn btn-default btn-sm"><i
                                                    class="fa fa-download"></i>&nbsp;Download</a>
                                    </td>
                                </tr>
                                @php($sl++)
                            @empty
                                <tr>
                                    <td colspan="8">No Payments Found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {!! $payments->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".select-picker").selectpicker();
        });
    </script>
@endsection
