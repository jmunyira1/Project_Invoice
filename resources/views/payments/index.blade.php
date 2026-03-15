<x-app-layout>
    <x-slot name="title">Payments</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Payments</li>
    </x-slot>

    {{-- This month summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card mb-0">
                <div class="card-body py-3">
                    <p class="text-muted f-13 mb-1">Received This Month</p>
                    <h5 class="mb-0 text-success">
                        {{ $currency }} {{ number_format($totalThisMonth, 2) }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between pb-0">
            <h5>All Payments</h5>
            <a href="{{ route('payments.create') }}" class="btn btn-success btn-sm">
                <i data-feather="plus" style="width:14px;height:14px" class="me-1"></i>
                Record Payment
            </a>
        </div>
        <div class="card-body p-0">
            @if($payments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i data-feather="credit-card" style="width:40px;height:40px;opacity:.3"
                       class="d-block mx-auto mb-3"></i>
                    <p class="mb-0">No payments recorded yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Project / Client</th>
                            <th>Document</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-end">Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $pay)
                            <tr>
                                <td class="text-muted f-13">
                                    {{ $pay->paid_on->format('d M Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $pay->project) }}"
                                       class="fw-semibold text-dark d-block f-13">
                                        {{ $pay->project->title }}
                                    </a>
                                    <small class="text-muted">{{ $pay->project->client->name }}</small>
                                </td>
                                <td>
                                    @if($pay->document)
                                        <a href="{{ route('documents.show', $pay->document) }}"
                                           class="text-muted f-13">
                                            {{ $pay->document->number }}
                                        </a>
                                    @else
                                        <span class="text-muted f-13">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light-secondary">
                                        {{ $pay->method_label }}
                                    </span>
                                </td>
                                <td class="text-muted f-13">{{ $pay->reference ?? '—' }}</td>
                                <td class="text-end fw-semibold">
                                    {{ $currency }} {{ number_format($pay->amount, 2) }}
                                </td>
                                <td class="text-end">
                                    <form method="POST"
                                          action="{{ route('payments.destroy', $pay) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this payment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-light btn-sm text-danger"
                                                title="Delete">
                                            <i data-feather="trash-2" style="width:13px;height:13px"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                    <div class="p-3 border-top">{{ $payments->links() }}</div>
                @endif
            @endif
        </div>
    </div>

</x-app-layout>
