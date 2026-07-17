<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+254711000000',
            'kra_pin' => null,
            'address' => 'Mombasa, Kenya',
        ];
    }
}
