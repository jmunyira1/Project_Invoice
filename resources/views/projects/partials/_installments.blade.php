{{-- Installment plan --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-calendar2-week me-2"></i>Installment Plan</h6>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addInstallment">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <div class="collapse border-bottom bg-body-tertiary" id="addInstallment">
        <form class="p-3" method="POST" action="{{ route('projects.installments.store', $project) }}"
              hx-post="{{ route('projects.installments.store', $project) }}"
              hx-target="#project-body" hx-swap="outerHTML">
            @csrf
            <div class="mb-2">
                <label class="form-label small">Label *</label>
                <input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Deposit, Milestone 1" required>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small">Amount *</label>
                    <input type="number" name="amount" class="form-control form-control-sm" min="0.01" step="0.01" required>
                </div>
                <div class="col-6">
                    <label class="form-label small">Due date</label>
                    <input type="date" name="due_date" class="form-control form-control-sm">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm w-100 mt-2">Add installment</button>
        </form>
    </div>

    <div class="card-body p-0">
        @forelse($project->installments as $inst)
            <div class="px-3 py-2 border-bottom {{ $inst->effective_status === 'overdue' ? 'bg-danger-subtle' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="fw-semibold small">{{ $inst->label }}</span>
                        <span class="badge badge-soft-{{ $inst->status_badge }} ms-1">{{ $inst->status_label }}</span>
                        <div class="text-muted" style="font-size:.72rem">
                            @if($inst->due_date)
                                <i class="bi bi-calendar-event me-1"></i>Due {{ $inst->due_date->format('d M Y') }}
                            @else
                                No due date
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold small">{{ $currency }} {{ number_format($inst->amount, 2) }}</div>
                        <div class="text-muted" style="font-size:.72rem">
                            Paid {{ number_format($inst->paid, 2) }}
                        </div>
                    </div>
                </div>
                <div class="progress mt-2" style="height:5px">
                    <div class="progress-bar {{ $inst->effective_status === 'paid' ? 'bg-success' : ($inst->effective_status === 'overdue' ? 'bg-danger' : 'bg-info') }}"
                         style="width: {{ $inst->progress * 100 }}%"></div>
                </div>
                <div class="d-flex justify-content-end gap-1 mt-2">
                    @if($inst->balance > 0)
                        <button type="button" class="btn btn-outline-success btn-sm py-0"
                                onclick="prefillPayment({installment_id: {{ $inst->id }}, amount: {{ $inst->balance }}, kind: 'installment'})">
                            <i class="bi bi-cash me-1"></i>Record payment
                        </button>
                    @endif
                    <form method="POST" action="{{ route('projects.installments.destroy', [$project, $inst]) }}"
                          hx-delete="{{ route('projects.installments.destroy', [$project, $inst]) }}"
                          hx-target="#project-body" hx-swap="outerHTML"
                          hx-confirm="Remove this installment? Payments are kept.">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm py-0 text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted text-center py-3 mb-0 small">No installment plan yet.</p>
        @endforelse

        @if($project->installments->isNotEmpty())
            <div class="d-flex justify-content-between px-3 py-2 small">
                <span class="text-muted">Scheduled total</span>
                <span class="fw-semibold">{{ $currency }} {{ number_format($project->scheduled_total, 2) }}</span>
            </div>
        @endif
    </div>
</div>
