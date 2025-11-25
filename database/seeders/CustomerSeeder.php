<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Suburb;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $suburbsExist = Suburb::exists();

        if (!$suburbsExist) {
            $this->command->warn('⚠️  No suburbs found. Please run location seeders first.');
            $this->command->warn('   Run: php artisan db:seed --class=StateSeeder');
            return;
        }

        Customer::factory()->count(10)->active()->individual()->verified()->create();

        Customer::factory()->count(5)->active()->company()->verified()->create();

        Customer::factory()->count(2)->inactive()->individual()->create();

        Customer::factory()->count(2)->suspended()->individual()->create();

        Customer::factory()->count(1)->active()->company()->create();

        $total = Customer::count();
        $this->command->info("✅ Created {$total} customers successfully!");
        $this->command->info("   - Active individuals: 10");
        $this->command->info("   - Active companies: 6");
        $this->command->info("   - Inactive: 2");
        $this->command->info("   - Suspended: 2");
    }
}
