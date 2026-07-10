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
                        @include('documents._form')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Create Document</button>
                            <a href="{{ route('documents.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
