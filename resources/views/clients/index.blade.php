<x-app-layout>
    <x-slot name="title">Clients</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Clients</li>
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <h5>All Clients</h5>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
                        <i data-feather="plus" style="width:14px;height:14px" class="me-1"></i>
                        Add Client
                    </a>
                </div>

                <div class="card-body p-0">
                    @if($clients->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i data-feather="users" style="width:40px;height:40px;opacity:.3"
                               class="mb-3 d-block mx-auto"></i>
                            <p class="mb-2">No clients yet.</p>
                            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
                                Add your first client
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th class="text-center">Projects</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                {{-- Avatar circle --}}
                                                <div
                                                    class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:34px;height:34px;font-size:12px;font-weight:600;">
                                                    {{ $client->initials }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('clients.show', $client) }}"
                                                       class="fw-semibold text-dark d-block">
                                                        {{ $client->name }}
                                                    </a>
                                                    @if($client->address)
                                                        <small
                                                            class="text-muted">{{ Str::limit($client->address, 40) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($client->email)
                                                <a href="mailto:{{ $client->email }}" class="text-muted">
                                                    {{ $client->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $client->phone ?? '—' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light-primary">
                                                {{ $client->projects_count }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('clients.show', $client) }}"
                                               class="btn btn-light btn-sm me-1"
                                               title="View">
                                                <i data-feather="eye" style="width:13px;height:13px"></i>
                                            </a>
                                            <a href="{{ route('clients.edit', $client) }}"
                                               class="btn btn-light btn-sm me-1"
                                               title="Edit">
                                                <i data-feather="edit-2" style="width:13px;height:13px"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('clients.destroy', $client) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete {{ addslashes($client->name) }}? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-light btn-sm text-danger"
                                                        title="Delete">
                                                    <i data-feather="trash-2" style="width:13px;height:13px"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($clients->hasPages())
                            <div class="p-3 border-top">
                                {{ $clients->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
