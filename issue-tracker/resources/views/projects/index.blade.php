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

<form method="GET" action="{{ route('projects.index') }}" class="panel form-panel" id="projects-filter-form">
    <div class="form-grid">
        <label class="full">
            Search
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by name or description"
                autocomplete="off"
            >
        </label>
        <div class="form-actions" style="align-items:flex-end;">
            <button type="submit" class="button primary">Search</button>
            <button type="button" class="button secondary" id="projects-reset-button">Reset</button>
        </div>
    </div>
</form>

<div id="projects-results">
    @include('projects.partials.results', ['projects' => $projects])
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('projects-filter-form');
        const results = document.getElementById('projects-results');
        const searchInput = form?.querySelector('input[name="search"]');
        const resetButton = document.getElementById('projects-reset-button');
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
