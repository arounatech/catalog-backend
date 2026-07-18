<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectImageRequest;
use App\Http\Requests\UpdateProjectImageRequest;
use App\Http\Resources\ProjectImageResource;
use App\Models\ProjectImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ProjectImageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('project_image.view');

        $projectImages = ProjectImage::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        return ProjectImageResource::collection($projectImages);
    }

    public function store(StoreProjectImageRequest $request): ProjectImageResource
    {
        Gate::authorize('project_image.create');

        $projectImage = ProjectImage::create($request->validated());

        return new ProjectImageResource($projectImage);
    }

    public function show(ProjectImage $projectImage): ProjectImageResource
    {
        Gate::authorize('project_image.view');

        return new ProjectImageResource($projectImage);
    }

    public function update(UpdateProjectImageRequest $request, ProjectImage $projectImage): ProjectImageResource
    {
        Gate::authorize('project_image.update');

        $projectImage->update($request->validated());

        return new ProjectImageResource($projectImage->fresh());
    }

    public function destroy(ProjectImage $projectImage): JsonResponse
    {
        Gate::authorize('project_image.delete');

        $projectImage->delete();

        return response()->json([
            'message' => 'Project image deleted successfully.',
        ]);
    }
}