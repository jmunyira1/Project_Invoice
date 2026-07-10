{{-- Deliverables --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Deliverables</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addDeliverable">
            <i class="bi bi-plus-lg me-1"></i>Add
        </button>
    </div>

    <div class="collapse border-bottom bg-body-tertiary" id="addDeliverable">
        <form class="p-3" method="POST" action="{{ route('projects.deliverables.store', $project) }}"
              hx-post="{{ route('projects.deliverables.store', $project) }}"
              hx-target="#project-body" hx-swap="outerHTML">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Logo design" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Description</label>
                    <input type="text" name="description" class="form-control form-control-sm" placeholder="Optional">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Qty *</label>
                    <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="0.01" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Unit Price *</label>
                    <input type="number" name="unit_price" class="form-control form-control-sm" value="0" min="0" step="0.01" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        @if($project->deliverables->isEmpty())
            <p class="text-muted text-center py-4 mb-0 small">No deliverables yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-clean align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($project->deliverables as $d)
                        <tr>
                            <td class="fw-semibold">{{ $d->name }}</td>
                            <td class="text-muted small">{{ $d->description ?? '—' }}</td>
                            <td class="text-end">{{ rtrim(rtrim(number_format($d->quantity, 2), '0'), '.') }}</td>
                            <td class="text-end">{{ number_format($d->unit_price, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($d->total_price, 2) }}</td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-light btn-sm" data-bs-toggle="collapse"
                                        data-bs-target="#editD{{ $d->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('projects.deliverables.destroy', [$project, $d]) }}"
                                      class="d-inline"
                                      hx-delete="{{ route('projects.deliverables.destroy', [$project, $d]) }}"
                                      hx-target="#project-body" hx-swap="outerHTML"
                                      hx-confirm="Remove this deliverable?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr class="collapse" id="editD{{ $d->id }}">
                            <td colspan="6" class="bg-body-tertiary">
                                <form method="POST" action="{{ route('projects.deliverables.update', [$project, $d]) }}"
                                      hx-patch="{{ route('projects.deliverables.update', [$project, $d]) }}"
                                      hx-target="#project-body" hx-swap="outerHTML">
                                    @csrf @method('PATCH')
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $d->name }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="description" class="form-control form-control-sm" value="{{ $d->description }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $d->quantity }}" min="0.01" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="unit_price" class="form-control form-control-sm" value="{{ $d->unit_price }}" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Deliverables Total</th>
                        <th class="text-end text-primary">{{ $currency }} {{ number_format($project->deliverables_total, 2) }}</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
