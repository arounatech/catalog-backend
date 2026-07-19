<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_public_service_request(): void
    {
        $service = Service::create([
            'title' => 'Public Request Test Service',
            'slug' => 'public-request-test-service-' . Str::uuid(),
            'description' => 'Test service description.',
            'price' => 100,
            'image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this->postJson('/api/public/service-requests', [
            'service_id' => $service->id,
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'phone' => '+123456789',
            'message' => 'I am interested in this service.',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('service_requests', [
            'user_id' => null,
            'service_id' => $service->id,
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'status' => 'new',
        ]);
    }

    public function test_guest_service_request_requires_valid_email(): void
    {
        $response = $this->postJson('/api/public/service-requests', [
            'name' => 'Guest User',
            'email' => 'not-valid-email',
            'phone' => '+123456789',
            'message' => 'This should fail because email is invalid.',
        ]);

        $response->assertUnprocessable();
    }
}