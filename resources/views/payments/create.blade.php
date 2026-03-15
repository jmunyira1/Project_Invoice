<x-app-layout>
    <x-slot name="title">Record Payment</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
        <li class="breadcrumb-item active">Record Payment</li>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">
            <div class="card">
                <div class="card-header pb-0"><h5>Record Payment</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf
                        <div class="row g-3">

                            {{-- Project --}}
                            <div class="col-12">
                                <label class="form-label">Project <span class="text-danger">*</span></label>
                                <select name="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror"
                                        id="projectSelect"
                                        required>
                                    <option value="">— Select project —</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                                            {{ $project->title }} ({{ $project->client->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Link to document (optional) --}}
                            @if($documents->isNotEmpty())
                                <div class="col-12">
                                    <label class="form-label">Link to Document (optional)</label>
                                    <select name="document_id"
                                            class="form-select @error('document_id') is-invalid @enderror">
                                        <option value="">— No specific document —</option>
                                        @foreach($documents as $doc)
                                            <option value="{{ $doc->id }}"
                                                {{ old('document_id', request('document_id')) == $doc->id ? 'selected' : '' }}>
                                                {{ $doc->number }} — {{ $doc->type_label }}
                                                ({{ $currency }} {{ number_format($doc->balance, 2) }} outstanding)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('document_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="document_id"
                                       value="{{ old('document_id', request('document_id')) }}">
                            @endif

                            {{-- Amount --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Amount ({{ $currency }}) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       name="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount') }}"
                                       min="0.01" step="0.01"
                                       placeholder="0.00"
                                       required>
                                @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Payment date --}}
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="paid_on"
                                       class="form-control @error('paid_on') is-invalid @enderror"
                                       value="{{ old('paid_on', now()->format('Y-m-d')) }}"
                                       required>
                                @error('paid_on')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Method --}}
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="method"
                                        class="form-select @error('method') is-invalid @enderror"
                                        id="methodSelect"
                                        required>
                                    <option value="">— Select method —</option>
                                    @foreach([
                                        'mpesa'  => 'M-Pesa',
                                        'cash'   => 'Cash',
                                        'bank'   => 'Bank Transfer',
                                        'cheque' => 'Cheque',
                                        'card'   => 'Card',
                                    ] as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('method') === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Reference --}}
                            <div class="col-md-6">
                                <label class="form-label" id="refLabel">
                                    Reference
                                </label>
                                <input type="text"
                                       name="reference"
                                       id="referenceInput"
                                       class="form-control @error('reference') is-invalid @enderror"
                                       value="{{ old('reference') }}"
                                       placeholder="e.g. M-Pesa code, cheque number">
                                @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          rows="2"
                                          placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-success">Save Payment</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update the reference label based on payment method
        document.getElementById('methodSelect')?.addEventListener('change', function () {
            const labels = {
                mpesa: 'M-Pesa Transaction Code',
                cheque: 'Cheque Number',
                bank: 'Transaction Reference',
                card: 'Transaction Reference',
                cash: 'Reference (optional)',
            };
            document.getElementById('refLabel').textContent = labels[this.value] || 'Reference';
        });
    </script>

</x-app-layout>
