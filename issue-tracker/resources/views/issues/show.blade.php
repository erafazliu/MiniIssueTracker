@extends('layouts.app')

@section('title', $issue->title)

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Issue</p>
        <h1>{{ $issue->title }}</h1>
    </div>
    <div class="actions">
        @can('update', $issue->project)
            <a class="button secondary" href="{{ route('issues.edit', $issue) }}">Edit</a>
        @endcan
        @can('update', $issue->project)
            <form method="POST" action="{{ route('issues.destroy', $issue) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="button link-button danger">Delete</button>
            </form>
        @endcan
    </div>
</div>

<div class="card">
    <p>{{ $issue->description }}</p>
    <div class="stats">
        <div>
            <span>Project</span>
            <strong>{{ $issue->project->name }}</strong>
        </div>
        <div>
            <span>Status</span>
            <strong>{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</strong>
        </div>
        <div>
            <span>Priority</span>
            <strong>{{ ucfirst($issue->priority) }}</strong>
        </div>
        <div>
            <span>Due date</span>
            <strong>{{ optional($issue->due_date)->format('M j, Y') ?? 'No due date' }}</strong>
        </div>
    </div>
</div>
@endsection
