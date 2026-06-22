<div class="issue-row">
    <div>
        <a class="issue-title" href="{{ route('issues.show', $issue) }}">{{ $issue->title }}</a>
        <p>{{ Str::limit($issue->description, 120) }}</p>
    </div>
    <div class="issue-meta">
        <span class="status status-{{ $issue->status }}">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
        <span class="priority priority-{{ $issue->priority }}">{{ ucfirst($issue->priority) }}</span>
        <span>{{ optional($issue->due_date)->format('M j, Y') ?? 'No due date' }}</span>
    </div>
</div>
