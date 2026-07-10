{{-- Shared by documents/create page and the create modal --}}
@php $selectedProjectId ??= null; $defaultTemplateId ??= null; @endphp
<div class="row g-3">

    {{-- Project --}}
    <div class="col-md-8">
        <label class="form-label">Project <span class="text-danger">*</span></label>
        <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
            <option value="">— Select project —</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}"
                    {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                    {{ $project->title }} ({{ $project->client->name }})
                </option>
            @endforeach
        </select>
        @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Type --}}
    <div class="col-md-4">
        <label class="form-label">Document Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">— Select type —</option>
            @foreach([
                'quote'         => 'Quote',
                'invoice'       => 'Invoice',
                'receipt'       => 'Receipt',
                'delivery_note' => 'Delivery Note',
                'statement'     => 'Statement',
            ] as $val => $label)
                <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Template --}}
    <div class="col-md-6">
        <label class="form-label">Template <span class="text-danger">*</span></label>
        <select name="template_id" class="form-select @error('template_id') is-invalid @enderror" required>
            @foreach($templates as $template)
                <option value="{{ $template->id }}"
                    {{ old('template_id', $defaultTemplateId) == $template->id ? 'selected' : '' }}>
                    {{ $template->name }}
                </option>
            @endforeach
        </select>
        @error('template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Issue date --}}
    <div class="col-md-3">
        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
        <input type="date" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror"
               value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
        @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Due date --}}
    <div class="col-md-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
               value="{{ old('due_date') }}">
        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                  placeholder="Payment terms, thank you message, special instructions...">{{ old('notes') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>
<p class="text-muted small mt-2 mb-0">
    <i class="bi bi-info-circle me-1"></i>Deliverables are automatically copied from the selected project.
</p>
