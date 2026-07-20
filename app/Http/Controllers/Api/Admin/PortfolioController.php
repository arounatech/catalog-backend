<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortfolioRequest;
use App\Http\Requests\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('portfolio.view');

        $portfolios = Portfolio::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return PortfolioResource::collection($portfolios);
    }

    public function store(StorePortfolioRequest $request): PortfolioResource
    {
        Gate::authorize('portfolio.create');

        $data = $request->validated();

        $data['slug'] = $this->generateUniqueSlug($data['title']);

        $portfolio = Portfolio::create($data);

        return new PortfolioResource($portfolio);
    }

    public function show(Portfolio $portfolio): PortfolioResource
    {
        Gate::authorize('portfolio.view');

        return new PortfolioResource($portfolio->load('projects'));
    }

    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio): PortfolioResource
    {
        Gate::authorize('portfolio.update');

        $data = $request->validated();

        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $portfolio->id);
        }

        $portfolio->update($data);

        return new PortfolioResource($portfolio->fresh()->load('projects'));
    }

    public function destroy(Portfolio $portfolio): JsonResponse
    {
        Gate::authorize('portfolio.delete');

        $portfolio->delete();

        return response()->json([
            'message' => 'Portfolio deleted successfully.',
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Portfolio::query()
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
