<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('service.view');

        $services = Service::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return ServiceResource::collection($services);
    }

    public function store(StoreServiceRequest $request): ServiceResource
    {
        Gate::authorize('service.create');

        $data = $request->validated();

        $data['slug'] = $this->generateUniqueSlug($data['title']);

        $service = Service::create($data);

        return new ServiceResource($service);
    }

    public function show(Service $service): ServiceResource
    {
        Gate::authorize('service.view');

        return new ServiceResource($service);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        Gate::authorize('service.update');

        $data = $request->validated();

        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $service->id);
        }

        $service->update($data);

        return new ServiceResource($service->fresh());
    }

    public function destroy(Service $service): JsonResponse
    {
        Gate::authorize('service.delete');

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully.',
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Service::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}