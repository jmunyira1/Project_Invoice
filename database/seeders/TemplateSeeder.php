<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Template 001',
                'slug' => 'template-001',
                'description' => 'Classic dark header bar with accent underline. Clean and professional.',
            ],
            [
                'name' => 'Template 002',
                'slug' => 'template-002',
                'description' => 'Modern design with bold left accent strip and purple branding.',
            ],
            [
                'name' => 'Template 003',
                'slug' => 'template-003',
                'description' => 'Minimal whitespace layout. Clean typography, no colour blocks.',
            ],
            [
                'name' => 'Template 004',
                'slug' => 'template-004',
                'description' => 'Bold two-tone split header. Blue and amber contrast.',
            ],
        ];

        foreach ($templates as $data) {
            Template::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
