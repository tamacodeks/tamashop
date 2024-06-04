<ol class="breadcrumb breadcrumb-custom">
    <li><a href="{{ secure_url('dashboard') }}"><i class="fa fa-home"></i>&nbsp;Dashboard</a></li>
    @foreach($data as $value)
        <li class="@if($value['active'] == 'yes') active @endif">@if($value['active'] == 'no')<a href="{{ $value['url'] }}">{{ $value['name'] }}</a>@else {{ $value['name'] }} @endif</li>
    @endforeach
</ol>