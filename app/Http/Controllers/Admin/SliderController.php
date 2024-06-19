<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Interfaces\SliderInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SliderStoreRequest;
use App\Http\Requests\SliderUpdateRequest;

class SliderController extends Controller
{
    protected SliderInterface $sliderInterface;

    public function __construct(SliderInterface $sliderInterface)
    {
        $this->sliderInterface = $sliderInterface;
    }

    public function index(Request $request)
    {
        return $this->sliderInterface->index($request);
    }


    public function create()
    {
        return $this->sliderInterface->create();
    }

    public function store(SliderStoreRequest $request)
    {
        return $this->sliderInterface->store($request);
    }

    public function edit(Slider $slider)
    {
        return $this->sliderInterface->edit($slider);
    }

    public function update(SliderUpdateRequest $request, $id)
    {
        return $this->sliderInterface->update($request, $id);
    }

    public function changeStatusSlider(Request $request)
    {
        return $this->sliderInterface->changeStatusSlider($request);
    }

    public function delete(Request $request)
    {
        return $this->sliderInterface->delete($request);
    }
}
