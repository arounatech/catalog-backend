<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PortfolioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $portfolios = Portfolio::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return PortfolioResource::collection($portfolios);
    }

    public function show(Portfolio $portfolio): PortfolioResource
    {
        abort_if($portfolio->status !== 'published', 404);

        return new PortfolioResource(
            $portfolio->load([
                'projects' => fn ($query) => $query
                    ->where('status', 'published')
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
        );
    }
}