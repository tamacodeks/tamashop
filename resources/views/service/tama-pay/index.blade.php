@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
     ['name' => "Tama Pay",'url'=> '','active' => 'yes']
    ]])
    <style>
        #tama_pin-error{
            color: #ff0000;
        }

    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="text-center">
                            <form class="form-inline" id="frmSearchPin" action="{{ secure_url('tama-pay') }}" method="GET">
                                <div class="input-group">
                                    <input name="tama_pin" id="tama_pin" type="text" class="form-control" placeholder="{{ trans('service.tamapay_txt_placeholder') }}" autofocus autocomplete="false" value="{{ $tama_pin }}">
                                    <span class="input-group-btn">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
            </span>
                                </div>
                                <span class="tmperr"></span>
                            </form>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if(isset($order_data) && !empty($order_data))
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="h-padding">{{ trans('service.tamapay_lbl_tama_pin') }} : {{ $order_data['tama_pin'] }} </h4>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="h-padding pull-right">{{ trans('service.tamapay_lbl_updated_on') }} : {{ $order_data['timestamp'] }} </h4>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="h-bottom">{{ trans('service.tamapay_lbl_order_info') }}</h4>
                                    <div class="h-div">
                                        <h5 class="h-padding">{{ trans('service.tamapay_lbl_order_id') }} : {{ $order_data['order_id'] }}</h5>
                                        <h5 class="h-padding">{{ trans('service.tamapay_lbl_order_status') }}: <span class="order-status"><span class="label label-danger">{{ $order_data['order_status'] }}</span></span> </h5>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="h-bottom">{{ trans('service.tamapay_lbl_sender_info') }}</h4>
                                    <div class="h-div">
                                        <h5 class="h-padding">{{ trans('service.tamapay_lbl_name') }} : {{  $order_data['sender_name'] }}</h5>
                                        <h5 class="h-padding">{{ trans('service.tamapay_lbl_contact') }} : {{ $order_data['sender_mobile'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="h-bottom">{{ trans('service.tamapay_lbl_receiver_info') }}</h4>
                                    <div class="h-div">
                                        <h5 class="h-padding">{{ trans('service.tamapay_lbl_name') }}  : {{ $order_data['receiver_name'] }}</h5>
                                        <h5 class="h-padding">{{ trans('service.tamapay_lbl_contact') }}  : {{ $order_data['receiver_mobile'] }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('service.tamapay_lbl_prod_name') }}</th>
                                                <th>{{ trans('service.tamapay_lbl_prod_price') }}</th>
                                                <th>{{ trans('common.lbl_qty') }}</th>
                                                <th>{{ trans('common.lbl_desc') }}</th>
                                                <th class="text-right">{{ trans('common.lbl_stotal') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($order_json) && !empty($order_json))
                                                @foreach($order_json as $product)
                                                    <tr>
                                                        <td>{{ $product['prod_name'] }}</td>
                                                        <td class="text-center">{{ isset($product['unit_price']) ? $product['unit_price'] : number_format($product['orig_price'],2) }}</td>
                                                        <td>{{ isset($product['prod_qty']) ? $product['prod_qty'] : "" }}</td>
                                                        <td>{!! $product['prod_desc'] !!}</td>
                                                        <td class="text-right">{{ \app\Library\AppHelper::formatAmount('EUR',$product['orig_price']) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" style="text-align: center"><p class="lead">{{ trans('service.tamapay_lbl_no_product') }}</p></td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td colspan="3" class="noborders"></td>
                                                <th class="text-right" scope="row">{{ trans('common.lbl_total') }}</th>
                                                <td class="text-right">{{ isset($order_data['order_amount']) ? \app\Library\AppHelper::formatAmount('EUR',$order_data['order_amount']) : "" }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>{{ trans('service.tamapay_lbl_ord_comment') }}</h5>
                                    <div class="well">
                                        {!! isset($order_data['order_note']) ? $order_data['order_note'] : "" !!}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <form id="hidtamapayform" action="{{ secure_url('tama-pay/confirm/order') }}" method="POST"  role="form">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="order_pin" value="{{ isset($order_data['tama_pin']) ? $order_data['tama_pin'] : "" }}">
                                        <input type="hidden" name="order_id" value="{{ isset($order_data['order_id']) ? $order_data['order_id'] : "" }}">
                                        <input type="hidden" name="order_amount" value="{{ isset($order_data['order_amount']) ? $order_data['order_amount'] : "" }}">
                                    </form>
                                    <div class="text-center">
                                        <?php
                                        $title = $order_data['order_status'] == "Accepted" ? "Make Payment" : "Payment already received by others for this order!";
                                        ?>
                                        <button type="button" id="btnMakePayment" class="btn btn-theme" data-toggle="tooltip" data-placement="top" title="{{ $title }}" ><i class="fa fa-check-circle"></i>&nbsp;{{ trans('service.tamapay_btn_make_payment') }}
                                        </button>
                                        <a href="{{ secure_url('service/tama-pay') }}" class="btn btn-danger"><i
                                                    class="fa fa-remove"></i> {{ trans('common.btn_cancel') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-center lead">{{ trans('service.tamapay_txt_ask_user') }}</h5>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('#frmSearchPin').validate({
                // rules & options,
                rules: {
                    tama_pin: {
                        required: true,
                        minlength: 9,
                        maxlength: 9
                    }
                },
                messages: {
                    tama_pin: "{{ trans('service.tamapay_txt_ask_user') }}",
                },
                errorElement: "span",
                errorPlacement: function (error, element) {
                    // Add the `help-block` class to the error element
                    error.addClass("help-block text-danger ");

                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.parent("label"));
                    } else {
                        error.insertAfter(".tmperr");
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parents(".tmperr").addClass("has-error").removeClass("has-success");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents(".tmperr").addClass("").removeClass("has-error");
                },
                submitHandler: function (form) {
                    $("#btnSubmit").html("<i class='fa fa-spinner fa-pulse'></i>").attr('disabled', 'disabled');
                    $('#frmSearchPin').attr('action',"{{ secure_url('tama-pay') }}");
                    form.submit();
                }
            });


            var checkTamaPin = "{{ isset($order_data['order_status']) ? $order_data['order_status'] : "Unknown" }}";
            if (checkTamaPin == 'Accepted') {

            } else {
                $("#btnMakePayment").attr("disabled", 'disabled');
//                $("#btnMakePayment").attr("title", 'Please enter tama pin to make payment!');
            }
            //disable search button
            $("#btnFindPin").attr('disabled', 'disabled');

            $("#btnMakePayment").click(function(e){
                e.preventDefault();
                // Confirm
                $.confirm({
                    title: "{{ trans('service.tama_btn_confirm_order') }}",
                    content: "{{ trans('common.lbl_ask_proceed_form') }}",
                    autoClose: '{{ trans('common.btn_cancel') }}|10000',
                    theme: 'material', // 'material', 'bootstrap','dark','light'
                    buttons: {
                        "{{ trans('common.lbl_yes') }}": {
                            text: '{{ trans('common.lbl_yes') }}',
                            btnClass: 'btn-theme',
                            action: function () {
                                $("#hidtamapayform").submit();
                            }
                        },
                        "{{ trans('common.btn_cancel') }}": function () {

                        }
                    },
                    escapeKey: '{{ trans('common.btn_cancel') }}',
                    draggable : false,
                    animation: 'zoom',
                    closeAnimation: 'bottom',
                    type: 'green',
                    icon : "fa fa-check-circle",


                });
            });

        });
    </script>
@endsection