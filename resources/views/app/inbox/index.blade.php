@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Inbox",'url'=> '','active' => 'yes']
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-3">
                            <ul class="list-group">
                                @foreach($users as $user)
                                    <a  href="{{ secure_url('private.chat.index', $user->id) }}"><li class="list-group-item @if($active_user == $user->id) active @endif">{{ $user->username }} <span class="badge">12</span></li></a>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <h3 class="text-center">Please click user to start conversation!</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection