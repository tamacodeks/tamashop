@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' =>[
        ['name' => trans('common.lbl_profile'),'url'=> '','active' => 'yes']
        ]
    ])
    <style>
        .profile
        {
            min-height: 355px;
            display: inline-block;
        }
        figcaption.ratings
        {
            margin-top:20px;
        }
        figcaption.ratings a
        {
            color:#f1c40f;
            font-size:11px;
        }
        figcaption.ratings a:hover
        {
            color:#f39c12;
            text-decoration:none;
        }
        .divider
        {
            border-top:1px solid rgba(0,0,0,0.1);
        }
        .emphasis
        {
            border-top: 4px solid transparent;
        }
        .emphasis:hover
        {
            border-top: 4px solid #1abc9c;
        }
        .emphasis h2
        {
            margin-bottom:0;
        }
        span.tags
        {
            background: #1abc9c;
            border-radius: 2px;
            color: #f5f5f5;
            font-weight: bold;
            padding: 2px 4px;
        }

    </style>
    <link href="{{ asset('vendor/intl-input/css/intlTelInput.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row" style="margin-top:40px;margin-bottom: 100px">
            <div class="col-md-offset-2 col-md-8 col-lg-offset-3 col-lg-6">
                <div class="well profile">
                    <div class="col-sm-12">
                        <div class="col-xs-12 col-sm-8">
                            <h2>{{ auth()->user()->username }}</h2>
                            <p><strong>{{ trans('users.lbl_user_group') }}: </strong> {{ \App\Models\UserGroup::find(auth()->user()->group_id)->name }}. </p>
                            <p><strong>{{ trans('common.created_at') }}: </strong> {{ auth()->user()->created_at }} </p>
                            <p><strong>{{ trans('users.lbl_user_fname') }}: </strong>{{ auth()->user()->first_name }}</p>
                            <p><strong>{{ trans('users.lbl_user_lname') }}: </strong>{{ auth()->user()->last_name }}</p>
                            <p><strong>{{ trans('common.lbl_email') }}: </strong>{{ auth()->user()->email }}</p>
                            <p><strong>{{ trans('users.lbl_mobile_no') }}: </strong>{{ auth()->user()->mobile }}</p>
                            @if(in_array(auth()->user()->group_id,[3,4]))
                                <p><strong>{{ trans('users.lbl_user_transaction_current_balance') }}: </strong><span style="color: #FF0000;font-size: 20px;"> {{ \app\Library\AppHelper::getBalance(auth()->user()->id,auth()->user()->currency,true) }}</span></p>
                                <p><strong>{{ trans('users.lbl_user_transaction_current_credit_limit') }}: </strong>
                                    <span style="color: #FF0000;font-size: 20px;"> {{ \app\Library\AppHelper::get_credit_limit(auth()->user()->id) }}</span>
                                </p>
                            @elseif(auth()->user()->group_id == 2)
                                <p><strong>{{ trans('users.lbl_user_transaction_current_balance') }}: </strong><span style="color: #FF0000;font-size: 20px;"> {{ \app\Library\AppHelper::getAdminBalance(true) }}</span></p>
                                <p><strong>{{ trans('users.lbl_user_transaction_current_credit_limit') }}: </strong>
                                    <span style="color: #FF0000;font-size: 20px;"> {{ \app\Library\AppHelper::getAdminBalance(false,true) }}</span>
                                </p>
                            @endif
                            @if(in_array(auth()->user()->group_id,[4]))
                                <p><strong>{{ trans('users.lbl_user_daily_current_credit_limit') }}: </strong><span style="color: #FF0000;font-size: 20px;"> {{ \app\Library\AppHelper::get_daily_limit(auth()->user()->id,auth()->user()->currency,true) }}</span></p>
                                <p><strong>{{ trans('users.lbl_remaining_limit') }}: </strong>
                                    <span style="color: #FF0000;font-size: 20px;"> {{ \app\Library\AppHelper::get_remaning_limit_balance(auth()->user()->id) }}</span>
                                </p>
                            @endif
                            @if(in_array(auth()->user()->group_id,[4]))
                                <p><strong>{{ trans('login.lbl_ip_address') }}: </strong><span style="color: #FF0000;font-size: 20px;"> {{ auth()->user()->ip_address }}</span></p>
                            @endif
                        </div>
                        <div class="col-xs-12 col-sm-4 text-center m-b-20">
                            <figure>
                                <img src="{{ asset($user_image) }}" alt="" class="img-responsive center-block">
                            </figure>
                        </div>
                    </div>
                    <div class="col-xs-12 divider text-center">
                        <div class="col-xs-12 col-sm-6 emphasis">
                            <h2><strong></strong></h2>
                            <p><small></small></p>
                            <button data-toggle="modal" data-target="#profileModal" class="btn btn-theme btn-block"><span class="fa fa-plus-circle"></span> {{ trans('users.lbl_modal_change_profile') }} </button>
                        </div>
                        <div class="col-xs-12 col-sm-6 emphasis">
                            <h2><strong></strong></h2>
                            <p><small></small></p>
                            <button data-toggle="modal" data-target="#commissionModal" class="btn btn-theme btn-block"><span class="fa fa-percent"></span> {{ trans('users.lbl_user_commission_commission') }} </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="profileModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('common.lbl_edit') }} {{ trans('common.lbl_profile') }}</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" action="{{ secure_url('user/edit/profile') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="control-label col-md-4" for="image">{{ trans('myservice.image') }}</label>
                            <div class="col-md-8">
                                <img src="{{ asset($user_image) }}" id="img_holder" class="img-responsive" alt="">
                                <input type="file" name="image" id="image" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="first_name">{{ trans('users.lbl_user_fname') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="first_name" id="first_name" value="{{ auth()->user()->first_name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="last_name">{{ trans('users.lbl_user_lname') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="last_name" id="last_name" value="{{ auth()->user()->last_name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="email">{{ trans('common.lbl_email') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" type="email" name="email" id="email" value="{{ auth()->user()->email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="mobile">{{ trans('users.lbl_mobile_no') }}</label>
                            <div class="col-md-8">
                                @if(auth()->user()->group_id == 4)
                                    +{{ auth()->user()->mobile }}
                                    <span class="help-text text-info">({{ trans('users.mobile_help_block') }})</span>

                                @else
                                    <input class="form-control" type="text" name="mobile" id="mobile" value="+{{ auth()->user()->mobile }}">
                                    <span id="error-msg"
                                          class="text-danger help-block  hide">{{ trans('users.error_mobile_no') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="password">{{ trans('users.lbl_password') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" type="password" name="password" id="password" value="">
                                <span class="help-text text-info">{{ trans('users.password_help_block') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fa fa-save"></i>&nbsp;{{ trans('common.btn_save') }}</button>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('common.btn_close') }}</button>
                </div>
            </div>

        </div>
    </div>
    <div id="commissionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('users.lbl_user_commission_current_setup') }}</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>{{ trans('users.lbl_user_commission_service') }}</th>
                            <th>{{ trans('users.lbl_user_service_status') }}</th>
                            <th>{{ trans('users.current_commission') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($services) && !empty($services))
                            @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td class="text-center"><span class="label label-{{ \app\Library\AppHelper::user_access($service->id,auth()->user()->id) == 1 ? 'primary' : "danger"}}">{{ \app\Library\AppHelper::user_access($service->id,auth()->user()->id) == 1 ? 'Enabled' : "Disabled"}}</span></td>
                                    @if(auth()->user()->group_id == 2)
                                        <td class="text-center">{{ \app\Library\DBHelper::getAppCommission($service->id) }}%</td>
                                    @else
                                        <td class="text-center">{{ \app\Library\DBHelper::getCommission(auth()->user()->id,$service->id) }}%</td>
                                    @endif
                                </tr>
                            @endforeach
                        @else

                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('common.btn_close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/intl-input/js/intlTelInput.js') }}" type="text/javascript"></script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#img_holder').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(document).ready(function () {
            $("#image").change(function () {
                readURL(this);
            });

            var telInput = $("#mobile"),
                errorMsg = $("#error-msg");

            // initialise plugin
            telInput.intlTelInput({
                initialCountry: "fr",
                nationalMode: true,
                formatOnDisplay: true,
                utilsScript: "{{ asset('vendor/intl-input/js/utils.js') }}"
            });

            var reset = function () {
                telInput.removeClass("error");
                errorMsg.addClass("hide");
            };

            // on blur: validate
            telInput.blur(function () {
                reset();
                if ($.trim(telInput.val())) {
                    if (telInput.intlTelInput("isValidNumber")) {
                        telInput.parents('.form-group').removeClass('has-error');
                        var intlNumber = telInput.intlTelInput("getNumber"); // get full number eg +17024181234
                        var countryData = telInput.intlTelInput("getSelectedCountryData"); // get country data as obj
                        var countryCode = countryData.dialCode; // get the actual code eg 1 for US
                        countryCode = "+" + countryCode; // convert 1 to +1

                        var newNo = intlNumber.replace(countryCode,   countryCode ); // final version
                        telInput.val(newNo);
                        $("#btnSubmit").removeAttr('disabled');
                    } else {
                        telInput.addClass("error");
                        telInput.parents('.form-group').addClass('has-error');
                        errorMsg.removeClass("hide");
                        $("#btnSubmit").attr('disabled','disabled');
                    }
                }
            });

            // on keyup / change flag: reset
            telInput.on("keyup change", reset);
            telInput.on('keyup change paste input', function (e) {
                reset();
                var code = (e.keyCode || e.which);
                // skip arrow keys
                if (code == 37 || code == 38 || code == 39 || code == 40 || code == 8) {
                    return;
                }
                // if first character is 0 filter it off
                var num = $(this).val();
                if (num.length === '') {
                    $(this).val('+');
                }
            });
            setTimeout(function () {
                telInput.val(telInput.intlTelInput("getNumber"))
            }, 1000);
        });
    </script>
@endsection
