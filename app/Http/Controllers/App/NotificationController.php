<?php

namespace App\Http\Controllers\App;

use app\Library\AppHelper;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    private $log_title;

    function __construct()
    {
        parent::__construct();
        $this->log_title = "Notification";
    }

    function index()
    {
        $this->data['page_title'] = "Notifications ";
        $this->data['notifications'] = Notification::join('users','users.id','notifications.user_id')->where('user_id',auth()->user()->id)->select([
            'users.username',
            'notifications.*'
        ])->orderBy('id',"DESC")->paginate(20);
        return view('app.notifications.index',$this->data);
    }

    function markAsRead()
    {
        Notification::where('user_id',auth()->user()->id)
            ->where('is_read',0)
            ->update([
                'is_read' => 1,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => auth()->user()->id
            ]);
        AppHelper::logger('info',$this->log_title,"All notifications are marked as read");
        return redirect()
            ->back()
            ->with('message',trans('common.mark_as_read_success'))
            ->with('message_type','success');
    }
}
