<x-app-layout>
    <x-slot name="title">New Project</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
        <li class="breadcrumb-item active">New Project</li>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card">
                <div class="card-header pb-0"><h5>Create New Project</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf
                        @include('projects._form')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Create Project</button>
                            <a href="{{ route('projects.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
