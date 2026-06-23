<div class="comment-card">
    <div class="comment-header">
        <strong>{{ $comment->user->name }}</strong>
        <span>{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <p>{{ $comment->body }}</p>
</div>
