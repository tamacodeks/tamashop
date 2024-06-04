@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('service.manage_service_provider'),'url'=> '','active' => 'yes']
    ]])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>{{ trans('service.manage_service_provider') }}</h4>

                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="tp-config-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>{{ trans('service.sp_primary') }}</th>
                                    <th>{{ trans('service.sp_secondary') }}</th>
                                    <th>{{ trans('common.created_at') }}</th>
                                    <th>{{ trans('common.updated_at') }}</th>
                                    <th>{{ trans('common.mr_tbl_action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="{{ secure_asset('vendor/datatables/datatables.css') }}" rel="stylesheet">
    <script src="{{ secure_asset('vendor/datatables/datatables.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/Buttons-1.5.1/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/Buttons-1.5.1/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/app.js') }}"></script>
    <script src="{{ secure_asset('vendor/common/handlebars-v4.0.11.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#tp-config-table').DataTable({
                "autoWidth": false,
                "pageLength": "-1",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "<span class='loader'></span>"
                },
                serverSide: true,
                ajax: '{{ secure_url('service_provider/fetch') }}',
                columns: [
                    {data: 'primary', name: 'primary',orderable : false,searchable: false},
                    {data: 'secondary', name: 'secondary',orderable : false,searchable: false},
                    {data: 'created_at', name: 'created_at',orderable : false},
                    {data: 'updated_at', name: 'updated_at',orderable : false},
                    {data: 'action', name: 'action',orderable : false,searchable: false}
                ],
            });
        });
    </script>
@endsection