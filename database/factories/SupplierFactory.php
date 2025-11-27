<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name'      => $this->faker->company(),
            'contact_name'      => $this->faker->name(),
            'email'             => $this->faker->unique()->companyEmail(),
            'phone'             => $this->faker->phoneNumber(),
            'phone_secondary'   => $this->faker->optional()->phoneNumber(),
            'street'            => $this->faker->streetName(),
            'exterior_number'   => $this->faker->buildingNumber(),
            'interior_number'   => $this->faker->optional()->secondaryAddress(),
            'address_reference' => $this->faker->optional()->sentence(),
            'rfc'               => strtoupper($this->faker->unique()->bothify('????######???')),
            'legal_name'        => $this->faker->company() . ' S.A. de C.V.',
            'tax_system'        => '601', // General de Ley Personas Morales
            'use_cfdi'          => 'G03', // Gastos en general
            'supplier_type'     => $this->faker->randomElement(['product', 'service', 'both']),
            'status'            => 'active',
            'notes'             => $this->faker->optional()->paragraph(),
        ];
    }
}
