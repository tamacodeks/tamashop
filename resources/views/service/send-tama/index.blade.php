@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Send Tama",'url'=> '','active' => 'yes']
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="row">
                                    @if(isset($countries))
                                        @foreach($countries as $country)
                                            <?php
                                            $decipher = new \app\Library\SecurityHelper();
                                            $enc_id = $decipher->encrypt($country['countryID']);
                                            ?>
                                            <div class="col-xs-6 col-md-3">
                                                <a href="{{ secure_url('send-tama/'.$enc_id) }}" >
                                                    <div class="panel panel-default send-tama-panel">
                                                        <div class="panel-body">
                                                            <img src="{{ $country['country_img_path']  }}" alt="" class="img-responsive center-block" />
                                                        </div>
                                                        <div class="panel-footer">
                                                            <a href="{{ secure_url('send-tama/'.$enc_id) }}" style="text-decoration: none;color: #000;font-size: 18px">
                                                                {{ $country['country_name'] }}</a>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection