<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('setting.view');

        $settings = Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->paginate(15);

        return SettingResource::collection($settings);
    }

    public function store(StoreSettingRequest $request): SettingResource
    {
        Gate::authorize('setting.create');

        $setting = Setting::create($request->validated());

        return new SettingResource($setting);
    }

    public function show(Setting $setting): SettingResource
    {
        Gate::authorize('setting.view');

        return new SettingResource($setting);
    }

    public function update(UpdateSettingRequest $request, Setting $setting): SettingResource
    {
        Gate::authorize('setting.update');

        $setting->update($request->validated());

        return new SettingResource($setting->fresh());
    }

    public function destroy(Setting $setting): JsonResponse
    {
        Gate::authorize('setting.delete');

        $setting->delete();

        return response()->json([
            'message' => 'Setting deleted successfully.',
        ]);
    }
}