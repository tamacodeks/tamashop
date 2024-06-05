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
            <h4 class="mb-0">2FA Verification</h4>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form class="box account-form" id="frmLogin" method="POST">
                {{ csrf_field() }}
                <div class="settings-form__field">
                    <label class="settings-form__field__label" for="totp">Authentication Code</label>
                    <input type="text" class="settings-form__field__input" id="totp" name="totp" placeholder="Authentication Code" minlength="6" maxlength="6" required autofocus>
                </div>
                <button type="button" class="btn btn-danger btn-deep-purple" id="btnCheckOtp">Verify</button>
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
        $("#btnCheckOtp").on("click", function () {
            $.ajax({
                url: '{{ secure_url('validate_otp') }}',
                data: $('#frmLogin').serialize(),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.error) {
                        $.alert({
                            title: '{{ trans('common.info') }}',
                            content: response.error
                        });
                    } else {
                        // Optionally, you can redirect the user here if needed
                        window.location.href = "{{ secure_url('dashboard') }}";
                    }
                },
                error: function (xhr, status, error) {
                    var errorMessage = 'Error: ';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage += xhr.responseJSON.error;
                    } else {
                        errorMessage += xhr.statusText;
                    }
                    $.alert({
                        title: '{{ trans('common.info') }}',
                        content: errorMessage
                    });
                }
            });
        });
    });
</script>
</body>
</html>
