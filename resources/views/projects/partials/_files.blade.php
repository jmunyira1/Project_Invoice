{{-- Project files --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Files</h6>
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#uploadFile">
            <i class="bi bi-upload"></i>
        </button>
    </div>

    <div class="collapse border-bottom bg-body-tertiary" id="uploadFile">
        <form class="p-3" method="POST" action="{{ route('projects.files.store', $project) }}"
              enctype="multipart/form-data"
              hx-post="{{ route('projects.files.store', $project) }}"
              hx-encoding="multipart/form-data"
              hx-target="#project-body" hx-swap="outerHTML">
            @csrf
            <div class="mb-2">
                <label class="form-label small">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="contract">Contract</option>
                    <option value="brief">Brief</option>
                    <option value="reference">Reference</option>
                    <option value="signed">Signed document</option>
                    <option value="other" selected>Other</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small">File *</label>
                <input type="file" name="file" class="form-control form-control-sm" required>
                <div class="form-text" style="font-size:.7rem">Max 20 MB. PDF, Office, images, zip.</div>
            </div>
            <button type="submit" class="btn btn-success btn-sm w-100">
                <i class="bi bi-cloud-arrow-up me-1"></i>Upload
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        @forelse($project->files as $file)
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <div class="d-flex align-items-center gap-2 text-truncate">
                    <i class="bi {{ $file->icon }} fs-5 text-muted"></i>
                    <div class="text-truncate">
                        <a href="{{ route('projects.files.download', [$project, $file]) }}"
                           class="small fw-semibold text-decoration-none text-truncate d-block">
                            {{ $file->original_name }}
                        </a>
                        <div class="text-muted" style="font-size:.72rem">
                            <span class="badge badge-soft-{{ $file->category_badge }}">{{ $file->category_label }}</span>
                            {{ $file->human_size }} · {{ $file->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('projects.files.download', [$project, $file]) }}"
                       class="btn btn-light btn-sm py-0" title="Download"><i class="bi bi-download"></i></a>
                    <form method="POST" action="{{ route('projects.files.destroy', [$project, $file]) }}"
                          hx-delete="{{ route('projects.files.destroy', [$project, $file]) }}"
                          hx-target="#project-body" hx-swap="outerHTML"
                          hx-confirm="Delete this file?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm py-0 text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted text-center py-3 mb-0 small">No files uploaded.</p>
        @endforelse
    </div>
</div>
