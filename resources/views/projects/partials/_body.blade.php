{{-- The whole swappable project body. HTMX mutations re-render this fragment. --}}
<div id="project-body">

    {{-- ── Header row ─────────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1">{{ $project->title }}</h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-soft-{{ $project->status_badge }} text-capitalize">
                    {{ $project->status }}
                </span>
                <span class="text-muted">·</span>
                <a href="{{ route('clients.show', $project->client) }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-person me-1"></i>{{ $project->client->name }}
                </a>
                @if($project->due_date)
                    <span class="text-muted">·</span>
                    <span class="text-muted small"><i class="bi bi-calendar-event me-1"></i>Due {{ $project->due_date->format('d M Y') }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @foreach($project->allowed_transitions as $next)
                <form method="POST" action="{{ route('projects.status', $project) }}"
                      hx-patch="{{ route('projects.status', $project) }}"
                      hx-target="#project-body" hx-swap="outerHTML">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next }}">
                    <button type="submit"
                            class="btn btn-sm {{ $next === 'cancelled' ? 'btn-outline-danger' : 'btn-outline-primary' }} text-capitalize">
                        {{ $next }}
                    </button>
                </form>
            @endforeach
            <a href="{{ route('projects.edit', $project) }}"
               hx-get="{{ route('projects.edit', $project) }}" hx-target="#app-modal-content"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>

    {{-- ── Financial summary ──────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        @php
            $tiles = [
                ['Project Value', $project->total_value, 'text-primary', 'bi-cash-stack', 'primary'],
                ['Total Costs', $project->total_costs, 'text-danger', 'bi-arrow-down-circle', 'danger'],
                ['Gross Profit', $project->profit, $project->profit >= 0 ? 'text-success' : 'text-danger', 'bi-graph-up-arrow', 'success'],
                ['Balance Due', $project->balance, $project->balance > 0 ? 'text-warning' : 'text-success', 'bi-wallet2', 'warning'],
            ];
        @endphp
        @foreach($tiles as [$label, $value, $cls, $icon, $tone])
            <div class="col-6 col-xl-3">
                <div class="card mb-0 stat-tile">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <span class="stat-icon badge-soft-{{ $tone }}"><i class="bi {{ $icon }}"></i></span>
                        <div>
                            <p class="text-muted small mb-1">{{ $label }}</p>
                            <h5 class="mb-0 {{ $cls }}">{{ $currency }} {{ number_format($value, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Payment progress --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Collected {{ $currency }} {{ number_format($project->total_paid, 2) }}
                    of {{ $currency }} {{ number_format($project->gross_value, 2) }}
                    @if($project->tax_total > 0)<span class="text-muted">(incl. VAT)</span>@endif</span>
                <span class="fw-semibold">{{ $project->paid_percent }}%</span>
            </div>
            <div class="progress" style="height:8px">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $project->paid_percent }}%"></div>
            </div>
            @if($project->tax_total > 0)
                <div class="d-flex justify-content-between small text-muted mt-2">
                    <span>Net {{ $currency }} {{ number_format($project->total_value, 2) }}
                        · VAT {{ $currency }} {{ number_format($project->tax_total, 2) }}</span>
                    <span>Balance {{ $currency }} {{ number_format($project->balance, 2) }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN ────────────────────────────────────────── --}}
        <div class="col-lg-8">
            @include('projects.partials._deliverables')
            @include('projects.partials._costs')
        </div>

        {{-- ── RIGHT COLUMN ───────────────────────────────────────── --}}
        <div class="col-lg-4">
            @include('projects.partials._details')
            @include('projects.partials._installments')
            @include('projects.partials._payments')
            @include('projects.partials._files')
            @include('projects.partials._documents')
        </div>
    </div>

    {{-- Prefill helper for "Record payment" against a specific installment --}}
    <script>
        window.prefillPayment = function (opts) {
            const box = document.getElementById('record-payment');
            if (!box) return;
            new bootstrap.Collapse(box, { toggle: false }).show();
            const set = (name, val) => { const el = box.querySelector(`[name="${name}"]`); if (el != null && val != null) el.value = val; };
            if (opts.installment_id) set('installment_id', opts.installment_id);
            if (opts.amount)         set('amount', opts.amount);
            if (opts.kind)           set('kind', opts.kind);
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        };
    </script>
</div>
