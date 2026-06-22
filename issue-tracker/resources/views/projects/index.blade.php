@extends('layouts.app')

@section('title','Projects')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Projects</p>
        <h1>Your projects</h1>
    </div>
    <div class="actions">
        <a class="button primary" href="{{ route('projects.create') }}">New project</a>
    </div>
</div>

@if($projects->isEmpty())
    <div class="empty">
        <p>No projects yet. Create one to get started.</p>
    </div>
@else
    <div class="card-grid">
        @foreach($projects as $project)
            <div class="card project-card">
                <h2><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></h2>
                <p>{{ $project->description }}</p>
                <div class="project-meta">
                    <span>{{ optional($project->start_date)->format('M j, Y') ?? 'No start date' }}</span>
                    <span>{{ optional($project->deadline)->format('M j, Y') ?? 'No deadline' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{ $projects->links() }}
@endif
@endsection
