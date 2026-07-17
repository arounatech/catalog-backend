<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateServiceInquiryRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceRequestController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $serviceRequests = ServiceRequest::query()
            ->with(['service', 'user'])
            ->orderByDesc('id')
            ->paginate(15);

        return ServiceRequestResource::collection($serviceRequests);
    }

    public function show(ServiceRequest $serviceRequest): ServiceRequestResource
    {
        return new ServiceRequestResource(
            $serviceRequest->load(['service', 'user'])
        );
    }

    public function update(UpdateServiceInquiryRequest $request, ServiceRequest $serviceRequest): ServiceRequestResource
    {
        $serviceRequest->update($request->validated());

        return new ServiceRequestResource(
            $serviceRequest->fresh()->load(['service', 'user'])
        );
    }

    public function destroy(ServiceRequest $serviceRequest): JsonResponse
    {
        $serviceRequest->delete();

        return response()->json([
            'message' => 'Service request deleted successfully.',
        ]);
    }
}