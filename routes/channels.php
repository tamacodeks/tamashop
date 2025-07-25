<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat-room-{chatRoom}', function ($user, $chatRoom) {
    $chatRoom = App\Models\ChatRoom::find($chatRoom);
    if(in_array(auth()->user()->id, explode(',', $chatRoom->user_ids))) {
        return true;
    } else {
        return false;
    }
});

Broadcast::channel('notify-user-{user_id}', function ($user, $user_id) {
    \Illuminate\Support\Facades\Log::info('=====>'.$user_id);
    return (int) $user->id === (int) $user_id;
});

Broadcast::channel('room-events-{chatRoom}', function ($user, $chatRoom) {
    return $user;
});

Broadcast::channel('typing-room-{chatRoom}', function ($user, $chatRoom) {
    return $user;
});
