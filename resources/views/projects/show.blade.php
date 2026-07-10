<x-app-layout>
    <x-slot name="title">{{ $project->title }}</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
        <li class="breadcrumb-item active">{{ $project->title }}</li>
    </x-slot>

    @include('projects.partials._body')
</x-app-layout>
