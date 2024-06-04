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
            <img src="{{ secure_asset('images/logo.png') }}" alt="">
        </figure>
        <h1>Current Updating System Please Wait....</h1>
        <nav class="webapp-auth__nav" style="display:none">
            <a href="https://tamaexpress.com" class="btn mode--link">TAMAEXPRESS {{ date('Y') }}</a>
        </nav>
    </section>
</main>
<div class="backdrop backdrop--"></div>
</body>
</html>

