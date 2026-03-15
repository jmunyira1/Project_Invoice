<x-app-layout>
    <x-slot name="title">Edit Client</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.index') }}">Clients</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a>
        </li>
        <li class="breadcrumb-item active">Edit</li>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Edit Client</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('clients.update', $client) }}">
                        @csrf
                        @method('PATCH')
                        @include('clients._form')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-light">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
