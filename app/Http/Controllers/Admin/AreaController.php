<?php

namespace App\Http\Controllers\Admin;

use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use App\Interfaces\AreaInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\AreaStoreRequest;
use App\Http\Requests\AreaUpdateRequest;

class AreaController extends Controller
{
    protected AreaInterface $areaInterface;

    public function __construct(AreaInterface $areaInterface)
    {
        $this->areaInterface = $areaInterface;
    }

    public function index(Request $request)
    {
        return $this->areaInterface->index($request);
    }

    public function create()
    {
        return $this->areaInterface->create();
    }

    public function store(AreaStoreRequest $request)
    {
        return $this->areaInterface->store($request);
    }

    public function edit(Area $area)
    {
        return $this->areaInterface->edit($area);
    }

    public function update(AreaUpdateRequest $request, $id)
    {
        return $this->areaInterface->update($request, $id);
    }

    public function delete(Request $request)
    {
        return $this->areaInterface->delete($request);
    }
}
