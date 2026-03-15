<?php

namespace App\Pdf;

use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentLine;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\Template;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SampleDataFactory
{
    /**
     * Build a fake Organisation (not saved to DB).
     */
    public static function organisation(Organisation $real): Organisation
    {
        $sample = new Organisation();
        $sample->forceFill([
            'id'          => $real->id,
            'name'        => $real->name,
            'email'       => $real->email,
            'phone'       => $real->phone ?? '+254 700 000 000',
            'address'     => $real->address ?? "123 Business Park\nNairobi, Kenya",
            'currency'    => $real->currency,
            'logo_path'   => $real->logo_path,
        ]);

        return $sample;
    }

    /**
     * Build a fake Document with lines (not saved to DB).
     */
    public static function document(Template $template): Document
    {
        // Fake client
        $client = new Client();
        $client->forceFill([
            'name'    => 'Acme Corporation',
            'email'   => 'billing@acme.com',
            'phone'   => '+254 722 123 456',
            'address' => "456 Client Avenue\nMombasa, Kenya",
        ]);

        // Fake project
        $project = new Project();
        $project->forceFill([
            'id'    => 0,
            'title' => 'Sample Project',
        ]);
        // Attach client without DB
        $project->setRelation('client', $client);

        // Fake lines
        $lines = new Collection([
            self::makeLine('Brand Identity Design',   'Logo, colour palette and brand guidelines', 1,    45000),
            self::makeLine('Website Design',          'UI/UX design for 5 pages',                 1,    60000),
            self::makeLine('Website Development',     'Frontend and backend development',          1,    80000),
            self::makeLine('Content Writing',         'Copywriting for all pages',                 3,     8000),
            self::makeLine('Monthly Maintenance',     'Hosting and support retainer',              12,    5000),
        ]);

        // Fake document
        $document = new Document();
        $document->forceFill([
            'id'         => 0,
            'number'     => 'INV-' . now()->format('ym') . '-001',
            'type'       => 'invoice',
            'issue_date' => Carbon::today(),
            'due_date'   => Carbon::today()->addDays(30),
            'notes'      => 'Thank you for your business. Payment is due within 30 days.',
            'sent_at'    => null,
        ]);

        $document->setRelation('project', $project);
        $document->setRelation('template', $template);
        $document->setRelation('lines', $lines);
        $document->setRelation('payments', new Collection());

        return $document;
    }

    private static function makeLine(string $name, string $desc, float $qty, float $price): DocumentLine
    {
        $line = new DocumentLine();
        $line->forceFill([
            'name'        => $name,
            'description' => $desc,
            'quantity'    => $qty,
            'unit_price'  => $price,
            'total_price' => $qty * $price,
            'sort_order'  => 0,
        ]);
        return $line;
    }
}
