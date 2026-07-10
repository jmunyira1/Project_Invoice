@php $project ??= null; $editing = (bool) $project; @endphp
<div class="modal-header">
    <h5 class="modal-title">
        <i class="bi bi-kanban me-2"></i>{{ $editing ? 'Edit Project' : 'New Project' }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form @if($editing) hx-patch="{{ route('projects.update', $project) }}" @else hx-post="{{ route('projects.store') }}" @endif
      hx-target="#app-modal-content" hx-swap="innerHTML" hx-disabled-elt="find button[type=submit]">
    <div class="modal-body">
        @include('projects._form')
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="htmx-indicator spinner-border spinner-border-sm me-1"></span>
            {{ $editing ? 'Save changes' : 'Create project' }}
        </button>
    </div>
</form>
