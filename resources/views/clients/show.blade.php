<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.index') }}">Clients</a>
        </li>
        <li class="breadcrumb-item active">{{ $client->name }}</li>
    </x-slot>

    <div class="row">

        {{-- Client profile card --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-body text-center pt-4">
                    {{-- Avatar --}}
                    <div
                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width:64px;height:64px;font-size:22px;font-weight:600;">
                        {{ $client->initials }}
                    </div>
                    <h5 class="mb-0">{{ $client->name }}</h5>
                    <p class="text-muted mb-3 f-14">Client</p>

                    <div class="d-flex gap-2 justify-content-center mb-4">
                        <a href="{{ route('clients.edit', $client) }}"
                           class="btn btn-primary btn-sm">
                            <i data-feather="edit-2" style="width:13px;height:13px" class="me-1"></i>
                            Edit
                        </a>
                        <form method="POST"
                              action="{{ route('clients.destroy', $client) }}"
                              onsubmit="return confirm('Delete this client?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light btn-sm text-danger">
                                <i data-feather="trash-2" style="width:13px;height:13px" class="me-1"></i>
                                Delete
                            </button>
                        </form>
                    </div>

                    <hr>

                    {{-- Contact details --}}
                    <div class="text-start">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i data-feather="mail" style="width:15px;height:15px" class="text-muted flex-shrink-0"></i>
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="text-dark f-14">
                                    {{ $client->email }}
                                </a>
                            @else
                                <span class="text-muted f-14">No email</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i data-feather="phone" style="width:15px;height:15px" class="text-muted flex-shrink-0"></i>
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="text-dark f-14">
                                    {{ $client->phone }}
                                </a>
                            @else
                                <span class="text-muted f-14">No phone</span>
                            @endif
                        </div>
                        @if($client->address)
                            <div class="d-flex align-items-start gap-2">
                                <i data-feather="map-pin" style="width:15px;height:15px"
                                   class="text-muted flex-shrink-0 mt-1"></i>
                                <span class="text-dark f-14" style="white-space:pre-line">{{ $client->address }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats card --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted f-14">Total projects</span>
                        <span class="fw-semibold">{{ $client->projects->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted f-14">Active</span>
                        <span class="fw-semibold">
                            {{ $client->projects->whereIn('status', ['active', 'quoted'])->count() }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted f-14">Completed</span>
                        <span class="fw-semibold">
                            {{ $client->projects->where('status', 'completed')->count() }}
                        </span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted f-14">Total value</span>
                        <span class="fw-semibold text-primary">
                            {{ $currency }}
                            {{ number_format($client->total_project_value, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Projects list --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h5>Projects</h5>
                    {{-- Will link to create project pre-filled with this client --}}
                    <a href="{{ route('projects.create', ['client_id' => $client->id]) }}"
                       class="btn btn-primary btn-sm">
                        <i data-feather="plus" style="width:14px;height:14px" class="me-1"></i>
                        New Project
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($client->projects->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i data-feather="briefcase" style="width:36px;height:36px;opacity:.3"
                               class="d-block mx-auto mb-2"></i>
                            <p class="mb-0">No projects yet for this client.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Due date</th>
                                    <th class="text-end">Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($client->projects as $project)
                                    @php
                                        $badge = [
                                            'draft'     => 'secondary',
                                            'quoted'    => 'info',
                                            'active'    => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                        ][$project->status] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('projects.show', $project) }}"
                                               class="fw-semibold text-dark">
                                                {{ $project->title }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $badge }} text-capitalize">
                                                {{ $project->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted f-14">
                                            {{ $project->due_date?->format('d M Y') ?? '—' }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            {{ $currency }}
                                            {{ number_format(
                                                $project->deliverables->sum(fn($d) => $d->quantity * $d->unit_price),
                                                2
                                            ) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
