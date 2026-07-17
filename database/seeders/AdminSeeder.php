<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('INITIAL_ADMIN_NAME');
        $email = env('INITIAL_ADMIN_EMAIL');
        $password = env('INITIAL_ADMIN_PASSWORD');

        if (! $name || ! $email || ! $password) {
            throw new RuntimeException('Initial admin environment variables are missing.');
        }

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'status' => 'active',
            ]
        );
    }
}