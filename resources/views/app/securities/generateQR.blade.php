@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
         ['name' => trans('common.setup_google_authenticator'), 'url'=> '', 'active' => 'yes']
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="card-title text-center">{{ trans('common.setup_google_authenticator') }}</h5>
                        <p class="card-text text-center">
                            {{ trans('common.must_setup_google_authenticator') }}
                        </p>

                        <div class="text-center mb-4">
                            <p class="card-description">
                                {!! $QR_Image !!}
                            </p>
                        </div>

                        <p class="text-center">
                            {{ trans('common.setup_two_factor_auth') }} {{ $secret }}
                        </p>
                        <div class="row">
                            <form class="form-horizontal" method="POST" action="{{ secure_url('verify2fa') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="max">{{ trans('common.enter_totp_code') }}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('secret') is-invalid @enderror" name="secret" id="secret">
                                    </div>
                                </div>

                                <input type="hidden" name="key" value="1">
                                <div class="col-md-8 pull-right">
                                    <button type="submit" class="btn btn-theme"><i class="fa fa-save"></i>&nbsp;{{ trans('common.btn_save_changes') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
