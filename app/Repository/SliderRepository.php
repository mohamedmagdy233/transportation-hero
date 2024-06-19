<?php

namespace App\Repository;

use App\Models\Slider;
use App\Traits\PhotoTrait;
use Yajra\DataTables\DataTables;
use App\Interfaces\SliderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SliderRepository implements SliderInterface
{
    use PhotoTrait;
    public function index($request)
    {
        if ($request->ajax()) {
            $slider = Slider::query()->latest()->get();
            return DataTables::of($slider)
                ->addColumn('action', function ($slider) {
                    return '
                            <button type="button" data-id="' . $slider->id . '" class="btn btn-pill btn-info-light editBtn"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $slider->id . '" data-title="' . $slider->link . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->editColumn('status', function ($slider) {
                    if ($slider->status == 1)
                        return '<button class="btn btn-sm btn-success statusBtn" data-id="' . $slider->id . '">مفعل</button>';
                    else
                        return '<button class="btn btn-sm btn-danger statusBtn" data-id="' . $slider->id . '">غير مفعل</button>';
                })
                ->editColumn('link', function ($slider) {
                    return '<a href="' . $slider->link . '" target="_blank" style="background-color: #007bff; color: #fff; padding: 5px; cursor: pointer; text-decoration: none; border: 1px solid #007bff; border-radius: 5px;">' . $slider->link . '</a>';
                })
                ->editColumn('image', function ($slider) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($slider->image) . '">
                    ';
                })
                ->editColumn('type', function ($slider) {
                    if ($slider->type == 'user')
                        return 'مستخدم';
                    else
                        return 'سائق';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.slider.index');
        }
    }

    public function create()
    {
        return view('admin.slider.parts.create');
    }

    public function store($request): JsonResponse
    {
        $inputs = $request->all();

        if ($request->has('image')) {
            $inputs['image'] = $this->saveImage($request->image, 'uploads/sliders', 'photo');
        }
        if (Slider::query()->create($inputs))
            return response()->json(['status' => 200]);
        else
            return response()->json(['status' => 405]);
    }

    public function edit($slider)
    {
        return view('admin.slider.parts.edit', compact('slider'));
    }

    public function update($request, $id): JsonResponse
    {
        $inputs = $request->except('id');

        $slider = Slider::query()->findOrFail($id);

        if ($request->has('image')) {
            if (file_exists($slider->image)) {
                unlink($slider->image);
            }
            $inputs['image'] = $this->saveImage($request->image, 'uploads/sliders', 'photo');
        }

        if ($slider->update($inputs))
            return response()->json(['status' => 200]);
        else
            return response()->json(['status' => 405]);
    }

    public function changeStatusSlider($request)
    {
        $slider = Slider::findOrFail($request->id);

        ($slider->status == 1) ? $slider->status = 0 : $slider->status = 1;

        $slider->save();

        if ($slider->status == 1) {
            return response()->json('200');
        } else {
            return response()->json('201');
        }
    }

    public function delete($request)
    {
        $slider = Slider::query()->where('id', $request->id)->first();

        $slider->delete();
        return response(['message' => 'تم الحذف بنجاح', 'status' => 200], 200);
    }
}
