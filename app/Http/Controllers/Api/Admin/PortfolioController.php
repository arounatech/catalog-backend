<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortfolioRequest;
use App\Http\Requests\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $portfolios = Portfolio::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return PortfolioResource::collection($portfolios);
    }

    public function store(StorePortfolioRequest $request): PortfolioResource
    {
        $data = $request->validated();

        $data['slug'] = $this->generateUniqueSlug($data['title']);

        $portfolio = Portfolio::create($data);

        return new PortfolioResource($portfolio);
    }

    public function show(Portfolio $portfolio): PortfolioResource
    {
        return new PortfolioResource($portfolio->load('projects'));
    }

    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio): PortfolioResource
    {
        $data = $request->validated();

        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $portfolio->id);
        }

        $portfolio->update($data);

        return new PortfolioResource($portfolio->fresh()->load('projects'));
    }

    public function destroy(Portfolio $portfolio): JsonResponse
    {
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
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}