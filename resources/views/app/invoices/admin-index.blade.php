@extends('layout.app')
@section('content')
    @include('layout.breadcrumb', ['data' => [
        ['name' => "Invoices", 'url' => '', 'active' => 'yes']
    ]])

    <link href="{{ asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.gen_invoice') }}</h3>
                    </div>
                    <div class="panel-body">
                        <!-- Admin-only options -->
                        @if(auth()->user()->group_id == 1)
                            <div class="pull-right">
                                <a href="{{ secure_url('invoices/generate') }}" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i>&nbsp;Generate Invoice
                                </a>
                            </div>
                            <form method="GET" id="search-form" action="{{ secure_url('invoices') }}" class="form-inline" role="form">
                                <div class="form-group">
                                    <label for="service_id">{{ trans('common.users') }}</label>
                                    <select name="service_id[]" id="service_id" class="select-picker" data-live-search="true"
                                            title="{{ trans('common.lbl_please_choose') }}" data-actions-box="true" multiple>
                                        <option value="">{{ trans('common.lbl_please_choose') }}</option>
                                        @forelse($users as $user)
                                            <option value="{{ $user->id }}"
                                                    @if(is_array(request()->service_id) && in_array($user->id, request()->service_id))
                                                    selected
                                                    @endif>
                                                {{ $user->username }}
                                            </option>
                                        @empty
                                        <!-- No users available -->
                                        @endforelse
                                    </select>
                                </div>
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i>&nbsp;{{ trans('common.filter_lbl_search') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <td>Sl</td>
                                <td>Username</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody>
                            @php($sl = 1)
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $sl }}</td>
                                    <td>{{ $invoice->username }}</td>
                                    <td>
                                        <a href="{{ secure_url('invoices/view/'.$invoice->user_id."/".$invoice->id) }}"
                                           class="btn btn-primary btn-sm view-pdf">
                                            <i class="fa fa-eye"></i>&nbsp;{{ trans('common.lbl_view') }}
                                        </a>
                                    </td>
                                </tr>
                                @php($sl++)
                            @empty
                                <tr>
                                    <td colspan="3">{{ trans('common.no_records_found') }}</td>
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
