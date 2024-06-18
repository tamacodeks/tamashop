@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => 'Invoice settings','url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Invoice settings</h3>
                    </div>
                    <div class="panel-body">
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ secure_url('invoice-settings/create') }}" onclick="AppModal(this.href,'Create new setting');return false;"><i class="fa fa-plus-circle"></i>&nbsp;{{ trans('common.add_new') }} </a>
                        </div>
                        <br><br>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <td>Sl</td>
                                    <td>Country</td>
                                    <td>Commission</td>
                                    <td>Created at</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php($sl=1)
                                @forelse($invoice_settings as $setting)
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $setting->nice_name }}</td>
                                        <td>{{ $setting->commission }}</td>
                                        <td>{{ $setting->created_at }}</td>
                                        <td>
                                            <a href="{{ secure_url('invoice-settings/edit/'.$setting->id) }}" onclick="AppModal(this.href,'Edit {{ $setting->nice_name }}');return false;" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i>&nbsp;{{ trans('common.lbl_edit') }} </a>
                                            <a href="{{ secure_url('invoice-settings/delete/'.$setting->id) }}" onclick="AppConfirmDelete(this.href,'Confirm','Do you want remove?');return false;" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i>&nbsp;{{ trans('common.btn_delete') }} </a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/date-picker/jquery-ui.js') }}"></script>
    <script src="{{ asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $( function() {
            $( ".date" ).datepicker({
                changeYear: true,
                minDate: '-3M',
                maxDate: new Date(),
                showButtonPanel: true,
                dateFormat : "yy-mm-dd",
                showAnim : "slideDown"
            });
        } );
        $(document).ready(function () {
            $(".select-picker").selectpicker();
        });
    </script>
@endsection