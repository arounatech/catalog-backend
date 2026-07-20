<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAccessSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_token_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/admin/dashboard');

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'Admin access required.',
            ]);
    }

    public function test_admin_token_cannot_access_user_routes(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/user/dashboard');

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'User access required.',
            ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_access_user_dashboard(): void
    {
        $response = $this->getJson('/api/user/dashboard');

        $response->assertUnauthorized();
    }
}
