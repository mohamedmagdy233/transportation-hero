<?php

namespace App\Repository;

use App\Models\Area;
use Yajra\DataTables\DataTables;
use App\Interfaces\AreaInterface;
use App\Models\City;
use Symfony\Component\HttpFoundation\JsonResponse;

class AreaRepository implements AreaInterface
{
    public function index($request)
    {
        if ($request->ajax()) {
            $area = Area::query()->latest()->get();
            return DataTables::of($area)
                ->addColumn('action', function ($area) {
                    return '
                            <button type="button" data-id="' . $area->id . '" class="btn btn-pill btn-info-light editBtn"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $area->id . '" data-title="' . $area->name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->editColumn('city_id', function ($area) {
                    return $area->city->name;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.area.index');
        }
    }

    public function create()
    {
        $cities = City::query()->select('id', 'name')->get();
        return view('admin.area.parts.create', compact('cities'));
    }

    public function store($request): JsonResponse
    {
        $inputs = $request->all();
        if (Area::query()->create($inputs))
            return response()->json(['status' => 200]);
        else
            return response()->json(['status' => 405]);
    }

    public function edit($area)
    {
        $cities = City::query()->select('id', 'name')->get();
        return view('admin.area.parts.edit', compact('area', 'cities'));
    }

    public function update($request, $id): JsonResponse
    {
        $inputs = $request->except('id');

        $area = Area::query()->findOrFail($id);

        if ($area->update($inputs))
            return response()->json(['status' => 200]);
        else
            return response()->json(['status' => 405]);
    }

    public function delete($request)
    {
        $area = Area::query()->where('id', $request->id)->first();
        $area->delete();
        return response(['message' => 'تم الحذف بنجاح', 'status' => 200], 200);
    }
}