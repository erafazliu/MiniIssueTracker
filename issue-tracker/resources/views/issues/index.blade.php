@extends('layouts.app')

@section('title', 'Issues')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Issues</p>
        <h1>Issues</h1>
    </div>
    <div class="actions">
        <a class="button primary" href="{{ route('issues.create') }}">New issue</a>
    </div>
</div>

<form method="GET" action="{{ route('issues.index') }}" class="panel form-panel">
    <div class="form-grid">
        <label>
            Status
            <select name="status">
                <option value="">All statuses</option>
                @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Priority
            <select name="priority">
                <option value="">All priorities</option>
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                    <option value="{{ $value }}" {{ request('priority') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <div class="form-actions" style="align-items:flex-end;">
            <button type="submit" class="button primary">Filter</button>
            <a class="button secondary" href="{{ route('issues.index') }}">Reset</a>
        </div>
    </div>
</form>

@if($issues->isEmpty())
    <div class="empty">
        <p>No issues yet. Create one to get started.</p>
    </div>
@else
    <div class="issues-list">
        @foreach($issues as $issue)
            @include('issues.partials.row', ['issue' => $issue])
        @endforeach
    </div>

    {{ $issues->links() }}
@endif
@endsection
