<x-app-layout>
    <x-slot name="title">Invoice Templates</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Templates</li>
    </x-slot>

    <div class="row g-4">
        @foreach($templates as $template)
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 {{ $template->id === $defaultId ? 'border-2 border-primary' : '' }}">

                    {{-- PDF thumbnail --}}
                    <div class="position-relative overflow-hidden bg-light"
                         style="height:240px; border-bottom:1px solid #eee;">

                        {{-- Scaled iframe preview --}}
                        <iframe src="{{ route('templates.preview', $template) }}"
                                style="width:167%; height:167%; border:none;
                                   transform:scale(0.6); transform-origin:top left;
                                   pointer-events:none; display:block;"
                                loading="lazy"
                                title="{{ $template->name }}">
                        </iframe>

                        {{-- Click overlay — opens full preview in new tab --}}
                        <a href="{{ route('templates.preview', $template) }}"
                           target="_blank"
                           class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-decoration-none preview-overlay"
                           title="Preview {{ $template->name }}">
                        <span class="badge bg-dark bg-opacity-75 px-3 py-2 preview-badge"
                              style="opacity:0; transition:opacity .15s; font-size:12px;">
                            <i data-feather="external-link" style="width:12px;height:12px" class="me-1"></i>
                            Open preview
                        </span>
                        </a>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h6 class="card-title mb-0 fw-semibold">{{ $template->name }}</h6>
                            @if($template->id === $defaultId)
                                <span class="badge badge-light-primary">Default</span>
                            @endif
                        </div>

                        <p class="text-muted f-13 flex-grow-1 mb-3">
                            {{ $template->description }}
                        </p>

                        <div class="d-flex gap-2">
                            {{-- Preview in new tab --}}
                            <a href="{{ route('templates.preview', $template) }}"
                               target="_blank"
                               class="btn btn-light btn-sm">
                                <i data-feather="eye" style="width:13px;height:13px" class="me-1"></i>
                                Preview
                            </a>

                            {{-- Set as default --}}
                            @if($template->id === $defaultId)
                                <button class="btn btn-primary btn-sm w-100" disabled>
                                    <i data-feather="check" style="width:13px;height:13px" class="me-1"></i>
                                    Selected
                                </button>
                            @else
                                <form method="POST"
                                      action="{{ route('templates.setDefault', $template) }}"
                                      class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        Use this
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

</x-app-layout>

@push('styles')
    <style>
        .preview-overlay:hover .preview-badge {
            opacity: 1 !important;
        }

        .preview-overlay {
            background: transparent;
            transition: background .15s;
        }

        .preview-overlay:hover {
            background: rgba(0, 0, 0, 0.12);
        }
    </style>
@endpush
