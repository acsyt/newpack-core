<?php

namespace Database\Seeders;

use App\Models\Central\User;
use App\Models\Shared\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('sync:permissions');

        $userCreate = User::updateOrCreate(
            ['email' => 'admin@acsyt.com'],
            [
                'uuid'           => Str::uuid(),
                'name'           => 'Admin',
                'last_name'      => 'Admin',
                'password'       => Hash::make('123456'),
                'language'       => 'en',
                'remember_token' => Str::random(10),
            ]);

        $role = Role::firstWhere('name', 'admin');
        if ($role) $userCreate->assignRole($role);

    }
}
