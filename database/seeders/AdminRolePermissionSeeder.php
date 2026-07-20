<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'service.view',
            'service.create',
            'service.update',
            'service.delete',

            'setting.view',
            'setting.create',
            'setting.update',
            'setting.delete',

            'portfolio.view',
            'portfolio.create',
            'portfolio.update',
            'portfolio.delete',

            'project.view',
            'project.create',
            'project.update',
            'project.delete',

            'project_image.view',
            'project_image.create',
            'project_image.update',
            'project_image.delete',

            'service_request.view',
            'service_request.update',
            'service_request.delete',

            'admin.view',
            'admin.create',
            'admin.update',
            'admin.delete',

            'role.view',
            'role.create',
            'role.update',
            'role.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'admin');
        }

        $superAdmin = Role::findOrCreate('super_admin', 'admin');
        $admin = Role::findOrCreate('admin', 'admin');
        $editor = Role::findOrCreate('editor', 'admin');
        $viewer = Role::findOrCreate('viewer', 'admin');

        $superAdmin->syncPermissions($permissions);

        $admin->syncPermissions([
            'service.view',
            'service.create',
            'service.update',
            'service.delete',

            'setting.view',
            'setting.update',

            'portfolio.view',
            'portfolio.create',
            'portfolio.update',
            'portfolio.delete',

            'project.view',
            'project.create',
            'project.update',
            'project.delete',

            'project_image.view',
            'project_image.create',
            'project_image.update',
            'project_image.delete',

            'service_request.view',
            'service_request.update',
            'service_request.delete',
        ]);

        $editor->syncPermissions([
            'service.view',
            'service.create',
            'service.update',

            'portfolio.view',
            'portfolio.create',
            'portfolio.update',

            'project.view',
            'project.create',
            'project.update',

            'project_image.view',
            'project_image.create',
            'project_image.update',

            'service_request.view',
        ]);

        $viewer->syncPermissions([
            'service.view',
            'setting.view',
            'portfolio.view',
            'project.view',
            'project_image.view',
            'service_request.view',
        ]);

        $initialAdminEmail = env('INITIAL_ADMIN_EMAIL');

        if ($initialAdminEmail) {
            $initialAdmin = Admin::query()
                ->where('email', $initialAdminEmail)
                ->first();

            $initialAdmin?->assignRole($superAdmin);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
