@if($projects->isEmpty())
    <div class="empty">
        <p>No projects match the current search.</p>
    </div>
@else
    <div class="card-grid">
        @foreach($projects as $project)
            <div class="card project-card">
                <h2><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></h2>
                <p>{{ $project->description }}</p>
                <div class="project-meta">
                    <span>{{ optional($project->start_date)->format('M j, Y') ?? 'No start date' }}</span>
                    <span>{{ optional($project->deadline)->format('M j, Y') ?? 'No deadline' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{ $projects->links() }}
@endif
