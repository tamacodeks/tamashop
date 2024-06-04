@extends('layout.app')
@section('content')
    <?php
    $decipher = new  \app\Library\SecurityHelper();
    $back_to_country_url = secure_url('send-tama/'.$decipher->encrypt($country->id));
    $product_id = $decipher->encrypt($product['product_id']);
    ?>
    @include('layout.breadcrumb',['data' => [
        ['name' => "Send Tama",'url'=> secure_url('send-tama'),'active' => 'no'],
        ['name' => $country->nice_name,'url'=> $back_to_country_url,'active' => 'no'],
        ['name' => $product['product_name'],'url'=> '','active' => 'yes'],
    ]
    ])
    <link href="{{ asset('vendor/intl-input/css/intlTelInput.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ $product['product_image'] }}" class="img-responsive">
                            </div>
                            <div class="col-md-8">
                                <h3>{{ $product['product_name'] }}</h3>
                                <h4>{{ trans('common.order_tbl_price') }} : {{ \app\Library\AppHelper::formatAmount('EUR',$product['product_cost']) }}</h4>
                                {!! $product['product_desc'] !!}
                                <div class="m-t-20">
                                    <a href="{{ $back_to_country_url }}" class="btn btn-warning"> <i class="fa fa-chevron-circle-left"></i> {{ trans('common.btn_back') }}</a>
                                    <a data-toggle="modal" data-target="#confirmModal" href="#" class="btn btn-primary"> <i class="fa fa-money-bill-alt"></i> {{ trans('common.lbl_buy_now') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="confirmModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('service.tama_buy_txt') }} {{ $product['product_name'] }}</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="frmConfirmOrder" action="{{ secure_url('send-tama/confirm/order') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="product_id" value="{{ $product['product_id'] }}">
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="fieldset-border">
                                    <legend class="legend-border">{{ trans('service.tama_sender_infos') }}</legend>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="sender_first_name">{{ trans('users.lbl_user_fname') }}</label>
                                        <div class="col-md-8">
                                            <input type="text" name="sender_first_name" id="sender_first_name" class="form-control" placeholder="{{ trans('service.tama_placeholder_sender') }}" tabindex="1" autofocus="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="sender_last_name">{{ trans('users.lbl_user_lname') }}</label>
                                        <div class="col-md-8">
                                            <input type="text" id="sender_last_name" name="sender_last_name" class="form-control" placeholder="{{ trans('service.tama_placeholder_surname_sender') }}" tabindex="2">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="sender_mobile">{{ trans('users.lbl_mobile_no') }}</label>
                                        <div class="col-md-8">
                                            <input type="tel" name="sender_mobile" id="sender_mobile"  class="form-control" placeholder="{{ trans('service.tama_placeholder_mobile_sender') }}" tabindex="3">                                               <span class="help-block hide" id="span_sender_mobile">{{ trans('users.error_mobile_no') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="sender_email">
                                            {{ trans('users.lbl_user_email') }}
                                        </label>
                                        <div class="col-md-8">
                                            <input type="email" name="sender_email" id="sender_email" class="form-control" placeholder="{{ trans('service.tama_placeholder_email_sender') }}" tabindex="4">
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="fieldset-border">
                                    <legend class="legend-border">{{ trans('service.tama_receiver_infos') }}</legend>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="receiver_first_name">{{ trans('users.lbl_user_fname') }}</label>
                                        <div class="col-md-8">
                                            <input type="text" name="receiver_first_name" id="receiver_first_name" class="form-control" placeholder="{{ trans('service.tama_placeholder_receiver') }}" tabindex="5">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="receiver_last_name">{{ trans('users.lbl_user_lname') }}</label>
                                        <div class="col-md-8">
                                            <input type="text" name="receiver_last_name" id="receiver_last_name" class="form-control" placeholder="{{ trans("service.tama_placeholder_surname_receiver") }}" tabindex="6">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="receiver_mobile">{{ trans('users.lbl_mobile_no') }}</label>
                                        <div class="col-md-8">
                                            <input type="text" name="receiver_mobile" id="receiver_mobile" class="form-control" placeholder="{{ trans('service.tama_placeholder_mobile_receiver') }}" tabindex="7" value="+{{ $country->phone_code }}">
                                            <span class="error help-block hide"
                                                  id="receiver_mobile_error">{{ trans('users.error_mobile_no') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">{{ trans('users.lbl_user_email') }}</label>
                                        <div class="col-md-8">
                                            <input type="email" name="receiver_email" class="form-control" placeholder="{{ trans('service.tama_placeholder_email_receiver') }}" tabindex="8">
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-4">{{ trans('service.tama_lbl_comment_order') }}</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" rows="3" name="order_comment" placeholder="{{ trans('service.tama_placeholder_order_comments') }}" tabindex="8"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <button tabindex="9" disabled="disabled" id="btnSubmit" type="submit" class="btn btn-theme"><i class="fa fa-check-circle"></i>&nbsp;{{ trans('service.tama_btn_confirm_order') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button"  tabindex="10"  class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;{{ trans('common.btn_close') }}</button>
                </div>
            </div>

        </div>
    </div>
    <script src="{{ secure_asset('vendor/intl-input/js/intlTelInput.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {

            $('#frmConfirmOrder').validate({ // initialize plugin
                // rules & options,
                rules: {
                    sender_first_name: "required",
                    sender_mobile: "required",
                    receiver_first_name: "required",
                    receiver_mobile: "required"
                },
                messages: {
                    sender_first_name: "{{ trans('service.tama_val_sender_name') }}",
                    sender_mobile: "{{ trans('service.tama_val_sender_mobile') }}",
                    receiver_first_name: "{{ trans('service.tama_val_receiver_name') }}",
                    receiver_mobile: "{{ trans('service.tama_val_receiver_mobile') }}"
                },
                errorElement: "form-group",
                errorPlacement: function (error, element) {
                    // Add the `help-block` class to the error element
                    error.addClass("help-block");

                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.parent("label"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents(".form-group").addClass("").removeClass("has-error");
                },
                submitHandler: function (form) {
                    $("#btnSubmit").html("<i class='fa fa-spinner fa-pulse'></i>&nbsp;{{ trans('common.processing') }}").attr('disabled', 'disabled');
                    form.submit();
                }
            });

            var telInput_receiver = $("#receiver_mobile"),
                errorMsg_receiver = $("#receiver_mobile_error");

            // initialise plugin
            telInput_receiver.intlTelInput({
                onlyCountries: ["{{ strtolower(optional($country)->iso) }}"],
                utilsScript: "{{ secure_asset('vendor/intl-input/js/utils.js') }}" // just for formatting/placeholders etc
            });
            var reset_receiver = function () {
                telInput_receiver.removeClass("has-error");
                errorMsg_receiver.addClass("hide");
            };
            // on blur: validate
            telInput_receiver.blur(function () {
                reset_receiver();
                if ($.trim(telInput_receiver.val())) {
                    if (telInput_receiver.intlTelInput("isValidNumber")) {
                        telInput_receiver.parents(".form-group").removeClass("has-error");
                        errorMsg_receiver.addClass("hide");
                        if(telInput_sender.intlTelInput('isValidNumber'))
                            $("#btnSubmit").removeAttr('disabled');
                    } else {
                        telInput_receiver.parents(".form-group").addClass("has-error");
                        errorMsg_receiver.removeClass("hide");
                        $("#btnSubmit").attr('disabled', 'disabled');
                    }
                }
                var num = telInput_receiver.val();
                if (num.length == 0 || num === '+') {
                    $(this).val("+{{ optional($country)->phone_code }}");
                }
            });
            telInput_receiver.on('change keyup paste input', function (e) {
                var code = (e.keyCode || e.which);
                // skip arrow keys
                if (code == 37 || code == 38 || code == 39 || code == 40 || code == 8) {
                    return;
                }
                if ($.trim(telInput_receiver.val())) {
                    if (telInput_receiver.intlTelInput("isValidNumber")) {
                        telInput_receiver.parents(".form-group").removeClass("has-error");
                        errorMsg_receiver.addClass("hide");
                        if(telInput_sender.intlTelInput('isValidNumber'))
                            $("#btnSubmit").removeAttr('disabled');
                    } else {
                        telInput_receiver.parents(".form-group").addClass("has-error");
                        errorMsg_receiver.removeClass("hide");
                        $("#btnSubmit").attr('disabled', 'disabled');
                    }
                }
                // if first character is 0 filter it off
                var num = telInput_receiver.val();
                if (num.length == 0 || num === '+') {
                    $(this).val("+{{ optional($country)->phone_code }}");
                }
            });
            // trigger a fake "change" event now, to trigger an initial sync
            telInput_receiver.change();
            setTimeout(function () {
                telInput_receiver.val("+{{ optional($country)->phone_code }}");
            },1000)


            var telInput_sender = $("#sender_mobile"),
                errorMsg_sender = $("#span_sender_mobile");

            // initialise plugin
            telInput_sender.intlTelInput({
                onlyCountries: ["al", "ad", "at", "by", "be", "ba", "bg", "hr", "cz", "dk",
                    "ee", "fo", "fi", "fr", "de", "gi", "gr", "va", "hu", "is", "ie", "it", "lv",
                    "li", "lt", "lu", "mk", "mt", "md", "mc", "me", "nl", "no", "pl", "pt", "ro",
                    "ru", "sm", "rs", "sk", "si", "es", "se", "ch", "ua", "gb"],
                utilsScript: "{{ secure_asset('vendor/intl-input/js/utils.js') }}" // just for formatting/placeholders etc
            });
            var reset_sender = function () {
                telInput_sender.removeClass("has-error");
                errorMsg_sender.addClass("hide");
            };
            // on blur: validate
            telInput_sender.blur(function () {
                reset_sender();
                if ($.trim(telInput_sender.val())) {
                    if (telInput_sender.intlTelInput("isValidNumber")) {
                        telInput_sender.parents(".form-group").addClass("").removeClass("has-error");
                        if(telInput_receiver.intlTelInput('isValidNumber'))
                            $("#btnSubmit").removeAttr('disabled');
                    } else {
                        telInput_sender.parents(".form-group").addClass("has-error").removeClass("");
                        errorMsg_sender.removeClass("hide");
                        $("#btnSubmit").attr('disabled', 'disabled');
                    }
                }
            });
            telInput_sender.on('change keyup paste input', function (e) {
                var code = (e.keyCode || e.which);
                // skip arrow keys
                if (code == 37 || code == 38 || code == 39 || code == 40 || code == 8) {
                    return;
                }
                if ($.trim(telInput_sender.val())) {
                    if (telInput_sender.intlTelInput("isValidNumber")) {
                        telInput_sender.parents(".form-group").addClass("").removeClass("has-error");
                        if(telInput_receiver.intlTelInput('isValidNumber'))
                            $("#btnSubmit").removeAttr('disabled');
                    } else {
                        telInput_sender.parents(".form-group").addClass("has-error").removeClass("");
                        errorMsg_sender.removeClass("hide");
                        $("#btnSubmit").attr('disabled', 'disabled');
                    }
                }
                // if first character is 0 filter it off
                var num = $(this).val();
                if (num.length == '') {
                    $(this).val('+');
                }
            });
            // trigger a fake "change" event now, to trigger an initial sync
            telInput_sender.change();
        });
    </script>
@endsection