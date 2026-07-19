<?php

namespace Tests\Feature\Api;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_dashboard_summary(): void
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
            ->getJson('/api/user/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('data.service_requests_count', 1)
            ->assertJsonPath('data.new_service_requests_count', 1)
            ->assertJsonFragment([
                'name' => 'Current User Request',
                'email' => 'current@example.com',
            ])
            ->assertJsonMissing([
                'name' => 'Other User Request',
                'email' => 'other@example.com',
            ]);
    }

    public function test_guest_cannot_access_user_dashboard(): void
    {
        $response = $this->getJson('/api/user/dashboard');

        $response->assertUnauthorized();
    }
}