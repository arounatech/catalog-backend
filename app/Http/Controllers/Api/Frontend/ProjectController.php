<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->with('portfolio')
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    public function show(Project $project): ProjectResource
    {
        abort_if($project->status !== 'published', 404);

        return new ProjectResource(
            $project->load(['portfolio', 'images'])
        );
    }
}