@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "View User Banners",'url'=> '#','active' => 'no'],
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 m-t-20">
                                <div class="table-responsive">
                                    <table id="routesTable" class="table table-bordered table-condensed table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ trans('common.menu_users') }}</th>
                                            <th>{{ trans('common.title') }}</th>
                                            <th>{{ trans('common.filter_lbl_from') }}</th>
                                            <th>{{ trans('common.filter_lbl_to') }}</th>
                                            <th>{{ trans('service.tp_image') }}</th>
                                            <th>{{ trans('service.ms_status') }}</th>
                                            <th>{{ trans('common.mr_tbl_action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($banners as $banner)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $banner['username'] }}</td>
                                                <td>{{ $banner['title'] }}</td>
                                                <td>{{ $banner['from_date'] }}</td>
                                                <td>{{ $banner['to_date'] }}</td>

                                                <td>
                                                    <img src="{{secure_asset('images/'.$banner['banner'])}}" class="img-responsive" style="height: 70px;" alt="">
                                                </td>
                                                <td>
                                                    @if($banner['to_date'] >= date("Y-m-d"))
                                                        <span class="label label-primary">Active</span>
                                                    @else
                                                        <span class="label label-danger">InActive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button data-toggle="modal" data-target="#imageview{{$banner['id']}}" class="btn btn-xs btn-primary"> {{ trans('users.lbl_tbl_user_view') }} </button>
                                                </td>
                                            </tr>
                                            <div id="imageview{{$banner['id']}}" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">{{ trans('common.lbl_view') }} </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="{{secure_asset('images/'.$banner['banner'])}}" class="img-responsive" alt="">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('common.btn_close') }}</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link href="{{ secure_asset('vendor/datatables/datatables.css') }}" rel="stylesheet">
    <script src="{{ secure_asset('vendor/datatables/datatables.js') }}"></script>
    <script>
        $(document).ready(function () {

            $('#routesTable').DataTable({
                "autoWidth": false,
                searching: true,
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "{{ trans('common.processing') }}"
                },
            });

            $("[id^=primary_country]").change(function () {
                var country_id = $('option:selected', this).attr('data-country-id');
                if($(this).val() != "")
                {
                    var currentVal = $(this).val();
                    $("#secondary_country_"+ country_id).val('');
                    $("#secondary_country_"+ country_id +" option").each(function(i){
                        if($(this).hasClass(currentVal+'-'+country_id)){
                            $('.'+currentVal+'-'+country_id).hide();
                        }else{
                            $(this).show();
                        }
                    });
                }else{
                    console.log(country_id);
                    $("#secondary_country_"+ country_id +" option").each(function(i){
                        $(this).show();
                    });
                    $("#secondary_country_"+ country_id).val('');
                }
            });
        });
        function updateRouteConfig(countryID,dialCode) {
            console.log('country id ',countryID)
            console.log('dial code ',dialCode)
            var primary,secondary;
            primary = $("#primary_country_"+countryID).val();
            secondary = $("#secondary_country_"+countryID).val();
            console.log('primary config ',primary)
            console.log('secondary config',secondary)
            if(countryID != "" && dialCode != "" && primary != "")
            {
                $.confirm({
                    title: 'Confirm!',
                    content: 'Do you want to update?',
                    buttons: {
                        confirm: function () {
                            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            var request = $.ajax({
                                type: "POST",
                                url: "{{ secure_url('tamatopup/routes/update') }}",
                                data: {_token: CSRF_TOKEN,country_id: countryID, dial_code: dialCode, primary: primary,secondary:secondary},
                                dataType: "html"
                            });

                            request.done(function(msg) {
                                // console.log(msg)
                                var obj = $.parseJSON(msg)
                                $.alert({
                                    title: 'Success',
                                    content: obj.message,
                                });
                            });

                            request.fail(function(jqXHR, textStatus) {
                                alert( "Request failed: " + textStatus );
                            });
                        },
                        cancel: function () {

                        },
                    }
                });
            }else{
                $.alert({
                    title: 'Alert!',
                    content: 'Please fill fields!',
                });
            }
        }
    </script>
@endsection