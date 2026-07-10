{{-- Internal Costs --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Internal Costs</h6>
            <small class="text-muted">Not visible on client documents</small>
        </div>
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#addCost">
            <i class="bi bi-plus-lg me-1"></i>Add
        </button>
    </div>

    <div class="collapse border-bottom bg-body-tertiary" id="addCost">
        <form class="p-3" method="POST" action="{{ route('projects.costs.store', $project) }}"
              hx-post="{{ route('projects.costs.store', $project) }}"
              hx-target="#project-body" hx-swap="outerHTML">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Title *</label>
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Stock photos" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Amount *</label>
                    <input type="number" name="amount" class="form-control form-control-sm" min="0" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Date *</label>
                    <input type="date" name="incurred_on" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Notes</label>
                    <input type="text" name="notes" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        @if($project->costs->isEmpty())
            <p class="text-muted text-center py-4 mb-0 small">No costs recorded.</p>
        @else
            <div class="table-responsive">
                <table class="table table-clean align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Notes</th>
                        <th class="text-end">Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($project->costs as $c)
                        <tr>
                            <td class="fw-semibold">{{ $c->title }}</td>
                            <td class="text-muted small">{{ $c->incurred_on->format('d M Y') }}</td>
                            <td class="text-muted small">{{ $c->notes ?? '—' }}</td>
                            <td class="text-end">{{ number_format($c->amount, 2) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('projects.costs.destroy', [$project, $c]) }}"
                                      class="d-inline"
                                      hx-delete="{{ route('projects.costs.destroy', [$project, $c]) }}"
                                      hx-target="#project-body" hx-swap="outerHTML"
                                      hx-confirm="Delete this cost?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total Costs</th>
                        <th class="text-end text-danger">{{ $currency }} {{ number_format($project->total_costs, 2) }}</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
