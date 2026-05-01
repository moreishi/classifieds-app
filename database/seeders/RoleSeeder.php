<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin role if it doesn't exist
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create admin user if none exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@iskina.ph'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'reputation_tier' => 'pro',
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info('✓ Admin role and user created.');
        $this->command->info('  Email: admin@iskina.ph / Password: password');
    }
}
