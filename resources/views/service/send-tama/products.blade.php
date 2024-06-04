@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Send Tama",'url'=> secure_url('send-tama'),'active' => 'no'],
        ['name' => $country_name,'url'=> '','active' => 'yes'],
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($products))
                                    <div class="row">
                                        @foreach ($products as $product)
                                            <?php
                                            $security = new \app\Library\SecurityHelper();
                                            $enc_id = $security->encrypt($product['product_id']);
                                            ?>
                                            <div class="col-xs-6 col-md-3">
                                                <a href="{{ secure_url('send-tama/product/'.$enc_id) }}" style="text-decoration: none">
                                                    <div class="panel panel-default send-tama-panel products">
                                                        <div class="panel-body">
                                                            <img src="{{ $product['product_image'] }}" alt="" class="img-responsive center-block" />

                                                            <h4 class="product-name">{{ $product['product_name'] }}</h4>
                                                        </div>
                                                        <div class="panel-footer">
                                                            <div class="pull-left">
                                                                <h4 class="product-name" style="margin: 5px;">{{ \app\Library\AppHelper::formatAmount('EUR',$product['product_cost']) }}</h4>
                                                            </div>
                                                            <div class="pull-right">
                                                                <a href="{{ secure_url('send-tama/product/'.$enc_id) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> {{ trans('common.lbl_view') }}</a>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <h4>{{ \Lang::get('service.tama_no_products_to_view') }}</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection