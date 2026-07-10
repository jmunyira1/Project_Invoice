{{-- Project details --}}
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Project Details</h6>
    </div>
    <div class="card-body">
        @if($project->description)
            <p class="text-muted small" style="white-space:pre-line">{{ $project->description }}</p>
            <hr>
        @endif
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted small">Client</span>
            <a href="{{ route('clients.show', $project->client) }}" class="small fw-semibold text-decoration-none">
                {{ $project->client->name }}
            </a>
        </div>
        @if($project->value !== null)
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Agreed Value</span>
                <span class="small fw-semibold">{{ $currency }} {{ number_format($project->value, 2) }}</span>
            </div>
        @endif
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted small">Created</span>
            <span class="small">{{ $project->created_at->format('d M Y') }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted small">Due Date</span>
            <span class="small">{{ $project->due_date?->format('d M Y') ?? '—' }}</span>
        </div>
    </div>
</div>
