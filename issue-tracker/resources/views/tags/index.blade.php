@extends('layouts.app')

@section('title', 'Tags')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Tags</p>
        <h1>Tags</h1>
    </div>
</div>

<form method="GET" action="{{ route('tags.index') }}" class="panel form-panel" id="tags-filter-form">
    <div class="form-grid">
        <label class="full">
            Search
            <input name="q" value="{{ request('q') }}" placeholder="Search tags" autocomplete="off">
        </label>
        <div class="form-actions" style="align-items:flex-end;">
            <button class="button primary" type="submit">Search</button>
            <button class="button secondary" type="button" id="tags-reset-button">Reset</button>
        </div>
    </div>
</form>

@can('create', App\Models\Project::class)
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
@endcan

<div id="tags-results">
    @include('tags.partials.results', ['tags' => $tags])
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('tags-filter-form');
        const results = document.getElementById('tags-results');
        const searchInput = form?.querySelector('input[name="q"]');
        const resetButton = document.getElementById('tags-reset-button');
        let debounceTimer;

        if (!form || !results) {
            return;
        }

        async function updateResults(url = null) {
            const targetUrl = url ?? `${form.action}?${new URLSearchParams(new FormData(form)).toString()}`;
            const response = await fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            results.innerHTML = await response.text();
            history.replaceState({}, '', targetUrl);
        }

        searchInput?.addEventListener('input', function () {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(function () {
                updateResults();
            }, 300);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            updateResults();
        });

        resetButton?.addEventListener('click', function () {
            form.reset();
            updateResults();
        });

        results.addEventListener('click', function (event) {
            const link = event.target.closest('.pagination a');
            if (!link) {
                return;
            }

            event.preventDefault();
            updateResults(link.href);
        });
    });
</script>
@endsection
