<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminPortfolioProjectPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_portfolio_view_permission_can_view_portfolios(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Portfolio Viewer',
            'email' => 'portfolioviewer@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'portfolio.view',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('portfolio.view');

        $portfolio = Portfolio::create([
            'title' => 'Admin Visible Portfolio',
            'slug' => 'admin-visible-portfolio-'.Str::uuid(),
            'description' => 'Visible to permitted admin.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/portfolios');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $portfolio->id,
                'title' => 'Admin Visible Portfolio',
            ]);
    }

    public function test_admin_with_portfolio_create_permission_can_create_portfolio(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Portfolio Creator',
            'email' => 'portfoliocreator@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'portfolio.create',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('portfolio.create');

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/portfolios', [
                'title' => 'Created Portfolio Test',
                'slug' => 'created-portfolio-test-'.Str::uuid(),
                'description' => 'Created during feature test.',
                'status' => 'published',
                'sort_order' => 1,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('portfolios', [
            'title' => 'Created Portfolio Test',
            'status' => 'published',
        ]);
    }

    public function test_admin_with_project_view_permission_can_view_projects(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Project Viewer',
            'email' => 'projectviewer@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'project.view',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('project.view');

        $project = Project::create([
            'portfolio_id' => null,
            'title' => 'Admin Visible Project',
            'slug' => 'admin-visible-project-'.Str::uuid(),
            'description' => 'Visible to permitted admin.',
            'body' => 'Project body.',
            'project_date' => now()->toDateString(),
            'cover_image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/projects');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $project->id,
                'title' => 'Admin Visible Project',
            ]);
    }

    public function test_admin_with_project_create_permission_can_create_project(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Project Creator',
            'email' => 'projectcreator@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'project.create',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('project.create');

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/projects', [
                'portfolio_id' => null,
                'title' => 'Created Project Test',
                'slug' => 'created-project-test-'.Str::uuid(),
                'description' => 'Created during feature test.',
                'body' => 'Project body content.',
                'project_date' => now()->toDateString(),
                'status' => 'published',
                'sort_order' => 1,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('projects', [
            'title' => 'Created Project Test',
            'status' => 'published',
        ]);
    }
}
