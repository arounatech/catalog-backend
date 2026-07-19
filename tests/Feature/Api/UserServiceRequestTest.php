<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_service_request(): void
    {
        $user = User::factory()->create();

        $service = Service::create([
            'title' => 'Test Service',
            'slug' => 'test-service-' . Str::uuid(),
            'description' => 'Test service description.',
            'price' => 100,
            'image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/user/service-requests', [
                'service_id' => $service->id,
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'phone' => '+123456789',
                'message' => 'I am interested in this service.',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('service_requests', [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'status' => 'new',
        ]);
    }

    public function test_authenticated_user_can_view_only_own_service_requests(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        ServiceRequest::create([
            'user_id' => $user->id,
            'service_id' => null,
            'name' => 'Current User Request',
            'email' => 'current@example.com',
            'phone' => null,
            'message' => 'This belongs to the current user.',
            'status' => 'new',
            'admin_note' => null,
        ]);

        ServiceRequest::create([
            'user_id' => $otherUser->id,
            'service_id' => null,
            'name' => 'Other User Request',
            'email' => 'other@example.com',
            'phone' => null,
            'message' => 'This belongs to another user.',
            'status' => 'new',
            'admin_note' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/user/service-requests');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Current User Request',
                'email' => 'current@example.com',
            ])
            ->assertJsonMissing([
                'name' => 'Other User Request',
                'email' => 'other@example.com',
            ]);
    }

    public function test_guest_cannot_create_user_service_request(): void
    {
        $response = $this->postJson('/api/user/service-requests', [
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'message' => 'This should not be allowed on user route.',
        ]);

        $response->assertUnauthorized();
    }
}