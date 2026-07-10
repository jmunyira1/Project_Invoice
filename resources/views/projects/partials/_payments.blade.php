{{-- Payments --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Payments</h6>
        <button class="btn btn-success btn-sm" data-bs-toggle="collapse" data-bs-target="#record-payment">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <div class="collapse border-bottom bg-body-tertiary" id="record-payment">
        <form class="p-3" method="POST" action="{{ route('payments.store') }}"
              hx-post="{{ route('payments.store') }}"
              hx-target="#project-body" hx-swap="outerHTML">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small">Amount *</label>
                    <input type="number" name="amount" class="form-control form-control-sm" min="0.01" step="0.01" required>
                </div>
                <div class="col-6">
                    <label class="form-label small">Type *</label>
                    <select name="kind" class="form-select form-select-sm" required>
                        <option value="deposit">Deposit</option>
                        <option value="part_payment" selected>Part Payment</option>
                        <option value="installment">Installment</option>
                        <option value="balance">Balance</option>
                        <option value="refund">Refund</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small">Method *</label>
                    <select name="method" class="form-select form-select-sm" required>
                        <option value="mpesa">M-Pesa</option>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small">Paid on *</label>
                    <input type="date" name="paid_on" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label small">Reference</label>
                    <input type="text" name="reference" class="form-control form-control-sm" placeholder="M-Pesa code, cheque no…">
                </div>
                @if($project->documents->whereIn('type', ['invoice','quote'])->isNotEmpty())
                    <div class="col-12">
                        <label class="form-label small">Apply to invoice</label>
                        <select name="document_id" class="form-select form-select-sm">
                            <option value="">— None —</option>
                            @foreach($project->documents->whereIn('type', ['invoice','quote']) as $d)
                                <option value="{{ $d->id }}">{{ $d->type_label }} {{ $d->number }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if($project->installments->isNotEmpty())
                    <div class="col-12">
                        <label class="form-label small">Settle installment</label>
                        <select name="installment_id" class="form-select form-select-sm">
                            <option value="">— None —</option>
                            @foreach($project->installments as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->label }} ({{ $currency }} {{ number_format($inst->balance, 2) }} left)</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="generate_receipt" value="1" id="genReceipt">
                <label class="form-check-label small" for="genReceipt">Generate a receipt document</label>
            </div>

            <button type="submit" class="btn btn-success btn-sm w-100 mt-2">
                <i class="bi bi-check-lg me-1"></i>Record payment
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        @forelse($project->payments as $pay)
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <div>
                    <span class="fw-semibold small">{{ $currency }} {{ number_format($pay->amount, 2) }}</span>
                    <span class="badge badge-soft-{{ $pay->kind_badge }} ms-1">{{ $pay->kind_label }}</span>
                    <div class="text-muted text-capitalize" style="font-size:.72rem">
                        {{ $pay->method_label }} · {{ $pay->paid_on->format('d M Y') }}
                        @if($pay->reference) · {{ $pay->reference }} @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('payments.destroy', $pay) }}"
                      onsubmit="return confirm('Delete this payment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-light btn-sm text-danger py-0"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        @empty
            <p class="text-muted text-center py-3 mb-0 small">No payments recorded.</p>
        @endforelse

        @if($project->payments->isNotEmpty())
            <div class="d-flex justify-content-between px-3 py-2 small">
                <span class="text-muted">Total paid</span>
                <span class="fw-semibold text-success">{{ $currency }} {{ number_format($project->total_paid, 2) }}</span>
            </div>
        @endif
    </div>
</div>
