@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Tama App Recharge",'url'=> '','active' => 'yes']
    ]])
    <link href="{{ secure_asset('vendor/intl-input/css/intlTelInput.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">TamaApp {{ trans('service.recharge') }}</div>
                    <div class="panel-body" id="loader">
                        <form id="frmTamaApp" class="form-horizontal" action="{{ secure_url('tama-app/confirm/order') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="control-label col-md-4" for="mobile">{{ trans('users.lbl_mobile_no') }}</label>
                                <div class="col-md-6">
                                    <input type="tel" name="mobile" id="mobile" class="form-control">
                                    <input type="hidden" name="areaCode" id="areaCode">
                                    <input type="hidden" name="currency" id="currency">
                                    <span class="error help-block hide" id="span_mobile">{{ trans('users.error_mobile_no') }}</span>
                                </div>
                            </div>
                            <div class="form-group hide" id="balanceDiv">
                                <label class="control-label col-md-4" id="dummyTxt">{{ trans('service.mytamaapp_cur_bal') }} : </label>
                                <div class="col-md-6">
                                    <h4 style="margin-top: 2px;" id="tamacurrBal" class="lead btn-badge ">00.00 €</h4>
                                </div>
                            </div>
                            <div class="form-group hide" id="amountDiv">
                                <label class="control-label col-md-4" for="amount">{{ trans('common.payment_frm_amount') }}</label>
                                <div class="col-md-6">
                                    <input type="text" name="amount" id="amount" class="form-control money-input">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary" id="btnSubmit">{{ trans('service.mytamaapp_btn_recharge') }}&nbsp;<i class="fa fa-bolt"></i></button>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </form>
                        <input type="hidden" id="moneySymbol">
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <script src="{{ secure_asset('vendor/intl-input/js/intlTelInput.js') }}" type="text/javascript"></script>
    <script>
        var telInput = $("#mobile"),
            errorMsg = $("#span_mobile");
        var fetchCount=0;
        function triggerFetchBalance() {
            if ($.trim(telInput.val())) {
                if (telInput.intlTelInput("isValidNumber")) {
                    telInput.parents(".form-group").addClass("").removeClass("has-error");
                    var intlNumber = telInput.intlTelInput("getNumber");
                    var countryData = telInput.intlTelInput("getSelectedCountryData");
                    var countryCode = countryData.dialCode;
                    countryCode = "+" + countryCode;
                    var newNo = intlNumber.replace(countryCode, "(" + countryCode+ ")" );
                    telInput.val(newNo);
                    $("#areaCode").val(countryData.dialCode);
                    $("#btnSubmit").attr('disabled','disabled');
                    var url = "{{ URL::to('tama-app/balance') }}";
                    $.ajax({
                        method: "POST",
                        url: url,
                        data : { mobile_number : newNo,dial_code : countryData.dialCode },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function () {
                            $('body').append("<span class='loader'></span>");
                        }, success: function (data) {
                            $(".loader").remove();
                            var obj = data;
                            console.log(obj);
                            if (obj.data.code == '200') {
                                $("#balanceDiv").removeClass('hide');
                                $("#amountDiv").removeClass('hide');
                                $("#tamacurrBal").html(obj.data.result.account_bal);
                                $("#moneySymbol").val(obj.data.result.symbol);
                                $("#currency").val(obj.data.result.currency);
                                $("#btnSubmit").removeAttr('disabled');
                            } else if (obj.data.code == '500') {
                                $.alert({
                                    title: '{{ trans('service.warning') }}',
                                    content: obj.data.message
                                });
                                $("#balanceDiv").addClass('hide');
                                $("#amountDiv").addClass('hide');
                            } else {
                                $.alert({
                                    title: '{{ trans('service.warning') }}',
                                    content: obj.data.message
                                });
                                $("#balanceDiv").addClass('hide');
                                $("#amountDiv").addClass('hide');
                            }
                        }, error: function (obj) {
                            console.log(obj);
                            $.alert({
                                title: '{{ trans('service.warning') }}',
                                content: obj.data.data.message
                            });
                            $("#balanceDiv").addClass('hide');
                            $("#amountDiv").addClass('hide');
                            $(".loader").remove();
                        }
                    });
                } else {
                    telInput.parents(".form-group").addClass("has-error").removeClass("");
                    errorMsg.removeClass("hide");
                    $("#balanceDiv").addClass('hide');
                    $("#btnSubmit").attr('disabled', 'disabled');
                }
            }
            fetchCount = 1;
        }
        $(document).ready(function () {

            // initialise plugin
            telInput.intlTelInput({
                nationalMode: true,
                utilsScript: "{{ secure_asset('vendor/intl-input/js/utils.js') }}"
            });
            var reset = function () {
                telInput.removeClass("has-error");
                errorMsg.addClass("hide");
            };
            // on blur: validate
            telInput.blur(function () {
                reset();
                if(fetchCount != 1)
                {
                    triggerFetchBalance();
                }else{

                }
            });
            telInput.on('change keyup paste input focus', function (e) {
                var code = (e.keyCode || e.which);
                // skip arrow keys
                if (code == 37 || code == 38 || code == 39 || code == 40 || code == 8) {
                    return;
                }
                var intlNumber = telInput.intlTelInput("getNumber");
                var countryData = telInput.intlTelInput("getSelectedCountryData");
                var countryCode = countryData.dialCode;
                countryCode = "+" + countryCode;
                var newNo = intlNumber.replace("(" + countryCode+ ")" ,countryCode);
                telInput.val(newNo);
                if ($.trim(telInput.val())) {
                    if (telInput.intlTelInput("isValidNumber")) {
                        telInput.parents(".form-group").addClass("").removeClass("has-error");
                        var intlNumber = telInput.intlTelInput("getNumber");
                        var countryData = telInput.intlTelInput("getSelectedCountryData");
                        var countryCode = countryData.dialCode;
                        countryCode = "+" + countryCode;
                        var newNo = intlNumber.replace(countryCode, "(" + countryCode+ ")" );
                        telInput.val(newNo);
                        $("#btnSubmit").removeAttr('disabled');
                        $("#areaCode").val(countryData.dialCode);
                    } else {
                        telInput.parents(".form-group").addClass("has-error").removeClass("");
                        errorMsg.removeClass("hide");
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
            telInput.change();


            $("#frmTamaApp").submit(function(e){
                e.preventDefault();
                var number = $("#mobile").val();
                var amount = $("#amount").val();
                if(amount == '')
                {
                    triggerFetchBalance();
                }
                else{
                    $.confirm({
                        title: "{{ trans('service.confirm') }}",
                        content: "{{ trans('service.tf_ask_top1up1') }} "+number+"  {{ trans('service.tf_ask_top1up2') }} "+$("#moneySymbol").val()+amount+"?",
                        theme: 'bootstrap', // 'material', 'bootstrap','dark','light'
                        escapeKey: '{{ trans('common.btn_cancel') }}',
                        buttons: {
                            "{{ trans('service.confirm') }}": {
                                text: '{{ trans('service.confirm') }}',
                                keys: ['enter'],
                                action: function () {
                                    $("#loader").LoadingOverlay('show');
                                    if(number == '' || number == '+'){
                                        $.alert({
                                            title: '{{ trans('service.warning') }}',
                                            content: "{{ trans('service.mytamaapp_err_tama_num') }}",
                                        });
                                        $("#loader").LoadingOverlay('hide');
                                    }else if(amount == ''){
                                        $.alert({
                                            title: '{{ trans('service.warning') }}',
                                            content: "{{ trans('service.mytamaapp_err_amount') }}",
                                        });
                                        $("#loader").LoadingOverlay('hide');
                                    }else{
                                        $("#frmTamaApp")[0].submit();
                                    }
                                }
                            },
                            "{{ trans('common.btn_cancel') }}": function () {

                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection