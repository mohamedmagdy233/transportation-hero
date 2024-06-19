<?php

namespace App\Repository;

use App\Interfaces\UserInterface;
use App\Models\User;
use App\Traits\PhotoTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\FirebaseNotification;
use Mockery\Expectation;

class UserRepository implements UserInterface
{
    use PhotoTrait, FirebaseNotification;

    public function indexPerson($request)
    {
        if ($request->ajax()) {
            $users = User::query()
                ->where('type', '=', 'user')->latest()->get();
            return DataTables::of($users)
                ->addColumn('action', function ($users) {
                    return '
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $users->id . '" data-title="' . $users->name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->editColumn('image', function ($users) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($users->img) . '">
                    ';
                })
                ->editColumn('status', function ($users) {
                    if ($users->status == 1)
                        return '<button class="btn btn-sm btn-success statusBtn" data-id="' . $users->id . '">مقبول</button>';
                    else
                        return '<button class="btn btn-sm btn-danger statusBtn" data-id="' . $users->id . '">غير مقبول</button>';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.user.index_person');
        }
    }


    public function indexCompany($request)
    {
        if ($request->ajax()) {
            $users = User::query()
                ->where('user_type', 'company')->latest()->get();
            return DataTables::of($users)
                ->addColumn('action', function ($users) {
                    return '
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $users->id . '" data-title="' . $users->name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->editColumn('image', function ($users) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($users->image) . '">
                    ';
                })
                ->editColumn('status', function ($user) {
                    if ($user->status == 1)
                        return '<button class="btn btn-sm btn-success statusBtn" data-id="' . $user->id . '">مفعل</button>';
                    else
                        return '<button class="btn btn-sm btn-danger statusBtn" data-id="' . $user->id . '">غير مفعل</button>';
                })
                ->editColumn('city_id', function ($user) {
                    return $user->city->name_ar;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.user.index_company');
        }
    }

    public function delete($request)
    {
        $admin = User::where('id', $request->id)->first();

        if (file_exists($admin->image)) {
            unlink($admin->image);
        }
        $admin->delete();
        return response(['message' => 'تم الحذف بنجاح', 'status' => 200], 200);
    }

    public function changeStatusUser($request)
    {

        try {

            $request->validate([
                'id' => 'required|integer|exists:users,id',
            ]);

            $user = User::findOrFail($request->id);

            ($user->status == 1) ? $user->status = 0 : $user->status = 1;

            $user->save();

            $notificationData = [
                'title' => 'تفعيل الحساب',
                'body' => ($user->status == 1) ? 'تم تفعيل حسابك من قبل الادمن' : 'تم تعطيل حسابك من قبل الادمن',
            ];

            return $this->sendFirebaseNotification($notificationData, $user->id, 'user');

            return response()->json('200');
        } catch (Expectation $e) {
            return response()->json('201');
        }
    }
}
