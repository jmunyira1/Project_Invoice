<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Classic',
            'slug' => 'template-001', // backed by App\Pdf\Template001
            'description' => 'Left-aligned masthead. Clean and monochrome.',
        ];
    }
}
