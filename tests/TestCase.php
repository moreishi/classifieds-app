<?php

namespace Tests;


use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        return $user->assignRole('admin');
    }

    protected function createSeller(array $overrides = []): User
    {
        // Ensure roles exist (RefreshDatabase doesn't run seeders)
        if (!\Spatie\Permission\Models\Role::whereName('seller')->exists()) {
            \Spatie\Permission\Models\Role::create(['name' => 'seller']);
            \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        }

        /** @var User $user */
        $user = User::factory()->create($overrides);

        return $user->assignRole('seller');
    }
}
