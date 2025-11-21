<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
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

        $admin = User::firstOrNew(['email' => 'admin@acsyt.com']);

        if (!$admin->exists) {
            $admin->password = Hash::make('123456');
            $admin->remember_token = Str::random(10);
            $admin->email_verified_at = now();
        }

        $admin->name = 'Admin';
        $admin->last_name = 'System';
        $admin->language = 'en';

        $admin->save();

        $admin->syncRoles(['admin']);

        if (app()->environment('local')) {
            User::factory()
                ->count(50)
                ->create()
                ->each(function ($user) {
                    $user->assignRole('admin');
                });
        }

    }
}
