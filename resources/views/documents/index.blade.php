<x-app-layout>
    <x-slot name="title">Documents</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Documents</li>
    </x-slot>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between pb-0">
            <h5>All Documents</h5>
            <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
                <i data-feather="plus" style="width:14px;height:14px" class="me-1"></i>
                New Document
            </a>
        </div>

        <div class="card-body p-0">
            @if($documents->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i data-feather="file-text" style="width:40px;height:40px;opacity:.3"
                       class="d-block mx-auto mb-3"></i>
                    <p class="mb-2">No documents yet.</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
                        Create your first document
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Number</th>
                            <th>Type</th>
                            <th>Project / Client</th>
                            <th>Issue Date</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($documents as $doc)
                            <tr>
                                <td>
                                    <a href="{{ route('documents.show', $doc) }}"
                                       class="fw-semibold text-dark">
                                        {{ $doc->number }}
                                    </a>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center gap-1 text-muted f-13">
                                        <i data-feather="{{ $doc->type_icon }}"
                                           style="width:13px;height:13px"></i>
                                        {{ $doc->type_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold f-13">{{ $doc->project->title }}</span>
                                    <br>
                                    <small class="text-muted">{{ $doc->project->client->name }}</small>
                                </td>
                                <td class="text-muted f-13">
                                    {{ $doc->issue_date->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $doc->status_badge }}">
                                        {{ $doc->status_label }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ $doc->project->organisation->currency }}
                                    {{ number_format($doc->lines->sum('total_price'), 2) }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('documents.show', $doc) }}"
                                       class="btn btn-light btn-sm me-1" title="View">
                                        <i data-feather="eye" style="width:13px;height:13px"></i>
                                    </a>
                                    <a href="{{ route('documents.pdf', $doc) }}"
                                       class="btn btn-light btn-sm me-1" title="PDF" target="_blank">
                                        <i data-feather="download" style="width:13px;height:13px"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('documents.destroy', $doc) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this document?')">
                                        @csrf @method('DELETE')
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
                @if($documents->hasPages())
                    <div class="p-3 border-top">{{ $documents->links() }}</div>
                @endif
            @endif
        </div>
    </div>

</x-app-layout>
