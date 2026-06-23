@extends('layouts.app')

@section('title', 'Issues')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Issues</p>
        <h1>Issues</h1>
    </div>
    @can('create', App\Models\Project::class)
        <div class="actions">
            <a class="button primary" href="{{ route('issues.create') }}">New issue</a>
        </div>
    @endcan
</div>

<form method="GET" action="{{ route('issues.index') }}" class="panel form-panel" id="issues-filter-form">
    <div class="form-grid">
        <label class="full">
            Search
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by title or description"
                autocomplete="off"
            >
        </label>
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
            <button type="button" class="button secondary" id="issues-reset-button">Reset</button>
        </div>
    </div>
</form>

<div id="issues-results">
    @include('issues.partials.results', ['issues' => $issues])
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('issues-filter-form');
        const results = document.getElementById('issues-results');
        const searchInput = form?.querySelector('input[name="search"]');
        const resetButton = document.getElementById('issues-reset-button');
        let debounceTimer;

        if (!form || !results) {
            return;
        }

        async function updateResults() {
            const params = new URLSearchParams(new FormData(form));
            const url = `${form.action}?${params.toString()}`;

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            results.innerHTML = await response.text();
            history.replaceState({}, '', url);
        }

        searchInput?.addEventListener('input', function () {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(updateResults, 300);
        });

        form.querySelectorAll('select').forEach(function (select) {
            select.addEventListener('change', updateResults);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            updateResults();
        });

        resetButton?.addEventListener('click', function () {
            form.reset();
            updateResults();
        });

        results.addEventListener('click', async function (event) {
            const link = event.target.closest('.pagination a');
            if (!link) {
                return;
            }

            event.preventDefault();

            const response = await fetch(link.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            results.innerHTML = await response.text();
            history.replaceState({}, '', link.href);
        });
    });
</script>
@endsection
