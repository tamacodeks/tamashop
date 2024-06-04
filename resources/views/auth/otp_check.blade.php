<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($page_title) ? $page_title : "Login" }}</title>
    <link href="{{ secure_asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('css/login.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ secure_asset('vendor/font-awesome/css/fontawesome-all.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('vendor/jquery-confirm/jquery-confirm.min.css') }}">
    <script src="{{ secure_asset('vendor/jquery/jquery-3.3.1.js') }}"></script>
</head>
<body class="content">
<main role="main">
    <section class="webapp-auth ">
        <figure class="webapp-auth__figure">
            <img src="{{ secure_asset('images/logo.png') }}" alt="" style="width: 150px;height: auto;">
        </figure>

        <section class="account-form-container">
            <h1 class="tama-login">{{ trans('login.lbl_two_step') }}</h1>
            <form class="box account-form" id="frmLogin" action="{{ secure_url('login') }}" method="POST">
                {{ csrf_field() }}
                <input id="hidden" type="hidden" class="form-control" name="username" value="{{$username}}">
                <input id="hidden" type="hidden" class="form-control" name="password" value="{{$password}}">
                <input id="hidden" type="hidden" class="form-control" name="lang" value="{{$lang}}">
                <div class="settings-form__field">
                    <label class="settings-form__field__label" for="username">{{ trans('login.lbl_otp') }}</label>
                    <input class="settings-form__field__input" type="text" name="otp" id="otp" placeholder="{{ trans('login.lbl_otp') }}" autofocus tabindex="1">
                </div>
                <a href="JavaScript:void(0);" class="btn btn-danger btn-deep-purple" id="btnCheckOtp">{{ trans('login.btn_verify') }}</a>
                <div style="margin-top: 10px;">
                    <div id="some_div"></div>
                    <div id="resend" align="right" style="display: none;"><a href="JavaScript:void(0);" id="re-send" >Resend OTP</a>
                    </div>
                </div>
            </form>
        </section>
    </section>
</main>
<div class="backdrop backdrop--"></div>
<script src="{{ secure_asset('vendor/jquery-validator/jquery.validate.min.js') }}"></script>
<script src="{{ secure_asset('vendor/common/loadingoverlay.min.js') }}"></script>
<script src="{{ secure_asset('vendor/jquery-confirm/jquery-confirm.min.js') }}"></script>
<script>
    $(document).ready(function () {
        document.getElementById("frmLogin").onkeypress = function(e) {
            var key = e.charCode || e.keyCode || 0;
            if (key == 13) {
                e.preventDefault();
            }
        }

        $("#btnCheckOtp").click(function () {
            $.ajax({
                url: '{{ secure_url('check_otp') }}',
                data: $('#frmLogin').serialize(),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.success == 1)
                    {
                        $.alert({
                            title: '{{ trans('common.info') }}',
                            content: 'Incorret OTP'
                        });
                    }
                    else
                    {
                        $("#frmLogin").submit();
                    }
                },
                error: function (response) {
                    $.alert({
                        title: '{{ trans('common.info') }}',
                        content: 'Something Went Wrong'
                    });
                }
            });
        });
        $("#re-send").click(function () {
            $.ajax({
                url: '{{ secure_url('resend_otp') }}',
                data: $('#frmLogin').serialize(),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    if(response.success == 0)
                    {
                        $.alert({
                            title: '{{ trans('common.info') }}',
                            content: 'OTP Send To Your Mobile Number!!!'
                        });
                    }
                },
                error: function (response) {
                    $.alert({
                        title: '{{ trans('common.info') }}',
                        content: 'Something Went Wrong'
                    });
                }
            });
        });
        var timeLeft = 5;
        var elem = document.getElementById('some_div');
        var timerId = setInterval(countdown, 1000);
        function countdown() {
            if (timeLeft == 0) {
                $("#some_div").hide();
                $("#resend").show();
            } else {
                elem.innerHTML = '<h5>Please Wait '+timeLeft+' Seconds to Resend OTP</h5>';
                timeLeft--;
            }
        }
        @if(\Session::has('message'))
        $.alert({
            title: "{{ ucfirst(session('message_type'))  }}",
            content: '{{ session('message')  }}',
            buttons: {
                "{{ trans('common.btn_close') }}": function () {

                }
            },
            backgroundDismiss: true, // this will just close the modal
            type: "{{ \app\Library\AppHelper::message_types(session('message_type'))  }}",
            autoClose: '{{ trans('common.btn_close') }}|5000'
        });
        @endif

        $('#frmLogin').validate({
            // rules & options,
            rules: {
                username: "required",
                password: "required"
            },
            messages: {
                username: "{{ trans('login.err_user') }}",
                password: "{{ trans('login.err_password') }}"
            },
            errorElement: "div",
            errorPlacement: function (error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");

                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.parents("checkbox"));
                } else {
                    error.insertAfter(element.parents('.settings-form__field'));
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("").removeClass("has-error");
            },
            submitHandler: function (form) {
                $("#login-loader").LoadingOverlay("show");
                $("#btnSubmit").html("<i class='fa fa-spinner fa-pulse'></i>").attr('disabled', 'disabled');
                form.submit();

            }
        });

        $('#frmReset').validate({
            // rules & options,
            rules: {
                txtUserName: "required"
            },
            messages: {
                txtUserName: "Username cannot be empty!",
            },
            errorElement: "span",
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
                $("#reset-panel").LoadingOverlay("show");
                $("#btnReset").html("<i class='fa fa-refresh fa-spin'></i>&nbsp;Reset...").attr('disabled', 'disabled');
                form.submit();

            }
        });

    });
</script>
</body>
</html>

