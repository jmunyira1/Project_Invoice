<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-file-earmark-plus me-2"></i>Generate Document</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form hx-post="{{ route('documents.store') }}" hx-target="#app-modal-content" hx-swap="innerHTML"
      hx-disabled-elt="find button[type=submit]">
    <div class="modal-body">
        @include('documents._form')
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="htmx-indicator spinner-border spinner-border-sm me-1"></span>
            Generate document
        </button>
    </div>
</form>
