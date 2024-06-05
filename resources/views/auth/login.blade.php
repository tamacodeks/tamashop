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
    <section class="webapp-auth">
        <figure class="webapp-auth__figure">
            <img src="{{ secure_asset('images/logo.png') }}" alt="">
        </figure>
        <section class="account-form-container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form class="box account-form" id="frmLogin" action="{{ secure_url('securelogin') }}" method="POST">
                {{ csrf_field() }}
                <div class="settings-form__field">
                    <label class="settings-form__field__label" for="username">{{ trans('users.lbl_user_name') }}</label>
                    <input class="settings-form__field__input" type="text" id="username" value="{{ old('username') }}" name="username" autofocus>
                </div>

                <div class="settings-form__field">
                    <label class="settings-form__field__label" for="password">{{ trans('users.lbl_password') }}</label>
                    <input class="settings-form__field__input" type="password" id="password" name="password" autocomplete="current-password">
                </div>

                <div style="margin-top: 20px;">
                    <div style="float: left;">
                        <label><input tabindex="3" class="remember" name="remember" type="checkbox" value="">{{ trans('login.lbl_remember_me') }}</label>
                    </div>
                    <div style="text-align: right">
                        <select name="lang" id="lang" tabindex="4">
                            <option value="fr" {{ old('lang') == 'fr' ? 'selected' : '' }}>FR</option>
                            <option value="en" {{ old('lang') == 'en' ? 'selected' : '' }}>EN</option>
                        </select>
                    </div>
                </div>
                <button id="btnSubmit" type="submit" class="btn mode--loader mode--primary size--large style--square">
                    <i class="fa fa-lock"></i>&nbsp;{{ trans('login.btn_login') }}
                </button>
            </form>
        </section>
        <nav class="webapp-auth__nav" style="display: none;">
            <a href="https://tamaexpress.com" class="btn mode--link">TAMAEXPRESS {{ date('Y') }}</a>
        </nav>
    </section>
</main>
<div class="backdrop backdrop--"></div>
<script src="{{ secure_asset('vendor/jquery-validator/jquery.validate.min.js') }}"></script>
<script src="{{ secure_asset('vendor/common/loadingoverlay.min.js') }}"></script>
<script src="{{ secure_asset('vendor/jquery-confirm/jquery-confirm.min.js') }}"></script>
<script>
    $(document).ready(function () {
        @if(\Session::has('message'))
        $.alert({
            title: "{{ ucfirst(session('message_type')) }}",
            content: '{{ session('message') }}',
            buttons: {
                "{{ trans('common.btn_close') }}": function () {}
            },
            backgroundDismiss: true, // this will just close the modal
            type: "{{ \app\Library\AppHelper::message_types(session('message_type')) }}",
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
                    error.insertAfter(element.parents("div"));
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

