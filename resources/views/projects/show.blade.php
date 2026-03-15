<x-app-layout>
    <x-slot name="title">{{ $project->title }}</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
        <li class="breadcrumb-item active">{{ $project->title }}</li>
    </x-slot>

    {{-- Header row --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1">{{ $project->title }}</h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-light-{{ $project->status_badge }} text-capitalize fs-6">
                    {{ $project->status }}
                </span>
                <span class="text-muted">·</span>
                <a href="{{ route('clients.show', $project->client) }}" class="text-muted f-14">
                    {{ $project->client->name }}
                </a>
                @if($project->due_date)
                    <span class="text-muted">·</span>
                    <span class="text-muted f-14">
                        Due {{ $project->due_date->format('d M Y') }}
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            {{-- Status transitions --}}
            @foreach($project->allowed_transitions as $next)
                <form method="POST" action="{{ route('projects.status', $project) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next }}">
                    <button type="submit"
                            class="btn btn-sm {{ $next === 'cancelled' ? 'btn-light text-danger' : 'btn-outline-primary' }}">
                        {{ ucfirst($next) }}
                    </button>
                </form>
            @endforeach
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-light btn-sm">
                <i data-feather="edit-2" style="width:13px;height:13px" class="me-1"></i>Edit
            </a>
        </div>
    </div>

    {{-- Financial summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <p class="text-muted f-13 mb-1">Project Value</p>
                    <h5 class="mb-0 text-primary">
                        {{ $currency }} {{ number_format($project->total_value, 2) }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <p class="text-muted f-13 mb-1">Total Costs</p>
                    <h5 class="mb-0 text-danger">
                        {{ $currency }} {{ number_format($project->total_costs, 2) }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <p class="text-muted f-13 mb-1">Gross Profit</p>
                    <h5 class="mb-0 {{ $project->profit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $currency }} {{ number_format($project->profit, 2) }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <p class="text-muted f-13 mb-1">Balance Due</p>
                    <h5 class="mb-0 {{ $project->balance > 0 ? 'text-warning' : 'text-success' }}">
                        {{ $currency }} {{ number_format($project->balance, 2) }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">

            {{-- Deliverables --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h6 class="mb-0">Deliverables</h6>
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="collapse"
                            data-bs-target="#addDeliverable">
                        <i data-feather="plus" style="width:13px;height:13px" class="me-1"></i>
                        Add
                    </button>
                </div>

                {{-- Add deliverable form --}}
                <div class="collapse px-3 pt-3" id="addDeliverable">
                    <form method="POST"
                          action="{{ route('projects.deliverables.store', $project) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label f-13">Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                       placeholder="e.g. Logo design" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label f-13">Description</label>
                                <input type="text" name="description" class="form-control form-control-sm"
                                       placeholder="Optional">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label f-13">Qty *</label>
                                <input type="number" name="quantity" class="form-control form-control-sm"
                                       value="1" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label f-13">Unit Price *</label>
                                <input type="number" name="unit_price" class="form-control form-control-sm"
                                       value="0" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>

                <div class="card-body p-0">
                    @if($project->deliverables->isEmpty())
                        <p class="text-muted text-center py-4 mb-0 f-14">No deliverables yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
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
                                        <td class="text-muted f-13">{{ $d->description ?? '—' }}</td>
                                        <td class="text-end">{{ $d->quantity }}</td>
                                        <td class="text-end">{{ number_format($d->unit_price, 2) }}</td>
                                        <td class="text-end fw-semibold">
                                            {{ number_format($d->total_price, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-light btn-sm me-1"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#editD{{ $d->id }}"
                                                    title="Edit">
                                                <i data-feather="edit-2" style="width:12px;height:12px"></i>
                                            </button>
                                            <form method="POST"
                                                  action="{{ route('projects.deliverables.destroy', [$project, $d]) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Remove this deliverable?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm text-danger"
                                                        title="Delete">
                                                    <i data-feather="trash-2" style="width:12px;height:12px"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    {{-- Inline edit row --}}
                                    <tr class="collapse bg-light" id="editD{{ $d->id }}">
                                        <td colspan="6" class="p-2">
                                            <form method="POST"
                                                  action="{{ route('projects.deliverables.update', [$project, $d]) }}">
                                                @csrf @method('PATCH')
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-md-4">
                                                        <input type="text" name="name"
                                                               class="form-control form-control-sm"
                                                               value="{{ $d->name }}" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" name="description"
                                                               class="form-control form-control-sm"
                                                               value="{{ $d->description }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number" name="quantity"
                                                               class="form-control form-control-sm"
                                                               value="{{ $d->quantity }}"
                                                               min="0.01" step="0.01" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number" name="unit_price"
                                                               class="form-control form-control-sm"
                                                               value="{{ $d->unit_price }}"
                                                               min="0" step="0.01" required>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="submit"
                                                                class="btn btn-success btn-sm w-100">
                                                            Save
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th class="text-end text-primary">
                                        {{ $currency }} {{ number_format($project->total_value, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Internal Costs --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div>
                        <h6 class="mb-0">Internal Costs</h6>
                        <small class="text-muted">Not visible on client documents</small>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="collapse"
                            data-bs-target="#addCost">
                        <i data-feather="plus" style="width:13px;height:13px" class="me-1"></i>
                        Add
                    </button>
                </div>

                {{-- Add cost form --}}
                <div class="collapse px-3 pt-3" id="addCost">
                    <form method="POST" action="{{ route('projects.costs.store', $project) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label f-13">Title *</label>
                                <input type="text" name="title" class="form-control form-control-sm"
                                       placeholder="e.g. Stock photos" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label f-13">Amount *</label>
                                <input type="number" name="amount" class="form-control form-control-sm"
                                       min="0" step="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label f-13">Date *</label>
                                <input type="date" name="incurred_on"
                                       class="form-control form-control-sm"
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label f-13">Notes</label>
                                <input type="text" name="notes" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>

                <div class="card-body p-0">
                    @if($project->costs->isEmpty())
                        <p class="text-muted text-center py-4 mb-0 f-14">No costs recorded.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
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
                                        <td class="text-muted f-13">{{ $c->incurred_on->format('d M Y') }}</td>
                                        <td class="text-muted f-13">{{ $c->notes ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($c->amount, 2) }}</td>
                                        <td class="text-end">
                                            <form method="POST"
                                                  action="{{ route('projects.costs.destroy', [$project, $c]) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this cost?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm text-danger">
                                                    <i data-feather="trash-2" style="width:12px;height:12px"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total Costs</th>
                                    <th class="text-end text-danger">
                                        {{ $currency }} {{ number_format($project->total_costs, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">

            {{-- Project info --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Project Details</h6>
                    @if($project->description)
                        <p class="text-muted f-14" style="white-space:pre-line">{{ $project->description }}</p>
                        <hr>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted f-13">Client</span>
                        <a href="{{ route('clients.show', $project->client) }}" class="f-13 fw-semibold">
                            {{ $project->client->name }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted f-13">Created</span>
                        <span class="f-13">{{ $project->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted f-13">Due Date</span>
                        <span class="f-13">{{ $project->due_date?->format('d M Y') ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Documents --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h6 class="mb-0">Documents</h6>
                    <a href="{{ route('documents.create', ['project_id' => $project->id]) }}"
                       class="btn btn-primary btn-sm">
                        <i data-feather="plus" style="width:13px;height:13px"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($project->documents as $doc)
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div>
                                <span class="badge badge-light-secondary f-11 me-1 text-uppercase">
                                    {{ str_replace('_', ' ', $doc->type) }}
                                </span>
                                <span class="f-13 fw-semibold">{{ $doc->number }}</span>
                                <br>
                                <small class="text-muted">{{ $doc->issue_date->format('d M Y') }}</small>
                            </div>
                            <a href="{{ route('documents.show', $doc) }}"
                               class="btn btn-light btn-sm">
                                <i data-feather="eye" style="width:12px;height:12px"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0 f-13">No documents yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Payments --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h6 class="mb-0">Payments</h6>
                    <a href="{{ route('payments.create', ['project_id' => $project->id]) }}"
                       class="btn btn-success btn-sm">
                        <i data-feather="plus" style="width:13px;height:13px"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($project->payments as $pay)
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div>
                                <span class="f-13 fw-semibold">
                                    {{ $currency }} {{ number_format($pay->amount, 2) }}
                                </span>
                                <br>
                                <small class="text-muted text-capitalize">
                                    {{ $pay->method }} · {{ $pay->paid_on->format('d M Y') }}
                                </small>
                            </div>
                            @if($pay->reference)
                                <small class="text-muted">{{ $pay->reference }}</small>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0 f-13">No payments recorded.</p>
                    @endforelse
                    @if($project->payments->isNotEmpty())
                        <div class="d-flex justify-content-between px-3 py-2">
                            <span class="f-13 text-muted">Total paid</span>
                            <span class="f-13 fw-semibold text-success">
                                {{ $currency }} {{ number_format($project->total_paid, 2) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
