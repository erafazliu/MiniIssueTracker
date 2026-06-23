@if($issues->isEmpty())
    <div class="empty">
        <p>No issues match the current filters.</p>
    </div>
@else
    <div class="issues-list">
        @foreach($issues as $issue)
            @include('issues.partials.row', ['issue' => $issue])
        @endforeach
    </div>

    {{ $issues->links() }}
@endif
