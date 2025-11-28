<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('Synchronizing permissions and roles...');

        $exitCode = Artisan::call('sync:permissions');
        $output = Artisan::output();

        $this->command->line($output);

        if ($exitCode !== 0) {
            $this->command->error('Failed to sync permissions. Exit code: ' . $exitCode);
            return;
        }

        $this->createAdminUser();

        $this->call([
            ProductTypeSeeder::class,
            ProductClassificationSeeder::class,
            RawMaterialSeeder::class,
            MachineSeeder::class,
            CustomerSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->createDummyUsers();
            $this->call([
                CustomerSeeder::class,
                SupplierSeeder::class,
                WarehouseSeeder::class,
            ]);}
    }

    private function createAdminUser(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@acsyt.com']);

        if (!$admin->exists) {
            $admin->password = Hash::make(env('ADMIN_PASSWORD', '123456'));
            $admin->remember_token = Str::random(10);
            $admin->email_verified_at = now();
        }

        $admin->name = 'Admin';
        $admin->last_name = 'System';
        $admin->language = 'en';
        $admin->active = true;

        $admin->save();

        $roleAdmin = Role::where('name', 'admin')->first();

        if ($roleAdmin) {
            $admin->syncRoles($roleAdmin);
        }
    }

    private function createDummyUsers(): void
    {
        $roleAdmin = Role::where('name', 'admin')->first();

        User::factory()
            ->count(50)
            ->create()
            ->each(function (User $user) use( $roleAdmin ) {
                $user->assignRole($roleAdmin);
            });
    }
}
