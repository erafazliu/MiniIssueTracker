@if($comments->isEmpty())
    <div class="panel">
        <p>No comments yet.</p>
    </div>
@else
    <div class="comment-list">
        @foreach($comments as $comment)
            @include('comments.partials.comment', ['comment' => $comment])
        @endforeach
    </div>

    <div class="comments-pagination">
        {{ $comments->links() }}
    </div>
@endif
