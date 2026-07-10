{{-- Generated documents --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Documents</h6>
        <a href="{{ route('documents.create', ['project_id' => $project->id]) }}"
           hx-get="{{ route('documents.create', ['project_id' => $project->id]) }}" hx-target="#app-modal-content"
           class="btn btn-primary btn-sm" title="Generate document">
            <i class="bi bi-plus-lg"></i>
        </a>
    </div>
    <div class="card-body p-0">
        @forelse($project->documents as $doc)
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <div>
                    <span class="badge badge-soft-secondary text-uppercase">{{ str_replace('_', ' ', $doc->type) }}</span>
                    <span class="small fw-semibold ms-1">{{ $doc->number }}</span>
                    <div class="text-muted" style="font-size:.72rem">{{ $doc->issue_date->format('d M Y') }}</div>
                </div>
                <a href="{{ route('documents.show', $doc) }}" class="btn btn-light btn-sm py-0"><i class="bi bi-eye"></i></a>
            </div>
        @empty
            <p class="text-muted text-center py-3 mb-0 small">No documents yet.</p>
        @endforelse
    </div>
</div>
