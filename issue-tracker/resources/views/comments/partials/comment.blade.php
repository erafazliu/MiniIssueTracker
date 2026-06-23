<div class="comment-card" style="padding: 20px 28px; border-bottom: 1px solid var(--line);">
    <div class="comment-header" style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
        <strong>{{ $comment->user->name }}</strong>
        <span style="color: var(--muted); font-size: 13px;">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <p style="margin: 0; color: #344054; line-height: 1.6;">{{ $comment->body }}</p>
</div>
