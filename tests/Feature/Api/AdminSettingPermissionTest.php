<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminSettingPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_setting_view_permission_cannot_view_settings(): void
    {
        $admin = Admin::create([
            'name' => 'Limited Admin',
            'email' => 'limitedsettings@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/settings');

        $response->assertForbidden();
    }

    public function test_admin_with_setting_view_permission_can_view_settings(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Settings Viewer',
            'email' => 'settingsviewer@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'setting.view',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('setting.view');

        $setting = Setting::create([
            'key' => 'site_title_' . Str::random(8),
            'value' => 'Catalog Website',
            'type' => 'string',
            'group' => 'general',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/settings');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $setting->id,
                'value' => 'Catalog Website',
                'type' => 'string',
                'group' => 'general',
            ]);
    }

    public function test_admin_with_setting_create_permission_can_create_setting(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name' => 'Settings Creator',
            'email' => 'settingscreator@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Permission::create([
            'name' => 'setting.create',
            'guard_name' => 'admin',
        ]);

        $admin->givePermissionTo('setting.create');

        $settingKey = 'homepage_title_' . Str::random(8);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/settings', [
                'key' => $settingKey,
                'value' => 'Welcome to the Catalog Website',
                'type' => 'string',
                'group' => 'homepage',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('settings', [
            'key' => $settingKey,
            'value' => 'Welcome to the Catalog Website',
            'type' => 'string',
            'group' => 'homepage',
        ]);
    }
}