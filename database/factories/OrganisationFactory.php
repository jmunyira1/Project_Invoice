<?php

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organisation>
 */
class OrganisationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(2),
            'email' => fake()->unique()->companyEmail(),
            'phone' => '+254700000000',
            'address' => 'Nairobi, Kenya',
            'currency' => 'KES',
            'kra_pin' => 'P05' . fake()->numberBetween(1000000, 9999999) . 'X',
            'vat_registered' => false,
            'default_tax_rate' => 16,
        ];
    }

    /**
     * A VAT-registered organisation (charges 16% VAT).
     */
    public function vatRegistered(): static
    {
        return $this->state(fn () => ['vat_registered' => true]);
    }
}
