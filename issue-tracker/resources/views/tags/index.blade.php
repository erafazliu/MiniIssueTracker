@extends('layouts.app')

@section('title', 'Tags')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Tags</p>
        <h1>Tags</h1>
    </div>
</div>

<form method="GET" action="{{ route('tags.index') }}" class="panel form-panel">
    <div class="form-grid">
        <label class="full">
            Search
            <input name="q" value="{{ request('q') }}" placeholder="Search tags">
        </label>
        <div class="form-actions" style="align-items:flex-end;">
            <button class="button primary">Search</button>
            <a class="button secondary" href="{{ route('tags.index') }}">Reset</a>
        </div>
    </div>
</form>

<form method="POST" action="{{ route('tags.store') }}" class="panel form-panel">
    @csrf
    @include('partials.errors')
    <div class="form-grid">
        <label class="full">
            Name
            <input name="name" value="{{ old('name') }}" required>
        </label>
        <label>
            Color
            <input name="color" type="color" value="{{ old('color', '#000000') }}">
        </label>
    </div>
    <div class="form-actions">
        <button class="button primary">Create tag</button>
    </div>
</form>

@if($tags->isEmpty())
    <div class="empty">
        <p>No tags found.</p>
    </div>
@else
    <div class="card-grid">
        @foreach($tags as $tag)
            <div class="card tag-card">
                <div class="tag-card-header">
                    <h2>{{ $tag->name }}</h2>
                    <form method="POST" action="{{ route('tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button link-button danger">Delete</button>
                    </form>
                </div>
                <p>Color: <span style="display:inline-block;width:14px;height:14px;background:{{ $tag->color ?? '#ccc' }};border:1px solid #333;margin-left:0.5rem;vertical-align:middle;"></span></p>
            </div>
        @endforeach
    </div>

    {{ $tags->links() }}
@endif
@endsection
