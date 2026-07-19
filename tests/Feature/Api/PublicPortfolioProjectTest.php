<?php

namespace Tests\Feature\Api;

use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicPortfolioProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_portfolios_endpoint_returns_published_portfolios(): void
    {
        $portfolio = Portfolio::create([
            'title' => 'Published Test Portfolio',
            'slug' => 'published-test-portfolio-' . Str::uuid(),
            'description' => 'This portfolio should be visible publicly.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/public/portfolios');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $portfolio->id,
                'title' => 'Published Test Portfolio',
                'status' => 'published',
            ]);
    }

    public function test_public_portfolio_show_returns_not_found_for_draft_portfolio(): void
    {
        $portfolio = Portfolio::create([
            'title' => 'Draft Test Portfolio',
            'slug' => 'draft-test-portfolio-' . Str::uuid(),
            'description' => 'This portfolio should not be visible publicly.',
            'status' => 'draft',
            'sort_order' => 1,
        ]);

        $response = $this->getJson("/api/public/portfolios/{$portfolio->id}");

        $response->assertNotFound();
    }

    public function test_public_projects_endpoint_returns_published_projects(): void
    {
        $project = Project::create([
            'portfolio_id' => null,
            'title' => 'Published Test Project',
            'slug' => 'published-test-project-' . Str::uuid(),
            'description' => 'This project should be visible publicly.',
            'body' => 'Project body content.',
            'project_date' => now()->toDateString(),
            'cover_image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/public/projects');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $project->id,
                'title' => 'Published Test Project',
                'status' => 'published',
            ]);
    }

    public function test_public_project_show_returns_not_found_for_draft_project(): void
    {
        $project = Project::create([
            'portfolio_id' => null,
            'title' => 'Draft Test Project',
            'slug' => 'draft-test-project-' . Str::uuid(),
            'description' => 'This project should not be visible publicly.',
            'body' => 'Project body content.',
            'project_date' => now()->toDateString(),
            'cover_image' => null,
            'status' => 'draft',
            'sort_order' => 1,
        ]);

        $response = $this->getJson("/api/public/projects/{$project->id}");

        $response->assertNotFound();
    }
}