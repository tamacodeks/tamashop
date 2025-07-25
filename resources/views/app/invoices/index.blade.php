@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Invoices",'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <style>
        .ui-datepicker-calendar {
            display: none;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.gen_invoice') }}</h3>
                    </div>
                    <div class="panel-body">
                        <div class="pull-right">
                            <a href="{{ secure_url('invoices/generate') }}" class="btn btn-primary"><i
                                        class="fa fa-plus-circle"></i>&nbsp;Generate Invoice</a>
                        </div>
                        <form method="GET" id="search-form" action="{{ secure_url('invoices') }}" class="form-inline"
                              role="form">
                            <div class="form-group">
                                <label for="service_id">{{ trans('common.users') }}</label>
                                <select name="service_id" id="service_id" class="select-picker" data-live-search="true"
                                        title="{{ trans('common.lbl_please_choose') }}" data-actions-box="true"
                                        multiple>
                                    <option value="">{{ trans('common.lbl_please_choose') }}</option>
                                    @forelse($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="invoice_date">Invoice For </label>
                            <?php
                            $yearArray = range(2018, date("Y"));
                            ?>
                            <!-- displaying the dropdown list -->
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select Year</option>
                                    <?php
                                    foreach ($yearArray as $year) {
                                        // if you want to select a particular year
                                        $selected = ($year == $req_year) ? 'selected' : '';
                                        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                                    }
                                    ?>
                                </select>

                            <?php
                            $monthArray = range(1, 12);
                            // set the month array
                            $formattedMonthArray = array(
                                "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                                "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                                "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                            );
                            ?>
                            <!-- displaying the dropdown list -->
                                <select name="month" id="month" class="form-control">
                                    <option value="">Select Month</option>
                                    <?php
                                    foreach ($monthArray as $month) {
                                        // if you want to select a particular month
                                        $selected = ($month == $req_month) ? 'selected' : '';
                                        // if you want to add extra 0 before the month uncomment the line below
                                        //$month = str_pad($month, 2, "0", STR_PAD_LEFT);
                                        echo '<option '.$selected.' value="'.$month.'">'.$formattedMonthArray[$month].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-filter"></i>&nbsp;{{ trans('common.filter_lbl_search') }}</button>
                        </form>
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
                            @php($sl=1)
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $sl }}</td>
                                    <td>{{ $invoice->username }}</td>
                                    <td>
                                        <a  href="{{ secure_url('invoices/view/'.$invoice->user_id."/".$invoice->id) }}"
                                            class="btn btn-primary btn-sm view-pdf" ><i
                                                    class="fa fa-eye"></i>&nbsp;{{ trans('common.lbl_view') }}</a>
                                    </td>
                                </tr>
                                @php($sl++)
                            @empty

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