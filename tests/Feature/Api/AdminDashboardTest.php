<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_summary(): void
    {
        $admin = Admin::create([
            'name' => 'Dashboard Admin',
            'email' => 'dashboard@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        User::factory()->create();

        Service::create([
            'title' => 'Published Dashboard Service',
            'slug' => 'published-dashboard-service-'.Str::uuid(),
            'description' => 'Published service.',
            'price' => 100,
            'image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'title' => 'Draft Dashboard Service',
            'slug' => 'draft-dashboard-service-'.Str::uuid(),
            'description' => 'Draft service.',
            'price' => 100,
            'image' => null,
            'status' => 'draft',
            'sort_order' => 2,
        ]);

        Portfolio::create([
            'title' => 'Dashboard Portfolio',
            'slug' => 'dashboard-portfolio-'.Str::uuid(),
            'description' => 'Dashboard portfolio.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Project::create([
            'portfolio_id' => null,
            'title' => 'Dashboard Project',
            'slug' => 'dashboard-project-'.Str::uuid(),
            'description' => 'Dashboard project.',
            'body' => 'Dashboard project body.',
            'project_date' => now()->toDateString(),
            'cover_image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        ServiceRequest::create([
            'user_id' => null,
            'service_id' => null,
            'name' => 'Dashboard Request',
            'email' => 'request@example.com',
            'phone' => '+123456789',
            'message' => 'Dashboard request message.',
            'status' => 'new',
            'admin_note' => null,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/dashboard');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'services_count',
                    'published_services_count',
                    'draft_services_count',
                    'projects_count',
                    'published_projects_count',
                    'draft_projects_count',
                    'portfolios_count',
                    'published_portfolios_count',
                    'draft_portfolios_count',
                    'service_requests_count',
                    'new_service_requests_count',
                    'users_count',
                    'latest_service_requests',
                ],
            ])
            ->assertJsonPath('data.services_count', 2)
            ->assertJsonPath('data.published_services_count', 1)
            ->assertJsonPath('data.draft_services_count', 1)
            ->assertJsonPath('data.projects_count', 1)
            ->assertJsonPath('data.portfolios_count', 1)
            ->assertJsonPath('data.service_requests_count', 1)
            ->assertJsonPath('data.users_count', 1);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertUnauthorized();
    }
}
