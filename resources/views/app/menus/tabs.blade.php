<ul class="nav nav-tabs">
    @foreach(\App\Models\UserGroup::all() as $service)
        <li class="@if($user_group_id == $service->id) active @endif"><a href="{{ secure_url('menus?template='.$service->id) }}">{{ $service->name }}</a></li>
    @endforeach
</ul>