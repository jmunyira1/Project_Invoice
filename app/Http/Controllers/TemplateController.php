<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Pdf\SampleDataFactory;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::all();
        $defaultId = $this->org()->default_template_id;

        return view('templates.index', compact('templates', 'defaultId'));
    }

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function setDefault(Template $template)
    {
        $this->org()->update(['default_template_id' => $template->id]);

        return back()->with('success', "\"{$template->name}\" is now your default template.");
    }

    /**
     * Stream a sample PDF for this template — no DB writes.
     */
    public function preview(Template $template)
    {
        $pdfClass = $template->getPdfClass();

        if (!class_exists($pdfClass)) {
            abort(404, "PDF class [{$pdfClass}] not found. Expected at app/Pdf/{$pdfClass}.php");
        }

        $sampleOrg = SampleDataFactory::organisation($this->org());
        $sampleDocument = SampleDataFactory::document($template);

        $pdf = new $pdfClass($sampleDocument, $sampleOrg);
        $output = $pdf->generate();

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview-' . $template->slug . '.pdf"',
        ]);
    }
}
