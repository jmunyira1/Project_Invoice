{{-- Shared by create/edit pages and the create/edit modal --}}
@php $project ??= null; $selectedClientId ??= null; @endphp
<div class="row g-3">

    <div class="col-md-8">
        <label class="form-label" for="title">Project Title <span class="text-danger">*</span></label>
        <input type="text"
               id="title"
               name="title"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $project?->title ?? '') }}"
               placeholder="e.g. Brand Identity Design"
               required autofocus>
        @error('title')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status"
                class="form-select @error('status') is-invalid @enderror">
            @foreach(['draft','quoted','active','completed','cancelled'] as $s)
                <option value="{{ $s }}"
                    {{ old('status', $project?->status ?? 'draft') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
        @error('status')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="form-label" for="client_id">Client <span class="text-danger">*</span></label>
        <select id="client_id" name="client_id"
                class="form-select @error('client_id') is-invalid @enderror" required>
            <option value="">— Select client —</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}"
                    {{ old('client_id', $project?->client_id ?? $selectedClientId ?? '') == $client->id ? 'selected' : '' }}>
                    {{ $client->name }}
                </option>
            @endforeach
        </select>
        @error('client_id')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="due_date">Due Date</label>
        <input type="date"
               id="due_date"
               name="due_date"
               class="form-control @error('due_date') is-invalid @enderror"
               value="{{ old('due_date', $project?->due_date?->format('Y-m-d') ?? '') }}">
        @error('due_date')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="value">
            Project Value
            <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip"
               title="Optional agreed/contract value. Leave blank to use the sum of deliverables."></i>
        </label>
        <div class="input-group">
            <span class="input-group-text">{{ auth()->user()->organisation->currency }}</span>
            <input type="number"
                   id="value"
                   name="value"
                   class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value', $project?->value ?? '') }}"
                   min="0" step="0.01"
                   placeholder="e.g. 150000">
            @error('value')
            <div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea id="description"
                  name="description"
                  class="form-control @error('description') is-invalid @enderror"
                  rows="4"
                  placeholder="Scope of work, notes, context...">{{ old('description', $project?->description ?? '') }}</textarea>
        @error('description')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>
