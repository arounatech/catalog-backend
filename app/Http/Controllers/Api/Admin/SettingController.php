<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SettingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $settings = Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->paginate(15);

        return SettingResource::collection($settings);
    }

    public function store(StoreSettingRequest $request): SettingResource
    {
        $setting = Setting::create($request->validated());

        return new SettingResource($setting);
    }

    public function show(Setting $setting): SettingResource
    {
        return new SettingResource($setting);
    }

    public function update(UpdateSettingRequest $request, Setting $setting): SettingResource
    {
        $setting->update($request->validated());

        return new SettingResource($setting->fresh());
    }

    public function destroy(Setting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json([
            'message' => 'Setting deleted successfully.',
        ]);
    }
}