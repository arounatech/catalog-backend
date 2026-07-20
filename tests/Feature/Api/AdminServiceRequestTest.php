<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_service_request_view_permission_can_view_service_requests(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Request Viewer Admin',
            'email' => 'requestviewer@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'service_request.view',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('service_request.view');

        $serviceRequest = ServiceRequest::create([
            'user_id' => null,
            'service_id' => null,
            'name' => 'Guest Request',
            'email' => 'guest@example.com',
            'phone' => '+123456789',
            'message' => 'I need more information.',
            'status' => 'new',
            'admin_note' => null,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/service-requests');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $serviceRequest->id,
                'name' => 'Guest Request',
                'email' => 'guest@example.com',
                'status' => 'new',
            ]);
    }

    public function test_admin_with_service_request_update_permission_can_update_service_request(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Request Manager Admin',
            'email' => 'requestmanager@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'service_request.update',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('service_request.update');

        $serviceRequest = ServiceRequest::create([
            'user_id' => null,
            'service_id' => null,
            'name' => 'Guest Request',
            'email' => 'guest@example.com',
            'phone' => '+123456789',
            'message' => 'I need more information.',
            'status' => 'new',
            'admin_note' => null,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/service-requests/{$serviceRequest->id}", [
                'status' => 'in_review',
                'admin_note' => 'Admin is reviewing this request.',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => 'in_review',
            'admin_note' => 'Admin is reviewing this request.',
        ]);
    }

    public function test_admin_without_service_request_view_permission_cannot_view_service_requests(): void
    {
        $admin = Admin::create([
            'name' => 'Limited Admin',
            'email' => 'limitedrequest@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/service-requests');

        $response->assertForbidden();
    }
}
