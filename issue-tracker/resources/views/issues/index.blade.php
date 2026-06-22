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
