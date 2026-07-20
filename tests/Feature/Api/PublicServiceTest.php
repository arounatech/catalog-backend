<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_services_endpoint_returns_published_services(): void
    {
        $service = Service::create([
            'title' => 'Published Test Service',
            'slug' => 'published-test-service-'.Str::uuid(),
            'description' => 'This service should be visible publicly.',
            'price' => 100,
            'image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/public/services');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $service->id,
                'title' => 'Published Test Service',
                'status' => 'published',
            ]);
    }

    public function test_public_service_show_returns_not_found_for_draft_service(): void
    {
        $service = Service::create([
            'title' => 'Draft Test Service',
            'slug' => 'draft-test-service-'.Str::uuid(),
            'description' => 'This service should not be visible publicly.',
            'price' => 100,
            'image' => null,
            'status' => 'draft',
            'sort_order' => 1,
        ]);

        $response = $this->getJson("/api/public/services/{$service->id}");

        $response->assertNotFound();
    }
}
