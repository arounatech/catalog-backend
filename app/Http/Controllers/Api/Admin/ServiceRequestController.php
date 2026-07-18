<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateServiceInquiryRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ServiceRequestController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('service_request.view');

        $serviceRequests = ServiceRequest::query()
            ->with(['service', 'user'])
            ->orderByDesc('id')
            ->paginate(15);

        return ServiceRequestResource::collection($serviceRequests);
    }

    public function show(ServiceRequest $serviceRequest): ServiceRequestResource
    {
        Gate::authorize('service_request.view');

        return new ServiceRequestResource(
            $serviceRequest->load(['service', 'user'])
        );
    }

    public function update(UpdateServiceInquiryRequest $request, ServiceRequest $serviceRequest): ServiceRequestResource
    {
        Gate::authorize('service_request.update');

        $serviceRequest->update($request->validated());

        return new ServiceRequestResource(
            $serviceRequest->fresh()->load(['service', 'user'])
        );
    }

    public function destroy(ServiceRequest $serviceRequest): JsonResponse
    {
        Gate::authorize('service_request.delete');

        $serviceRequest->delete();

        return response()->json([
            'message' => 'Service request deleted successfully.',
        ]);
    }
}