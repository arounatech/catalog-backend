<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\UserResource;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $latestServiceRequests = ServiceRequest::query()
            ->where('user_id', $request->user()->id)
            ->with(['service'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'user' => new UserResource($request->user()),

                'service_requests_count' => ServiceRequest::query()
                    ->where('user_id', $request->user()->id)
                    ->count(),

                'new_service_requests_count' => ServiceRequest::query()
                    ->where('user_id', $request->user()->id)
                    ->where('status', 'new')
                    ->count(),

                'latest_service_requests' => ServiceRequestResource::collection($latestServiceRequests),
            ],
        ]);
    }
}
