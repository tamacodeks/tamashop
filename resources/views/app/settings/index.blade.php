@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Banners",'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ $page_title }}</h3>
                    </div>
                <div class="panel">
                    <div class="panel-body">
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
                                            <button data-toggle="modal" data-target="#imageview{{$banner['id']}}" class="btn btn-xs btn-primary"> {{ trans('users.lbl_tbl_user_view') }} </button>
                                            <a href="{{ secure_url('edit/banner/'.$banner['id']) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i>&nbsp;Edit
                                            </a>
                                            <a onclick="AppConfirmDelete(this.href,'{{ trans('common.ask_remove')}} {{  $banner['title']}}');return false;" href="{{secure_url('del_banner/' . $banner->id)}}" class="btn btn-xs btn-danger"> <i class="fa fa-times-circle"></i>{{trans('common.btn_delete')}}</a>
                                        </td>
                                    </tr>
                                    <div id="imageview{{$banner['id']}}" class="modal fade" role="dialog">
                                        <div class="modal-dialog">

                                            <!-- Modal content-->
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
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ $add_title }}</h3>
                    </div>
                    <div class="panel-body">
                        <form id="frmPayment" class="form-horizontal" action="{{ url('add/banner') }}" method="POST"  enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @if(!empty($row))
                                <input type="hidden" name="id" value="{{ $row['id'] }}">
                            @endif
                            <div class="form-group">
                                <label class="control-label col-md-4" for="amount">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="title" id="title" @if(!empty($row))value="{{ $row['title'] }}"@endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4" for="amount">From Date</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control date" name="from" id="from" @if(!empty($row))value="{{ $row['from_date'] }}"@endif  required >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4" for="amount">To Date</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control date" name="to" id="to" @if(!empty($row))value="{{ $row['to_date'] }}"@endif  required>
                                </div>
                            </div>
                            @if(!empty($row))
                                <div class="form-group images_hide">
                                    <label class="control-label col-md-4" for="amount"></label>
                                    <div class="col-md-6">
                                        <img src="{{secure_asset('images/'.$row['banner'])}}" class="img-responsive" style="height: 150px;" alt="">
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="control-label col-md-4" for="amount">Banner</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control images" name="image" id="image"  @if(!empty($row)) value="{{ $row['banner'] }}"@else required @endif >
                                    <span class="text-danger error">Image above 1000 * 500 below 1400 * 900 </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fa fa-save"></i>&nbsp;Create Banner</button>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <link href="{{ secure_asset('vendor/datatables/datatables.css') }}" rel="stylesheet">
    <script src="{{ secure_asset('vendor/datatables/datatables.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/app.js') }}"></script>
    <script src="{{ secure_asset('vendor/date-picker/jquery-ui.js') }}"></script>
    <script src="{{ secure_asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".images").change(function () {
                $(".images_hide").hide();
            });
            $('#routesTable').DataTable({
                "autoWidth": false,
                searching: true,
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "{{ trans('common.processing') }}"
                },
            });
                $( function() {
                    $( ".date" ).datepicker({
                        changeYear: true,
                        minDate: new Date(),
                        maxDate: '1m',
                        showButtonPanel: true,
                        dateFormat : "yy-mm-dd",
                        showAnim : "slideDown"
                    });
                });
        });
    </script>
    <script type="text/javascript">
        $(function () {
            $("#image").bind("change", function () {
                var fileUpload = $("#image")[0];
                var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.jpg|.png|.gif)$");
                if (regex.test(fileUpload.value.toLowerCase())) {
                    if (typeof (fileUpload.files) != "undefined") {
                        var reader = new FileReader();
                        reader.readAsDataURL(fileUpload.files[0]);
                        reader.onload = function (e) {
                            var image = new Image();
                            image.src = e.target.result;
                            image.onload = function () {
                                var height = this.height;
                                var width = this.width;
                                if (width < 1000 || width  > 1400 || height < 500 || height > 900) {
                                    $('.error').text('Image above 1000 * 500 below 1400 * 900');
                                    $('#image').val('');
                                    return false;
                                }
                                $('.error').text('');
                                return true;
                            };
                        }
                    } else {
                        $('.error').text('This browser does not support HTML5.');
                        $('#image').val('');
                        return false;
                    }
                } else {
                    $('.error').text('Please select a valid Image file.');
                    $('#image').val('');
                    return false;
                }
            });
        });
    </script>
@endsection