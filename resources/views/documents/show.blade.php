<x-app-layout>
    <x-slot name="title">{{ $document->number }}</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
        <li class="breadcrumb-item active">{{ $document->number }}</li>
    </x-slot>

    {{-- Action bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="d-flex align-items-center gap-2">
            <i data-feather="{{ $document->type_icon }}" style="width:18px;height:18px" class="text-muted"></i>
            <h5 class="mb-0">{{ $document->type_label }}: {{ $document->number }}</h5>
            <span class="badge badge-light-{{ $document->status_badge }}">
                {{ $document->status_label }}
            </span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if(!$document->sent_at)
                <form method="POST" action="{{ route('documents.markSent', $document) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i data-feather="send" style="width:13px;height:13px" class="me-1"></i>
                        Mark as Sent
                    </button>
                </form>
            @endif
            <a href="{{ route('documents.pdf', $document) }}"
               target="_blank"
               class="btn btn-light btn-sm">
                <i data-feather="external-link" style="width:13px;height:13px" class="me-1"></i>
                Open in new tab
            </a>
            <a href="{{ route('documents.pdf', $document) }}?download=1"
               class="btn btn-primary btn-sm">
                <i data-feather="download" style="width:13px;height:13px" class="me-1"></i>
                Download
            </a>
            <a href="{{ route('projects.show', $document->project) }}"
               class="btn btn-light btn-sm">
                <i data-feather="arrow-left" style="width:13px;height:13px" class="me-1"></i>
                Back to Project
            </a>
            <form method="POST"
                  action="{{ route('documents.destroy', $document) }}"
                  onsubmit="return confirm('Delete this document?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-light btn-sm text-danger">
                    <i data-feather="trash-2" style="width:13px;height:13px"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- PDF embed --}}
    <div class="card p-0" style="overflow:hidden">
        <iframe src="{{ route('documents.pdf', $document) }}"
                style="width:100%; height:82vh; border:none; display:block;"
                title="{{ $document->type_label }} {{ $document->number }}">
            <p class="p-3 text-muted">
                Your browser cannot display the PDF inline.
                <a href="{{ route('documents.pdf', $document) }}" target="_blank">Click here to open it.</a>
            </p>
        </iframe>
    </div>

</x-app-layout>
