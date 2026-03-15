<x-app-layout>
    <x-slot name="title">New Document</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
        <li class="breadcrumb-item active">New Document</li>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Create Document</h5>
                    <p class="text-muted f-13 mb-0">
                        Deliverables will be automatically copied from the selected project.
                    </p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('documents.store') }}">
                        @csrf
                        <div class="row g-3">

                            {{-- Project --}}
                            <div class="col-md-8">
                                <label class="form-label">Project <span class="text-danger">*</span></label>
                                <select name="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror"
                                        required>
                                    <option value="">— Select project —</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                                            {{ $project->title }}
                                            ({{ $project->client->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Type --}}
                            <div class="col-md-4">
                                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                <select name="type"
                                        class="form-select @error('type') is-invalid @enderror"
                                        required>
                                    <option value="">— Select type —</option>
                                    @foreach([
                                        'quote'         => 'Quote',
                                        'invoice'       => 'Invoice',
                                        'receipt'       => 'Receipt',
                                        'delivery_note' => 'Delivery Note',
                                        'statement'     => 'Statement',
                                    ] as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('type') === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Template --}}
                            <div class="col-md-6">
                                <label class="form-label">Template <span class="text-danger">*</span></label>
                                <select name="template_id"
                                        class="form-select @error('template_id') is-invalid @enderror"
                                        required>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}"
                                            {{ old('template_id', $defaultTemplateId) == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Issue date --}}
                            <div class="col-md-3">
                                <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="issue_date"
                                       class="form-control @error('issue_date') is-invalid @enderror"
                                       value="{{ old('issue_date', now()->format('Y-m-d')) }}"
                                       required>
                                @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Due date --}}
                            <div class="col-md-3">
                                <label class="form-label">Due Date</label>
                                <input type="date"
                                       name="due_date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       value="{{ old('due_date') }}">
                                @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Payment terms, thank you message, special instructions...">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Create Document
                            </button>
                            <a href="{{ route('documents.index') }}" class="btn btn-light">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
