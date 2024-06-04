@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
         ['name' => 'Set up Google Authenticator','url'=> '','active' => 'yes']
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="card-title text-center">Set up Google Authenticator</h5>
                        <p class="card-text text-center">
                            You must set up your Google Authenticator app before continuing. You will be unable to log in otherwise.
                        </p>

                        <div class="text-center mb-4">
                            <p class="card-description">
                                {!! $QR_Image !!}
                            </p>
                        </div>

                        <p class="text-center">
                            Set up your two-factor authentication by scanning the barcode below. Alternatively, you can use the code {{ $secret }}
                        </p>
                        <div class="row">
                            <form class="form-horizontal" method="POST" action="{{ secure_url('verify2fa') }}"   enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="max">Enter TOTP Codet</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('secret') is-invalid @enderror" name="secret" id="secret">
                                    </div>
                                </div>

                                <input type="hidden" name="key" value="1">
                                <div class="col-md-8 pull-right">
                                    <button type="submit" class="btn btn-theme"><i
                                                class="fa fa-save"></i>&nbsp;{{ trans('common.btn_save_changes') }}
                                    </button>
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