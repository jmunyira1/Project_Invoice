<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
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

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function store(StorePaymentRequest $request)
    {
        // Ensure project belongs to this org
        $this->org()->projects()->findOrFail($request->project_id);

        // If a document is specified, ensure it belongs to this org
        if ($request->document_id) {
            $this->org()->documents()->findOrFail($request->document_id);
        }

        $data = $request->validated();
        $data['organisation_id'] = $this->org()->id;

        Payment::create($data);

        return redirect()
            ->route('projects.show', $request->project_id)
            ->with('success', 'Payment recorded successfully.');
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
        if ($selectedProjectId) {
            $documents = $org->documents()
                ->where('project_id', $selectedProjectId)
                ->whereIn('type', ['invoice', 'quote'])
                ->get();
        }

        return view('payments.create', compact(
            'projects', 'selectedProjectId', 'documents', 'currency'
        ));
    }

    public function destroy(Payment $payment)
    {
        if ($payment->organisation_id !== $this->org()->id) {
            abort(403);
        }

        $projectId = $payment->project_id;
        $payment->delete();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('success', 'Payment deleted.');
    }
}
