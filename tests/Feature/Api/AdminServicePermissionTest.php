<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminServicePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_service_view_permission_cannot_view_services(): void
    {
        $admin = Admin::create([
            'name' => 'Limited Admin',
            'email' => 'limited@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/services');

        $response->assertForbidden();
    }

    public function test_admin_with_service_view_permission_can_view_services(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Service Admin',
            'email' => 'serviceadmin@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'service.view',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('service.view');

        $service = Service::create([
            'title' => 'Admin Visible Service',
            'slug' => 'admin-visible-service-' . Str::uuid(),
            'description' => 'This service should be visible to permitted admins.',
            'price' => 100,
            'image' => null,
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/services');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $service->id,
                'title' => 'Admin Visible Service',
            ]);
    }

    public function test_admin_with_service_create_permission_can_create_service(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Creator Admin',
            'email' => 'creator@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'service.create',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('service.create');

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/services', [
                'title' => 'Created By Admin Test',
                'slug' => 'created-by-admin-test-' . Str::uuid(),
                'description' => 'Created during feature test.',
                'price' => 150,
                'status' => 'published',
                'sort_order' => 1,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('services', [
            'title' => 'Created By Admin Test',
            'status' => 'published',
        ]);
    }
}