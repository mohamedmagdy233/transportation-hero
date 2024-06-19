<?php

namespace App\Repository;

use App\Interfaces\SettingInterface;
use App\Models\Setting;
use App\Traits\PhotoTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\DataTables;

class SettingRepository implements SettingInterface
{
    use PhotoTrait;

    public function index()
    {
        $settings = Setting::query()->select('id', 'logo', 'trip_insurance', 'rewards', 'about', 'phone', 'support', 'safety_roles', 'polices', 'km', 'vat')->first();
        return view('admin.setting.index', compact('settings'));
    }

    public function update($request, $id): JsonResponse
    {
        $inputs = $request->except('id');

        $setting = Setting::query()->findOrFail($id);

        if ($request->has('logo')) {
            $inputs['logo'] = $this->saveImage($request->logo, 'uploads/settings', 'photo');
        } else {
            unset($request->logo);
        }

        if ($setting->update($inputs))
            return response()->json(['status' => 200]);
        else
            return response()->json(['status' => 405]);
    }
}
