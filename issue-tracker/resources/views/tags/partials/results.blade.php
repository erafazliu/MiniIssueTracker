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
                    @can('create', App\Models\Project::class)
                        <form method="POST" action="{{ route('tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button link-button danger">Delete</button>
                        </form>
                    @endcan
                </div>
                <p>Color: <span style="display:inline-block;width:14px;height:14px;background:{{ $tag->color ?? '#ccc' }};border:1px solid #333;margin-left:0.5rem;vertical-align:middle;"></span></p>
            </div>
        @endforeach
    </div>

    {{ $tags->links() }}
@endif
