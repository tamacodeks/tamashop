@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
         ['name' => "Calling cards",'url'=> secure_url('calling-cards'),'active' => 'no'],
         ['name' => $card_name,'url'=> secure_url('calling-cards/'.$card_id),'active' => 'no'],
         ['name' => $page_title,'url'=> '','active' => 'yes'],
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" id="loader">
                                <fieldset class="fieldset-border">
                                    <legend class="legend-border">
                                        {{ $page_title }}
                                    </legend>
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <button id="printMe" class="btn btn-theme center-block m-b-20"><i class="fa fa-print"></i>&nbsp;{{ trans('common.btn_print') }}</button>
                                                <div class="card-block" id="print-content">
                                                    <table style="width: 100%;max-width: 100%;">
                                                        <tr>
                                                            <td style="text-align:center;">
                                                                <?php
                                                                $tp_config =  \App\Models\TelecomProvider::find($provider->id);
                                                                //                                                                    dd($tp_config);
                                                                $src_img = $tp_config->getMedia('telecom_providers_cards')->first();
                                                                $img = !empty($src_img) ? optional($src_img)->getUrl('thumb') : secure_asset('images/card_image.png');
                                                                ?>
                                                                <img class="center-block" src="{{ secure_asset($img) }}" style="margin-bottom: 5px;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;border-top: 1px dashed #322f32;">
                                                                <h1 id="cardName" style="
     margin-top: 5px;font-size: 1.4em;">{{ $card->name }}</h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;border-top: 1px dashed #322f32;">
                                                                <p id="cardDesc" style="
     margin-top: 5px;font-size: .9em;">
                                                                    {{ $card->description }}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;border-top: 1px dashed #322f32;">
                                                                <h1 style="margin-top: 5px;font-size: 17px;"><span>{{ trans('myservice.code_secret') }}</span><br>
                                                                    <span id="cardPin" style="font-size: 1.4em;color: blue">{{ $card->pin }}</span>
                                                                </h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;border-top: 1px dashed #322f32;">
                                                                <h1 style="margin-top: 5px;font-size: 14px;">{{ $card->access_number }}</h1>
                                                            </td>
                                                        </tr>
                                                        @if(!empty($card->validity))
                                                            <tr>
                                                                <td style="border-top:1px dashed #322f32">
                                                                    <h1 style="margin-top: 5px;text-align:center;font-size: 14px;"><span>{{ $card->validity }}</span></h1>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if(!empty($card->comment_1))
                                                            <tr>
                                                                <td style="border-top:1px dashed #322f32">
                                                                    <p style="margin-top: 5px;text-align:center;font-size: 14px;">{{ $card->comment_1 }}</p>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if(!empty($card->comment_2))
                                                            <tr>
                                                                <td style="border-top:1px dashed #322f32">
                                                                    <p style="margin-top: 5px;text-align:center;font-size: 14px;"><{{ $card->comment_2 }}</p>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <td style="border-top: 1px dashed #322f32;">
                                                                <table style="width:100%">
                                                                    <tr>
                                                                        <td>
                                                                            {{ trans('sale.serial') }}
                                                                        </td>
                                                                        <td id="cardSerial" align="right">{{ $card->serial }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            {{ trans('myservice.lbl_print_client') }}
                                                                        </td>
                                                                        <td align="right">{{ auth()->user()->username }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ trans('common.lbl_date') }}</td>
                                                                        <td id="cardDate" align="right">{{ $card->updated_at }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;">
                                                                <img style="margin-top: 5px;" src="{{ secure_asset('images/logo.png') }}" class="center-block"/>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function print_pin() {
            var contents = $("#print-content").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
            //Create a new HTML document.
            frameDoc.document.write('<html><head><title>{{ $page_title }}</title>');
            frameDoc.document.write('</head><body>');
            //Append the external CSS file.
//                       frameDoc.document.write('<link href="style.css" rel="stylesheet" type="text/css" />');
            //Append the DIV contents.
            frameDoc.document.write(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 500);
        }

        $(document).ready(function () {
            var request;
            $("#printMe").click(function () {
                print_pin();
            });
        });
    </script>
@endsection