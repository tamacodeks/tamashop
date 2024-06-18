@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => 'Invoices','url'=> url('invoices'),'active' => 'no'],
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
                        @if(count($errors))
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.
                                <br/>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" id="frmGenerateInvoice" class="form-horizontal"
                              action="{{ url('invoices/generate/confirm') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="service_id" class="control-label col-md-4">{{ trans('common.users') }}</label>
                                <div class="col-md-8">
                                    <select name="user_id[]" id="user_id" class="select-picker"
                                            data-live-search="true"
                                            title="{{ trans('common.lbl_please_choose') }}" data-size="10"
                                            data-actions-box="true"
                                            multiple>
                                        <option value="">{{ trans('common.lbl_please_choose') }}</option>
                                        @forelse($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="invoice_date" class="control-label col-md-4">Invoice For </label>
                                <div class="col-md-3">
                                <?php
                                $yearArray = range(2018, date("Y"));
                                ?>
                                <!-- displaying the dropdown list -->
                                    <select name="year" id="year" class="form-control">
                                        <option value="">Select Year</option>
                                        <?php
                                        foreach ($yearArray as $year) {
                                            // if you want to select a particular year
                                            $selected = '';
                                            echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
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
                                    <?php
//                                    echo date('F, Y');
//                                    $selected_month = [];
//                                    for ($i = 1; $i < 6; $i++) {
////                                        echo "<br>".date('F Y', strtotime("-$i month"));
////                                        $selected_month[] =
//                                    }
                                    ?>
                                    <select name="month" id="month" class="form-control">
                                        <option value="">Select Month</option>
                                        <?php
                                        foreach ($monthArray as $month) {
                                            // if you want to select a particular month
                                            $selected = '';
                                            // if you want to add extra 0 before the month uncomment the line below
                                            //$month = str_pad($month, 2, "0", STR_PAD_LEFT);
                                            echo '<option '.$selected.' value="'.$month.'">'.$formattedMonthArray[$month].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="invoice_month" class="control-label col-md-4">&nbsp;</label>
                                <div class="col-md-8">
                                    <button type="submit" id="btnSubmit" class="btn btn-primary"><i class="fa fa-file-pdf"></i>&nbsp;Generate
                                        Invoice
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".select-picker").selectpicker();

            $('#frmGenerateInvoice').validate({
                // rules & options,
                rules: {
                    user_id: "required",
                    month: "required",
                    year: "required"
                },
                errorElement: "span",
                errorPlacement: function (error, element) {
                    // Add the `help-block` class to the error element
                    error.addClass("help-block");

                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.parents("checkbox"));
                    } else {
                        // error.insertAfter(element.parents('.form-group'));
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group").addClass("").removeClass("has-error");
                },
                submitHandler: function (form) {
                    $("#btnSubmit").html("<i class='fas fa-sync fa-spin'></i>&nbsp;{{ trans('common.processing') }}...").attr('disabled', 'disabled');
                    form.submit();
                }
            });
        });
    </script>
@endsection