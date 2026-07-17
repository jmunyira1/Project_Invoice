<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Organisation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'client_id' => Client::factory(),
            'title' => fake()->catchPhrase(),
            'description' => null,
            'value' => null,
            'status' => 'active',
            'due_date' => now()->addDays(30),
        ];
    }
}
