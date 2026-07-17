<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceInquiryRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;

class ServiceRequestController extends Controller
{
    public function store(StoreServiceInquiryRequest $request): ServiceRequestResource
    {
        $data = $request->validated();

        $data['status'] = 'new';
        $data['user_id'] = $request->user()?->id;

        $serviceRequest = ServiceRequest::create($data);

        return new ServiceRequestResource(
            $serviceRequest->load('service')
        );
    }
}