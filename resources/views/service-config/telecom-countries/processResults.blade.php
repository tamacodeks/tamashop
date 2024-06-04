@extends('layout.app')

@section('content')
    @include('layout.breadcrumb', [
        'data' => [
            ['name' => trans('common.telecom_provider_countries'), 'url' => '', 'active' => 'yes']
        ]
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Process Results</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach ($results as $result)
                                <li class="list-group-item">
                                    <span class="step">{{ $result['step'] }}</span>:
                                    @if ($result['status'] == 'completed successfully')
                                        <span class="status success">Completed</span>
                                    @elseif ($result['status'] == 'failed')
                                        <span class="status failed">Failed</span>
                                    @else
                                        <span class="status exception">Failed due to exception</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
