@extends('layouts.app')

@section('title', $issue->title)

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Issue</p>
        <h1>{{ $issue->title }}</h1>
    </div>
    <div class="actions">
        @can('update', $issue->project)
            <a class="button secondary" href="{{ route('issues.edit', $issue) }}">Edit</a>
        @endcan
        @can('update', $issue->project)
            <form method="POST" action="{{ route('issues.destroy', $issue) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="button link-button danger">Delete</button>
            </form>
        @endcan
    </div>
</div>

<div class="card">
    <p>{{ $issue->description }}</p>
    <div class="stats">
        <div>
            <span>Project</span>
            <strong>{{ $issue->project->name }}</strong>
        </div>
        <div>
            <span>Status</span>
            <strong>{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</strong>
        </div>
        <div>
            <span>Priority</span>
            <strong>{{ ucfirst($issue->priority) }}</strong>
        </div>
        <div>
            <span>Due date</span>
            <strong>{{ optional($issue->due_date)->format('M j, Y') ?? 'No due date' }}</strong>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 28px;">
        <h2 style="margin: 0;">Tags</h2>
        @can('update', $issue->project)
            <button id="open-tag-modal" class="button primary" type="button">Manage tags</button>
        @endcan
    </div>

    <div id="tags-list" class="tag-list" style="padding: 20px 28px 0;">
        @forelse($issue->tags as $tag)
            <div class="tag-pill" data-tag-id="{{ $tag->id }}" style="padding: 12px 28px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line);\">
                <span>{{ $tag->name }}</span>
                @can('update', $issue->project)
                    <button class="tag-remove" data-issue-id="{{ $issue->id }}" data-tag-id="{{ $tag->id }}" style=\"background: none; border: none; color: var(--danger); cursor: pointer; font-size: 18px;\">×</button>
                @endcan
            </div>
        @empty
            <div class="empty" style="padding: 20px 28px; text-align: left;">
                <p style="margin: 0; color: var(--muted);\">No tags attached to this issue.</p>
            </div>
        @endforelse
    </div>
</div>

@can('update', $issue->project)
    <div id="tag-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <button id="close-tag-modal" class="button link-button">Close</button>
            <h3>Attach tags</h3>
            <div class="tag-actions">
                @foreach(App\Models\Tag::orderBy('name')->get() as $tag)
                    <div class="tag-pill tag-selectable {{ $issue->tags->contains($tag) ? 'selected' : '' }}" data-tag-id="{{ $tag->id }}">
                        {{ $tag->name }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endcan

<div class="panel">
    <div class="panel-heading" style="padding: 20px 28px;">
        <h2 style="margin: 0;">Comments</h2>
    </div>

    <div id="comments-error" class="form-errors" style="display:none; margin: 20px 28px 1rem;"></div>

    <div id="comments-list" style="padding: 0 28px;">
        @php
            $comments = $issue->comments()->with('user')->latest()->paginate(5);
        @endphp
        @include('comments.partials.list', ['comments' => $comments])
    </div>

    <form id="comment-form" class="panel form-panel" method="POST" action="{{ route('issues.comments.store', $issue) }}" style="margin-top: 1rem;">
        @csrf
        <textarea name="body" id="comment-body" rows="4" placeholder="Write a comment..." required></textarea>
        <button class="button primary" type="submit">Post comment</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openModal = document.getElementById('open-tag-modal');
        const closeModal = document.getElementById('close-tag-modal');
        const modal = document.getElementById('tag-modal');
        const tagsList = document.getElementById('tags-list');
        const issueId = {{ $issue->id }};
        const commentsList = document.getElementById('comments-list');
        const commentsError = document.getElementById('comments-error');
        const commentForm = document.getElementById('comment-form');
        const commentBody = document.getElementById('comment-body');
        const commentsEndpoint = '{{ route('issues.comments.index', $issue) }}';
        const commentsStoreEndpoint = '{{ route('issues.comments.store', $issue) }}';

        if (openModal && modal) {
            openModal.addEventListener('click', () => modal.style.display = 'block');
        }

        if (closeModal && modal) {
            closeModal.addEventListener('click', () => modal.style.display = 'none');
        }

        function createTagPill(tagId, tagName) {
            const pill = document.createElement('div');
            pill.className = 'tag-pill';
            pill.dataset.tagId = tagId;

            const label = document.createElement('span');
            label.textContent = tagName;
            pill.appendChild(label);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'tag-remove';
            removeButton.dataset.issueId = issueId;
            removeButton.dataset.tagId = tagId;
            removeButton.textContent = '×';
            pill.appendChild(removeButton);

            return pill;
        }

        function updateEmptyState() {
            const empty = tagsList.querySelector('.empty');
            if (tagsList.querySelectorAll('.tag-pill').length === 0) {
                if (!empty) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.className = 'empty';
                    emptyMessage.innerHTML = '<p>No tags attached to this issue.</p>';
                    tagsList.appendChild(emptyMessage);
                }
            } else if (empty) {
                empty.remove();
            }
        }

        async function loadComments(url = commentsEndpoint) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            commentsList.innerHTML = data.html;
        }

        if (commentForm) {
            commentForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                commentsError.style.display = 'none';
                commentsError.innerHTML = '';

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(commentsStoreEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ body: commentBody.value }),
                });

                if (response.status === 422) {
                    const data = await response.json();
                    const messages = Object.values(data.errors || {}).flat();
                    commentsError.innerHTML = messages.map(msg => `<p>${msg}</p>`).join('');
                    commentsError.style.display = 'block';
                    return;
                }

                if (response.ok) {
                    commentBody.value = '';
                    await loadComments();
                }
            });
        }

        commentsList?.addEventListener('click', async function (event) {
            const link = event.target.closest('.comments-pagination a');
            if (!link) {
                return;
            }

            event.preventDefault();
            await loadComments(link.href);
        });

        tagsList?.addEventListener('click', async function (event) {
            const button = event.target.closest('.tag-remove');
            if (!button) {
                return;
            }

            const tagId = button.dataset.tagId;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(`/issues/${issueId}/tags/${tagId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                button.closest('.tag-pill').remove();
                updateEmptyState();
                const selectable = document.querySelector(`.tag-selectable[data-tag-id="${tagId}"]`);
                selectable?.classList.remove('selected');
            }
        });

        document.querySelector('.tag-actions')?.addEventListener('click', async function (event) {
            const tagElement = event.target.closest('.tag-selectable');
            if (!tagElement) {
                return;
            }

            const tagId = tagElement.dataset.tagId;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const isSelected = tagElement.classList.contains('selected');
            const url = `/issues/${issueId}/tags/${tagId}`;
            const method = isSelected ? 'DELETE' : 'POST';
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            tagElement.classList.toggle('selected');
            const tagName = tagElement.textContent.trim();
            const existing = tagsList.querySelector(`.tag-pill[data-tag-id="${tagId}"]`);

            if (!isSelected && !existing) {
                if (tagsList.querySelector('.empty')) {
                    tagsList.innerHTML = '';
                }
                tagsList.appendChild(createTagPill(tagId, tagName));
            } else if (isSelected && existing) {
                existing.remove();
                updateEmptyState();
            }
        });
    });
</script>
@endsection
