<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds. There are exactly three document templates,
     * each backed by a PDF class in app/Pdf.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Classic',
                'slug' => 'template-001',
                'description' => 'Left-aligned masthead — brand on the left, title on the right, with a Bill-To column beside the invoice details. Clean and monochrome.',
            ],
            [
                'name' => 'Centered',
                'slug' => 'template-002',
                'description' => 'Centered title with side-by-side From / Bill-To blocks. Minimal, understated and monochrome.',
            ],
            [
                'name' => 'Accent',
                'slug' => 'template-003',
                'description' => 'A coloured header band with a reversed white title and an accent-coloured total. A bolder, more branded look.',
            ],
        ];

        foreach ($templates as $data) {
            Template::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // Retire the legacy fourth template (never had a PDF class).
        $legacy = Template::where('slug', 'template-004')->first();
        if ($legacy) {
            $fallback = Template::where('slug', 'template-001')->value('id');
            Organisation::where('default_template_id', $legacy->id)
                ->update(['default_template_id' => $fallback]);

            if (!$legacy->documents()->exists()) {
                $legacy->delete();
            }
        }
    }
}
