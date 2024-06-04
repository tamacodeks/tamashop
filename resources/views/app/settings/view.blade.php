@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "View Banners",'url'=> '#','active' => 'no'],
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
                                            <th>Total Banners</th>
                                            <th>Active Banners</th>
                                            <th>{{ trans('common.mr_tbl_action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($banners as $banner)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $banner['username'] }}</td>
                                                <td>{{ $banner['total_banner'] }}</td>
                                                <td>
                                                    @foreach($active as $ban)
                                                        @if($banner['id'] == $ban['id'])
                                                            {{$ban['total_banner']}}
                                                        @endif
                                                    @endforeach
                                                </td>

                                                <td>
                                                    <a href="{{ secure_url('view/banner/'.$banner['id']) }}" class="btn btn-xs btn-primary" target="_blank" >&nbsp;View
                                                    </a>
                                                </td>

                                            </tr>
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
        });

    </script>
@endsection