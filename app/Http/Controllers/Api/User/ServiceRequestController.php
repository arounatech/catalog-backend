<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceInquiryRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceRequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $serviceRequests = ServiceRequest::query()
            ->where('user_id', $request->user()->id)
            ->with(['service'])
            ->orderByDesc('id')
            ->paginate(15);

        return ServiceRequestResource::collection($serviceRequests);
    }

    public function store(StoreServiceInquiryRequest $request): ServiceRequestResource
    {
        $data = $request->validated();

        $data['user_id'] = $request->user()->id;

        $serviceRequest = ServiceRequest::create($data);

        return new ServiceRequestResource(
            $serviceRequest->load(['service', 'user'])
        );
    }
}