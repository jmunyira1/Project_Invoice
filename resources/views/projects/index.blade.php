<x-app-layout>
    <x-slot name="title">Projects</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Projects</li>
    </x-slot>

    {{-- Status summary pills --}}
    <div class="row g-3 mb-4">
        @foreach(['draft' => 'secondary', 'quoted' => 'info', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'] as $status => $color)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card mb-0 text-center py-2">
                    <div class="card-body p-2">
                        <h4 class="mb-0 text-{{ $color }}">{{ $statusCounts[$status] ?? 0 }}</h4>
                        <small class="text-muted text-capitalize">{{ $status }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between pb-0">
            <h5>All Projects</h5>
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                <i data-feather="plus" style="width:14px;height:14px" class="me-1"></i>
                New Project
            </a>
        </div>
        <div class="card-body p-0">
            @if($projects->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i data-feather="briefcase" style="width:40px;height:40px;opacity:.3"
                       class="d-block mx-auto mb-3"></i>
                    <p class="mb-2">No projects yet.</p>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                        Create your first project
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="fw-semibold text-dark d-block">
                                        {{ $project->title }}
                                    </a>
                                    <small class="text-muted">
                                        {{ $project->deliverables_count }}
                                        deliverable{{ $project->deliverables_count !== 1 ? 's' : '' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('clients.show', $project->client) }}"
                                       class="text-muted">
                                        {{ $project->client->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $project->status_badge }} text-capitalize">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="text-muted f-14">
                                    {{ $project->due_date?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="btn btn-light btn-sm me-1" title="View">
                                        <i data-feather="eye" style="width:13px;height:13px"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="btn btn-light btn-sm me-1" title="Edit">
                                        <i data-feather="edit-2" style="width:13px;height:13px"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('projects.destroy', $project) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this project? All deliverables and costs will be lost.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete">
                                            <i data-feather="trash-2" style="width:13px;height:13px"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($projects->hasPages())
                    <div class="p-3 border-top">{{ $projects->links() }}</div>
                @endif
            @endif
        </div>
    </div>

</x-app-layout>
