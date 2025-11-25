<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Suburb;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $clientType = fake()->randomElement(['individual', 'company']);
        $isCompany = $clientType === 'company';

        $regimenesFiscales = ['601', '612', '626', '603', '605', '606', '607', '608'];
        $usosCfdi = ['G01', 'G02', 'G03', 'I01', 'I02', 'I03', 'I04', 'I05', 'I06', 'I07', 'I08'];

        return [
            'name' => $isCompany ? fake()->company() : fake()->firstName(),
            'last_name' => $isCompany ? fake()->randomElement(['SA de CV', 'SC', 'SPR de RL']) : fake()->lastName() . ' ' . fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '55' . fake()->numerify('########'),
            'phone_secondary' => fake()->boolean(30) ? '55' . fake()->numerify('########') : null,
            'mobile' => fake()->boolean(70) ? '55' . fake()->numerify('########') : null,
            'whatsapp' => fake()->boolean(60) ? '55' . fake()->numerify('########') : null,
            'suburb_id' => Suburb::inRandomOrder()->first()?->id,
            'street' => fake()->streetName(),
            'exterior_number' => fake()->buildingNumber(),
            'interior_number' => fake()->boolean(40) ? fake()->randomElement(['A', 'B', 'C', '1', '2', '3', '101', '202']) : null,
            'address_reference' => fake()->boolean(50) ? fake()->sentence(6) : null,
            'rfc' => $this->generateRFC($isCompany),
            'razon_social' => $isCompany ? fake()->company() . ' ' . fake()->randomElement(['SA de CV', 'SC', 'SPR de RL']) : null,
            'regimen_fiscal' => fake()->randomElement($regimenesFiscales),
            'uso_cfdi' => fake()->randomElement($usosCfdi),
            'status' => fake()->randomElement(['active', 'active', 'active', 'active', 'inactive', 'suspended']),
            'client_type' => $clientType,
            'notes' => fake()->boolean(40) ? fake()->sentence(10) : null,
            'email_verified_at' => fake()->boolean(80) ? now() : null,
            'email_verification_token' => null,
            'created_by' => User::first()?->id,
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }

    private function generateRFC(bool $isCompany): string
    {
        if ($isCompany) {
            return strtoupper(fake()->lexify('???')) . fake()->numerify('######') . strtoupper(fake()->lexify('???'));
        }

        return strtoupper(fake()->lexify('????')) . fake()->numerify('######') . strtoupper(fake()->lexify('???'));
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_type' => 'individual',
            'razon_social' => null,
        ]);
    }

    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_type' => 'company',
            'name' => fake()->company(),
            'last_name' => fake()->randomElement(['SA de CV', 'SC', 'SPR de RL']),
            'razon_social' => fake()->company() . ' ' . fake()->randomElement(['SA de CV', 'SC', 'SPR de RL']),
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }
}
