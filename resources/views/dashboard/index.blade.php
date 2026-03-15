<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </x-slot>

    {{-- ── KPI Cards ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted f-13">Total Revenue</span>
                        <div class="bg-light-primary rounded p-2">
                            <i data-feather="trending-up" style="width:16px;height:16px" class="text-primary"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $currency }} {{ number_format($totalRevenue, 2) }}</h4>
                    <small class="text-muted">All time collected</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted f-13">Outstanding</span>
                        <div class="bg-light-warning rounded p-2">
                            <i data-feather="clock" style="width:16px;height:16px" class="text-warning"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $currency }} {{ number_format($outstanding, 2) }}</h4>
                    <small class="text-muted">Unpaid sent invoices</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted f-13">Active Projects</span>
                        <div class="bg-light-success rounded p-2">
                            <i data-feather="briefcase" style="width:16px;height:16px" class="text-success"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $activeProjects }}</h4>
                    <small class="text-muted">Active &amp; quoted</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted f-13">This Month</span>
                        <div class="bg-light-info rounded p-2">
                            <i data-feather="calendar" style="width:16px;height:16px" class="text-info"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $currency }} {{ number_format($monthRevenue, 2) }}</h4>
                    <small class="text-muted">{{ now()->format('F Y') }}</small>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Row 2: Projects + Payments ───────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Recent Projects --}}
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h6 class="mb-0">Recent Projects</h6>
                    <a href="{{ route('projects.index') }}" class="f-13 text-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentProjects as $project)
                        @php
                            $badge = [
                                'draft'     => 'secondary',
                                'quoted'    => 'info',
                                'active'    => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            ][$project->status] ?? 'secondary';
                        @endphp
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:32px;height:32px">
                                    <i data-feather="briefcase" style="width:14px;height:14px" class="text-muted"></i>
                                </div>
                                <div>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="fw-semibold text-dark f-14 d-block">
                                        {{ $project->title }}
                                    </a>
                                    <small class="text-muted">{{ $project->client->name }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                            <span class="badge badge-light-{{ $badge }} text-capitalize d-block mb-1">
                                {{ $project->status }}
                            </span>
                                @if($project->due_date)
                                    <small class="text-muted">{{ $project->due_date->format('d M') }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <p class="mb-1 f-14">No projects yet.</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                Create one
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h6 class="mb-0">Recent Payments</h6>
                    <a href="{{ route('payments.index') }}" class="f-13 text-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $pay)
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div>
                            <span class="fw-semibold f-14 d-block">
                                {{ $currency }} {{ number_format($pay->amount, 2) }}
                            </span>
                                <small class="text-muted">
                                    {{ $pay->client_name }} · {{ ucfirst($pay->method) }}
                                </small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">
                                    {{ \Carbon\Carbon::parse($pay->paid_on)->format('d M Y') }}
                                </small>
                                @if($pay->reference)
                                    <small class="text-muted">{{ $pay->reference }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0 f-14">No payments recorded yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- ── Row 3: Project status + Unpaid invoices ──────────── --}}
    <div class="row g-4">

        {{-- Project breakdown --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Project Breakdown</h6>
                </div>
                <div class="card-body">
                    @foreach(['draft' => 'secondary', 'quoted' => 'info', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'] as $status => $color)
                        @php $count = $projectStatusCounts[$status] ?? 0; @endphp
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-{{ $color }}"
                                      style="width:10px;height:10px;border-radius:50%;padding:0">&nbsp;</span>
                                <span class="text-capitalize f-14">{{ $status }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="width:80px;height:6px">
                                    @php
                                        $total = $projectStatusCounts->sum();
                                        $pct = $total > 0 ? round(($count / $total) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="fw-semibold f-13"
                                      style="min-width:20px;text-align:right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Unpaid invoices --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h6 class="mb-0">Unpaid Invoices</h6>
                    <a href="{{ route('documents.index') }}" class="f-13 text-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    @forelse($unpaidInvoices as $inv)
                        @php
                            $isOverdue = $inv->due_date && \Carbon\Carbon::parse($inv->due_date)->isPast();
                        @endphp
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div>
                                <a href="{{ route('documents.show', $inv->id) }}"
                                   class="fw-semibold text-dark f-14 d-block">
                                    {{ $inv->number }}
                                </a>
                                <small class="text-muted">{{ $inv->client_name }} · {{ $inv->project_title }}</small>
                            </div>
                            <div class="text-end">
                            <span class="fw-semibold f-14 {{ $isOverdue ? 'text-danger' : 'text-warning' }} d-block">
                                {{ $currency }} {{ number_format($inv->balance, 2) }}
                            </span>
                                @if($inv->due_date)
                                    <small class="{{ $isOverdue ? 'text-danger' : 'text-muted' }}">
                                        {{ $isOverdue ? 'Overdue · ' : 'Due ' }}{{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i data-feather="check-circle" style="width:28px;height:28px"
                               class="text-success mb-2 d-block mx-auto"></i>
                            <p class="text-muted mb-0 f-14">All invoices are settled.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
