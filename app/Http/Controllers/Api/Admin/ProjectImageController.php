<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectImageRequest;
use App\Http\Requests\UpdateProjectImageRequest;
use App\Http\Resources\ProjectImageResource;
use App\Models\ProjectImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectImageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $projectImages = ProjectImage::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        return ProjectImageResource::collection($projectImages);
    }

    public function store(StoreProjectImageRequest $request): ProjectImageResource
    {
        $projectImage = ProjectImage::create($request->validated());

        return new ProjectImageResource($projectImage);
    }

    public function show(ProjectImage $projectImage): ProjectImageResource
    {
        return new ProjectImageResource($projectImage);
    }

    public function update(UpdateProjectImageRequest $request, ProjectImage $projectImage): ProjectImageResource
    {
        $projectImage->update($request->validated());

        return new ProjectImageResource($projectImage->fresh());
    }

    public function destroy(ProjectImage $projectImage): JsonResponse
    {
        $projectImage->delete();

        return response()->json([
            'message' => 'Project image deleted successfully.',
        ]);
    }
}