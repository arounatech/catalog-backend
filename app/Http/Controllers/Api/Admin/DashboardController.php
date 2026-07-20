<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceRequestResource;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $latestServiceRequests = ServiceRequest::query()
            ->with(['service', 'user'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'services_count' => Service::query()->count(),
                'published_services_count' => Service::query()
                    ->where('status', 'published')
                    ->count(),
                'draft_services_count' => Service::query()
                    ->where('status', 'draft')
                    ->count(),

                'projects_count' => Project::query()->count(),
                'published_projects_count' => Project::query()
                    ->where('status', 'published')
                    ->count(),
                'draft_projects_count' => Project::query()
                    ->where('status', 'draft')
                    ->count(),

                'portfolios_count' => Portfolio::query()->count(),
                'published_portfolios_count' => Portfolio::query()
                    ->where('status', 'published')
                    ->count(),
                'draft_portfolios_count' => Portfolio::query()
                    ->where('status', 'draft')
                    ->count(),

                'service_requests_count' => ServiceRequest::query()->count(),
                'new_service_requests_count' => ServiceRequest::query()
                    ->where('status', 'new')
                    ->count(),

                'users_count' => User::query()->count(),

                'latest_service_requests' => ServiceRequestResource::collection($latestServiceRequests),
            ],
        ]);
    }
}
