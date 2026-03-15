<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\DocumentLine;
use App\Models\Project;
use App\Models\Template;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = $this->org()
            ->documents()
            ->with(['project.client', 'template'])
            ->latest()
            ->paginate(25);

        return view('documents.index', compact('documents'));
    }

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function store(StoreDocumentRequest $request)
    {
        // Ensure project belongs to this org
        $project = $this->org()->projects()->findOrFail($request->project_id);

        $data = $request->validated();
        $data['organisation_id'] = $this->org()->id;
        $data['number'] = $this->org()->nextDocumentNumber($data['type']);

        $document = Document::create($data);

        // Snapshot deliverables as document lines
        $this->snapshotDeliverables($document, $project);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', "{$document->type_label} {$document->number} created.");
    }

    public function create()
    {
        $projects = $this->org()->projects()->with('client')->orderBy('title')->get();
        $templates = Template::all();
        $selectedProjectId = request('project_id');

        // Pre-select org default template
        $defaultTemplateId = $this->org()->default_template_id;

        return view('documents.create', compact(
            'projects', 'templates', 'selectedProjectId', 'defaultTemplateId'
        ));
    }

    /**
     * Copy deliverables from the project into frozen document lines.
     */
    private function snapshotDeliverables(Document $document, Project $project): void
    {
        $project->load('deliverables');

        foreach ($project->deliverables as $i => $d) {
            DocumentLine::create([
                'document_id' => $document->id,
                'name' => $d->name,
                'description' => $d->description,
                'quantity' => $d->quantity,
                'unit_price' => $d->unit_price,
                'total_price' => $d->quantity * $d->unit_price,
                'sort_order' => $i,
            ]);
        }
    }

    public function show(Document $document)
    {
        $this->authorise($document);

        $document->load(['lines', 'project.client', 'template', 'payments']);
        $org = $this->org();
        $currency = $org->currency;

        return view('documents.show', compact('document', 'org', 'currency'));
    }

    private function authorise(Document $document): void
    {
        if ($document->organisation_id !== $this->org()->id) {
            abort(403);
        }
    }

    public function destroy(Document $document)
    {
        $this->authorise($document);

        if ($document->file_path && file_exists(storage_path('app/public/' . $document->file_path))) {
            unlink(storage_path('app/public/' . $document->file_path));
        }

        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document deleted.');
    }

    // ── Private ────────────────────────────────────────────────────

    /**
     * Mark document as sent (sets sent_at timestamp).
     */
    public function markSent(Document $document)
    {
        $this->authorise($document);

        $document->update(['sent_at' => now()]);

        return back()->with('success', 'Document marked as sent.');
    }

    /**
     * Generate PDF using TCPDF and store it, then stream it.
     */
    public function pdf(Document $document)
    {
        $this->authorise($document);

        $document->load(['lines', 'project.client', 'template', 'payments']);
        $org = $this->org();

        $pdfClass = $document->template->getPdfClass();

        if (!class_exists($pdfClass)) {
            return back()->with('error', "PDF template class [{$pdfClass}] not found.");
        }

        /** @var \App\Pdf\BasePdfTemplate $pdf */
        $pdf = new $pdfClass($document, $org);
        $output = $pdf->generate();

        // Store file
        $filename = $document->number . '.pdf';
        $path = 'documents/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $output);
        $document->update(['file_path' => $path]);

        // ?download=1 forces download, otherwise inline for iframe
        $disposition = request()->query('download') ? 'attachment' : 'inline';

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        ]);
    }
}
