<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('project.view');

        $projects = Project::query()
            ->with('portfolio')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        Gate::authorize('project.create');

        $data = $request->validated();

        $data['slug'] = $this->generateUniqueSlug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        }

        $project = Project::create($data);

        return new ProjectResource($project->load(['portfolio', 'images']));
    }

    public function show(Project $project): ProjectResource
    {
        Gate::authorize('project.view');

        return new ProjectResource($project->load(['portfolio', 'images']));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        Gate::authorize('project.update');

        $data = $request->validated();

        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $project->id);
        }

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        }

        $project->update($data);

        return new ProjectResource($project->fresh()->load(['portfolio', 'images']));
    }

    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('project.delete');

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Project::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
