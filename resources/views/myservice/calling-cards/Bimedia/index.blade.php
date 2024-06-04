@extends('layout.app')d
@section('content')
    @include('layout.breadcrumb',['data' => [
         ['name' => "MyService",'url'=> '','active' => 'no'],
         ['name' => "Bimedia Service",'url'=> '','active' => 'yes']
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-euro-sign fa-4x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div style="font-size: 20px;font-weight: 600;">
                                                {{ $total_amount - $remaining_bal }}
                                            </div>
                                            <div style="font-size: 15px;margin-top: 10px;">{{ trans('common.balance') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i id="faSync" class="fa fa-sync fa-4x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <button class="btn btn-primary" id="syncCatalogue">{{ trans('myservice.sync_cat') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery and jQuery UI (REQUIRED) -->
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script type="text/javascript" charset="utf-8">

        $(document).ready(function() {

            $("#syncCatalogue").click(function () {
                var request;
                //call the ajax fn to sync xml
                $('body').append("<span class='loader'></span>");
                $("#faSync").addClass("fa-pulse");
                // Abort any pending request
                if (request) {
                    request.abort();
                }
                // Fire off the request to /form.php
                request = $.ajax({
                    url: "{{ secure_url('bimedia/sync/catalogue') }}",
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                // Callback handler that will be called on success
                request.done(function (response, textStatus, jqXHR){
                    // Log a message to the console
                    console.log(response);
                    if(response.data.code == 200){
                        $.alert({
                            content: response.data.message,
                            buttons: {
                                "{{ trans('common.btn_close') }}": function () {

                                }
                            },
                            type : "green",
                            icon : "fa fa-info-circle",
                            theme: 'material'
                        });
                    }else{
                        $.alert({
                            content: response.data.message,
                            buttons: {
                                "{{ trans('common.btn_close') }}": function () {

                                }
                            },
                            type : "red",
                            icon : "fa fa-exclamation-circle",
                            theme: 'material'
                        });
                    }
                });

                // Callback handler that will be called on failure
                request.fail(function (jqXHR, textStatus, errorThrown){
                    // Log the error to the console
                    console.error(
                        "The following error occurred: "+
                        textStatus, errorThrown
                    );
                });

                // Callback handler that will be called regardless
                // if the request failed or succeeded
                request.always(function () {
                    // enable the inputs
                    $(".loader").remove();
                    $("#faSync").removeClass("fa-pulse");
                });
            });
        });
    </script>
@endsection