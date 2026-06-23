@if($comments->isEmpty())
    <div style="padding: 20px 28px;">
        <p style="color: var(--muted); margin: 0;">No comments yet. Start the conversation!</p>
    </div>
@else
    <div class="comment-list">
        @foreach($comments as $comment)
            @include('comments.partials.comment', ['comment' => $comment])
        @endforeach
    </div>

    <div class="comments-pagination" style="padding: 16px 28px;">
        {{ $comments->links() }}
    </div>
@endif
