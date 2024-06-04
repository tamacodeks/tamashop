<?php

namespace App\Http\Controllers\App;

use app\Library\AppHelper;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class UserGroupController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $page_data = [
            'page_title' => "User Groups"
        ];
        return view('app.user-groups.index', $page_data);
    }

    function getUserGroups()
    {
        $query = UserGroup::select('id', 'name', 'description', 'status', 'level_access', 'created_at', 'updated_at');
        $query->orderBy('id', "ASC");
        $user_groups = $query;
        return Datatables::of($user_groups)
            ->addColumn('action', function ($user_groups) {
                return '<a href="' . secure_url('user-group/update/' . $user_groups->id) . '" class="btn btn-xs btn-primary" onclick="AppModal(this.href,\'' . trans('common.btn_add') . ' ' . trans('users.lbl_user_group') . '\');return false;"><i class="fa fa-edit"></i> ' . trans('common.lbl_edit') . '</a>&nbsp;&nbsp;<a onclick="AppConfirmDelete(this.href,\'' . trans('common.lbl_remove') . ' ' . $user_groups->username . '\',\'' . trans('common.ask_remove') . '\' );return false;" href="' . secure_url('user-group/remove/' . $user_groups->id) . '" class="btn btn-xs btn-danger"><i class="fa fa-times-circle"></i> ' . trans('common.btn_delete') . '</a>';
            })
            ->addColumn('level_access', function ($user_groups) {
                $user_access = UserGroup::whereIn('id', explode(',', $user_groups->level_access))->select('name')->get()->pluck('name')->toArray();
                $collection = collect($user_access)->flatten();
//                dd($user_access);
                return implode(', ', $collection->all());
            })
            ->editColumn('status', function ($user_groups) {
                return $user_groups->status == 1 ? "<label class='label label-primary'>" . trans('common.lbl_enabled') . "</label>" : "<label class='label label-danger'>" . trans('common.lbl_disabled') . "</label>";
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    function edit($id = '')
    {
        if ($id != '') {
            $row = UserGroup::find($id)->toArray();
            $level_access = !empty($row['level_access']) ? explode(',', $row['level_access']) : [];
        } else {
            $row = array(
                'id' => '',
                'name' => '',
                'description' => '',
                'status' => ''
            );
            $level_access = [];
        }
        $page_data = [
            'row' => $row,
            'user_groups' => UserGroup::select('id', 'name')->get(),
            'level_access' => $level_access
        ];
        return view('app.user-groups.update', $page_data);
    }

    function update(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:85'
        ]);
        if ($validator->fails()) {
            AppHelper::logger('warning', 'UserGroup Update Failed', 'Unable to update user group', $request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message', $html)
                ->with('message_type', 'warning');
        }
        $user_access = !empty($request->level_access) ? $request->level_access : [];
        $level_access = implode(',', $user_access);
        if ($request->id != '') {
            UserGroup::where('id', $request->id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => !empty($request->status) ? 1 : 0,
                'level_access' => $level_access,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id
            ]);
        } else {
            UserGroup::insert([
                'name' => $request->name,
                'description' => $request->description,
                'status' => !empty($request->status) ? 1 : 0,
                'level_access' => $level_access,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => auth()->user()->id
            ]);
        }
        AppHelper::logger('success', 'UserGroup Update', 'UserGroup has been updated', $request->all());
        return redirect('user-groups')->with('message', trans('common.msg_update_success'))->with('message_type', 'success');
    }


    function delete($id)
    {
        $user_group = UserGroup::find($id);
        $user_group->delete();
        AppHelper::logger('success', 'UserGroup Delete', 'UserGroup has been deleted', $user_group);
        return redirect('user-groups')->with('message', trans('common.msg_remove_success'))->with('message_type', 'success');
    }
}
