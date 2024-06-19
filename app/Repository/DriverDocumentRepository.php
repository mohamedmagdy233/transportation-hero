<?php

namespace App\Repository;

use App\Models\DriverDocuments;
use Yajra\DataTables\DataTables;
use App\Interfaces\DriverDocumentInterface;
use App\Traits\FirebaseNotification;

class DriverDocumentRepository implements DriverDocumentInterface
{
    use FirebaseNotification;
    public function index($request)
    {
        if ($request->ajax()) {
            $driver_document = DriverDocuments::query()->latest()->get();
            return DataTables::of($driver_document)
                ->addColumn('action', function ($driver_document) {
                    return '
                            <button type="button" data-id="' . $driver_document->id . '" class="btn btn-pill btn-success editBtn"><i class="fa fa-eye"></i></button>
                       ';
                })
                ->editColumn('driver_id', function ($driver_document) {
                    return $driver_document->drivers->name ?? '--';
                })
                ->editColumn('status', function ($driver_document) {
                    if ($driver_document->status == 1)
                        return '<button class="btn btn-sm btn-success statusBtn" data-id="' . $driver_document->id . '">مقبول</button>';
                    else
                        return '<button class="btn btn-sm btn-danger statusBtn" data-id="' . $driver_document->id . '">غير مقبول</button>';
                })
                ->editColumn('agency_number', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->agency_number) . '">
                    ';
                })
                ->editColumn('bike_license', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->bike_license) . '">
                    ';
                })
                ->editColumn('id_card', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->id_card) . '">
                    ';
                })
                ->editColumn('house_card', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->id_card) . '">
                    ';
                })
                ->editColumn('bike_image', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->bike_image) . '">
                    ';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.driver_document.index');
        }
    }

    public function edit($driver_document)
    {
        return view('admin.driver_document.parts.edit', compact('driver_document'));
    }

    public function changeStatusDocument($request)
    {
        try {
            $driver_document = DriverDocuments::findOrFail($request->id);

            $driver_id = $driver_document->driver_id;

            $driver_document->status = ($driver_document->status == 1) ? 0 : 1;

            $driver_document->save();

            $notificationData = [
                'title' => 'تفعيل الحساب',
                'body' => ($driver_document->status == 1) ? 'تم تفعيل حسابك من قبل الادمن' : 'تم تعطيل حسابك من قبل الادمن',
            ];


            return $this->sendFirebaseNotification($notificationData, $driver_id, 'acceptDriver');

            return response()->json('200');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
