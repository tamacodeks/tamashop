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
                              action="{{ Request::is('payment_invoice*') ? secure_url('invoices/payments') : secure_url('invoices') }}">
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
                            <li @if($singleornot == 1) class="active" @endif><a href="{{ url('payment_invoice') }}{{ request()->has('period') ? '?period=' . request('period') : '' }}">Details des transactions</a></li>
                            <li @if($singleornot == 0) class="active" @endif ><a href="{{ url('invoices') }}{{ request()->has('period') ? '?period=' . request('period') : '' }}">Facture</a></li>
                        </ul>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <td>Sl</td>
                                <td>Username</td>
                                <td>Invoice For</td>
                                <td>Period</td>
                                <td>Invoice ID</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody>
                            @php($sl=1)
                            @forelse($invoices as $invoice)
                                @if(($singleornot == 1 && $invoice->service == 'each_payment') || ($singleornot != 1 && $invoice->service != 'each_payment'))
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $invoice->username }}</td>
                                        <td>{{ $invoice->service == 'each_payment' ? 'Payment' : ucfirst(str_replace("-", " ", $invoice->service)) }}</td>
                                        <td>{{ date("F", mktime(0, 0, 0, $invoice->month, 10)) . " " . $invoice->year }}</td>
                                        <td>{{ $invoice->invoice_ref }}</td>
                                        <td>
                                            <a target="_blank"
                                               href="{{ secure_url('invoices/download/'.$invoice->id."/".$invoice->service) }}"
                                               class="btn btn-default btn-sm"><i class="fa fa-download"></i>&nbsp;Download</a>
                                        </td>
                                    </tr>
                                    @php($sl++)
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6">No invoices found</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                        {!! $invoices->render() !!}
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