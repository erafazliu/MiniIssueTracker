@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Project</p>
        <h1>{{ $project->name }}</h1>
    </div>
    <div class="actions">
        @can('update', $project)
            <a class="button secondary" href="{{ route('projects.edit', $project) }}">Edit</a>
        @endcan
        @can('delete', $project)
            <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="button link-button danger">Delete</button>
            </form>
        @endcan
    </div>
</div>

<div class="card">
    <p>{{ $project->description }}</p>
    <div class="stats">
        <div>
            <span>Start date</span>
            <strong>{{ optional($project->start_date)->format('M j, Y') ?? 'Not set' }}</strong>
        </div>
        <div>
            <span>Deadline</span>
            <strong>{{ optional($project->deadline)->format('M j, Y') ?? 'Not set' }}</strong>
        </div>
        <div>
            <span>Owner</span>
            <strong>{{ $project->owner->name }}</strong>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h2>Issues</h2>
        @can('update', $project)
            <a class="button primary" href="{{ route('issues.create', ['project_id' => $project->id]) }}">New issue</a>
        @endcan
    </div>

    @if($project->issues->isEmpty())
        <div class="empty">
            <p>No issues on this project yet.</p>
        </div>
    @else
        <div class="issues-list">
            @foreach($project->issues as $issue)
                @include('issues.partials.row', ['issue' => $issue])
            @endforeach
        </div>
    @endif
</div>
@endsection
