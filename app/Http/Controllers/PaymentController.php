<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectCards;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Template;

class PaymentController extends Controller
{
    use HandlesProjectCards;

    public function index()
    {
        $payments = $this->org()
            ->payments()
            ->with(['project.client', 'document'])
            ->latest('paid_on')
            ->paginate(25);

        $totalThisMonth = $this->org()
            ->payments()
            ->whereMonth('paid_on', now()->month)
            ->whereYear('paid_on', now()->year)
            ->sum('amount');

        $currency = $this->org()->currency;

        return view('payments.index', compact('payments', 'totalThisMonth', 'currency'));
    }

    public function store(StorePaymentRequest $request)
    {
        // Ensure project belongs to this org
        $project = $this->org()->projects()->findOrFail($request->project_id);

        // If a document / installment is specified, ensure it belongs to this org
        if ($request->document_id) {
            $this->org()->documents()->findOrFail($request->document_id);
        }
        if ($request->installment_id) {
            $project->installments()->findOrFail($request->installment_id);
        }

        $data = $request->validated();
        $data['organisation_id'] = $this->org()->id;
        $generateReceipt = (bool) ($data['generate_receipt'] ?? false);
        unset($data['generate_receipt']);

        $payment = Payment::create($data);

        // Keep the installment plan status current.
        $payment->loadMissing('installment');
        $payment->installment?->syncStatus();

        $receipt = $generateReceipt ? $this->generateReceipt($project, $payment) : null;

        $redirectTo = $receipt
            ? route('documents.show', $receipt)
            : route('projects.show', $project);

        $message = $receipt
            ? "Payment recorded. Receipt {$receipt->number} generated."
            : 'Payment recorded successfully.';

        // HTMX: a receipt means navigate away; otherwise refresh the project body.
        if ($request->header('HX-Request')) {
            if ($receipt) {
                return response()->noContent()->header('HX-Redirect', $redirectTo);
            }
            return $this->projectBodyResponse($request, $project, $message);
        }

        return redirect($redirectTo)->with('success', $message);
    }

    public function create()
    {
        $org = $this->org();
        $projects = $org->projects()
            ->whereNotIn('status', ['cancelled'])
            ->with('client')
            ->orderBy('title')
            ->get();

        $selectedProjectId = request('project_id');
        $currency = $org->currency;

        // Load open documents for the pre-selected project
        $documents = collect();
        $installments = collect();
        if ($selectedProjectId) {
            $documents = $org->documents()
                ->where('project_id', $selectedProjectId)
                ->whereIn('type', ['invoice', 'quote'])
                ->get();

            $installments = $org->projects()->findOrFail($selectedProjectId)
                ->installments()
                ->get();
        }

        return view('payments.create', compact(
            'projects', 'selectedProjectId', 'documents', 'installments', 'currency'
        ));
    }

    public function destroy(Payment $payment)
    {
        if ($payment->organisation_id !== $this->org()->id) {
            abort(403);
        }

        $projectId = $payment->project_id;
        $installment = $payment->installment;

        $payment->delete();
        $installment?->syncStatus();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('success', 'Payment deleted.');
    }

    // ── Private ────────────────────────────────────────────────────

    /**
     * Create a receipt document that evidences a single payment.
     */
    private function generateReceipt(Project $project, Payment $payment): Document
    {
        $org = $this->org();

        return Document::create([
            'organisation_id' => $org->id,
            'project_id' => $project->id,
            'payment_id' => $payment->id,
            'template_id' => $this->defaultTemplateId(),
            'type' => 'receipt',
            'number' => $org->nextDocumentNumber('receipt'),
            'issue_date' => $payment->paid_on,
            'sent_at' => now(), // a receipt is issued the moment it is created
        ]);
    }

    /**
     * A template id whose PDF class definitely exists.
     */
    private function defaultTemplateId(): int
    {
        return $this->org()->default_template_id
            ?? Template::whereIn('slug', ['template-001', 'template-002'])->value('id')
            ?? Template::query()->value('id');
    }
}
