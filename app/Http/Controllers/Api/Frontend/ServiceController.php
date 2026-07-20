<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $services = Service::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return ServiceResource::collection($services);
    }

    public function show(Service $service): ServiceResource
    {
        abort_if($service->status !== 'published', 404);

        return new ServiceResource($service);
    }
}
