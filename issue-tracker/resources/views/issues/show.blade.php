@extends('layouts.app')

@section('title', $issue->title)

@section('content')
@php
    $allUsers = App\Models\User::orderBy('name')->get();
    $allTags = App\Models\Tag::orderBy('name')->get();
@endphp

<div class="page-heading">
    <div>
        <p class="eyebrow">Issue</p>
        <h1>{{ $issue->title }}</h1>
    </div>
    <div class="actions">
        @can('update', $issue->project)
            <a class="button secondary" href="{{ route('issues.edit', $issue) }}">Edit</a>
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
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;padding:20px 28px;">
        <h2 style="margin:0;">Members</h2>
        @can('update', $issue->project)
            <button id="open-member-modal" class="button primary" type="button">Manage members</button>
        @endcan
    </div>

    <div id="members-list" class="tag-list" style="padding:20px 28px 0;">
        @forelse($issue->members as $member)
            <div class="list-pill-row member-row" data-user-id="{{ $member->id }}">
                <span>{{ $member->name }}</span>
                @can('update', $issue->project)
                    <button class="tag-remove member-remove" data-user-id="{{ $member->id }}" type="button">×</button>
                @endcan
            </div>
        @empty
            <div class="empty member-empty" style="padding:20px 28px;text-align:left;">
                <p style="margin:0;color:var(--muted);">No members assigned to this issue.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;padding:20px 28px;">
        <h2 style="margin:0;">Tags</h2>
        @can('update', $issue->project)
            <button id="open-tag-modal" class="button primary" type="button">Manage tags</button>
        @endcan
    </div>

    <div id="tags-list" class="tag-list" style="padding:20px 28px 0;">
        @forelse($issue->tags as $tag)
            <div class="list-pill-row tag-row-item" data-tag-id="{{ $tag->id }}">
                <span>{{ $tag->name }}</span>
                @can('update', $issue->project)
                    <button class="tag-remove" data-tag-id="{{ $tag->id }}" type="button">×</button>
                @endcan
            </div>
        @empty
            <div class="empty tag-empty" style="padding:20px 28px;text-align:left;">
                <p style="margin:0;color:var(--muted);">No tags attached to this issue.</p>
            </div>
        @endforelse
    </div>
</div>

@can('update', $issue->project)
    <div id="member-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Assign members</h3>
                <button id="close-member-modal" class="button link-button" type="button">Close</button>
            </div>
            <div class="member-actions">
                @foreach($allUsers as $user)
                    <button class="member-pill member-selectable {{ $issue->members->contains($user) ? 'selected' : '' }}" data-user-id="{{ $user->id }}" type="button">
                        {{ $user->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div id="tag-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Attach tags</h3>
                <button id="close-tag-modal" class="button link-button" type="button">Close</button>
            </div>
            <div class="tag-actions">
                @foreach($allTags as $tag)
                    <button class="tag-pill tag-selectable {{ $issue->tags->contains($tag) ? 'selected' : '' }}" data-tag-id="{{ $tag->id }}" type="button">
                        {{ $tag->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endcan

<div class="panel">
    <div class="panel-heading" style="padding:20px 28px;">
        <h2 style="margin:0;">Comments</h2>
    </div>

    <div id="comments-error" class="form-errors" style="display:none;margin:20px 28px 1rem;"></div>

    <div id="comments-list" style="padding:0 28px;">
        @php
            $comments = $issue->comments()->with('user')->latest()->paginate(5);
        @endphp
        @include('comments.partials.list', ['comments' => $comments])
    </div>

    <form id="comment-form" class="panel form-panel" method="POST" action="{{ route('issues.comments.store', $issue) }}" style="margin-top:1rem;">
        @csrf
        <textarea name="body" id="comment-body" rows="4" placeholder="Write a comment..." required></textarea>
        <button class="button primary" type="submit">Post comment</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const issueId = {{ $issue->id }};
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const commentsEndpoint = '{{ route('issues.comments.index', $issue) }}';
        const commentsStoreEndpoint = '{{ route('issues.comments.store', $issue) }}';

        const tagModal = document.getElementById('tag-modal');
        const openTagModal = document.getElementById('open-tag-modal');
        const closeTagModal = document.getElementById('close-tag-modal');
        const tagActions = document.querySelector('.tag-actions');
        const tagsList = document.getElementById('tags-list');

        const memberModal = document.getElementById('member-modal');
        const openMemberModal = document.getElementById('open-member-modal');
        const closeMemberModal = document.getElementById('close-member-modal');
        const memberActions = document.querySelector('.member-actions');
        const membersList = document.getElementById('members-list');

        const commentsList = document.getElementById('comments-list');
        const commentsError = document.getElementById('comments-error');
        const commentForm = document.getElementById('comment-form');
        const commentBody = document.getElementById('comment-body');

        function toggleModal(modal, isOpen) {
            if (!modal) {
                return;
            }

            modal.style.display = isOpen ? 'flex' : 'none';
        }

        function bindModal(openButton, closeButton, modal) {
            openButton?.addEventListener('click', function () {
                toggleModal(modal, true);
            });

            closeButton?.addEventListener('click', function () {
                toggleModal(modal, false);
            });

            modal?.addEventListener('click', function (event) {
                if (event.target === modal) {
                    toggleModal(modal, false);
                }
            });
        }

        bindModal(openTagModal, closeTagModal, tagModal);
        bindModal(openMemberModal, closeMemberModal, memberModal);

        function createListRow(type, id, label) {
            const row = document.createElement('div');
            row.className = `list-pill-row ${type}-row`;

            if (type === 'tag') {
                row.dataset.tagId = id;
            } else {
                row.dataset.userId = id;
            }

            const text = document.createElement('span');
            text.textContent = label;
            row.appendChild(text);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = type === 'tag' ? 'tag-remove' : 'tag-remove member-remove';
            removeButton.textContent = '×';

            if (type === 'tag') {
                removeButton.dataset.tagId = id;
            } else {
                removeButton.dataset.userId = id;
            }

            row.appendChild(removeButton);

            return row;
        }

        function syncEmptyState(container, selector, message, emptyClass) {
            const currentEmpty = container.querySelector(`.${emptyClass}`);
            if (container.querySelectorAll(selector).length === 0) {
                if (!currentEmpty) {
                    const empty = document.createElement('div');
                    empty.className = `empty ${emptyClass}`;
                    empty.style.cssText = 'padding:20px 28px;text-align:left;';
                    empty.innerHTML = `<p style="margin:0;color:var(--muted);">${message}</p>`;
                    container.appendChild(empty);
                }
                return;
            }

            currentEmpty?.remove();
        }

        async function jsonRequest(url, method) {
            return fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    Accept: 'application/json',
                },
            });
        }

        async function loadComments(url = commentsEndpoint) {
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    Accept: 'application/json',
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

                const response = await fetch(commentsStoreEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ body: commentBody.value }),
                });

                if (response.status === 422) {
                    const data = await response.json();
                    const messages = Object.values(data.errors || {}).flat();
                    commentsError.innerHTML = messages.map(function (message) {
                        return `<p>${message}</p>`;
                    }).join('');
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
            const response = await jsonRequest(`/issues/${issueId}/tags/${tagId}`, 'DELETE');

            if (!response.ok) {
                return;
            }

            button.closest('[data-tag-id]')?.remove();
            document.querySelector(`.tag-selectable[data-tag-id="${tagId}"]`)?.classList.remove('selected');
            syncEmptyState(tagsList, '.list-pill-row[data-tag-id]', 'No tags attached to this issue.', 'tag-empty');
        });

        tagActions?.addEventListener('click', async function (event) {
            const tagButton = event.target.closest('.tag-selectable');
            if (!tagButton) {
                return;
            }

            const tagId = tagButton.dataset.tagId;
            const isSelected = tagButton.classList.contains('selected');
            const response = await jsonRequest(`/issues/${issueId}/tags/${tagId}`, isSelected ? 'DELETE' : 'POST');

            if (!response.ok) {
                return;
            }

            tagButton.classList.toggle('selected');
            const existing = tagsList.querySelector(`.list-pill-row[data-tag-id="${tagId}"]`);

            if (!isSelected && !existing) {
                tagsList.querySelector('.tag-empty')?.remove();
                tagsList.appendChild(createListRow('tag', tagId, tagButton.textContent.trim()));
            }

            if (isSelected && existing) {
                existing.remove();
                syncEmptyState(tagsList, '.list-pill-row[data-tag-id]', 'No tags attached to this issue.', 'tag-empty');
            }
        });

        membersList?.addEventListener('click', async function (event) {
            const button = event.target.closest('.member-remove');
            if (!button) {
                return;
            }

            const userId = button.dataset.userId;
            const response = await jsonRequest(`/issues/${issueId}/members/${userId}`, 'DELETE');

            if (!response.ok) {
                return;
            }

            button.closest('[data-user-id]')?.remove();
            document.querySelector(`.member-selectable[data-user-id="${userId}"]`)?.classList.remove('selected');
            syncEmptyState(membersList, '.list-pill-row[data-user-id]', 'No members assigned to this issue.', 'member-empty');
        });

        memberActions?.addEventListener('click', async function (event) {
            const memberButton = event.target.closest('.member-selectable');
            if (!memberButton) {
                return;
            }

            const userId = memberButton.dataset.userId;
            const isSelected = memberButton.classList.contains('selected');
            const response = await jsonRequest(`/issues/${issueId}/members/${userId}`, isSelected ? 'DELETE' : 'POST');

            if (!response.ok) {
                return;
            }

            memberButton.classList.toggle('selected');
            const existing = membersList.querySelector(`.list-pill-row[data-user-id="${userId}"]`);

            if (!isSelected && !existing) {
                membersList.querySelector('.member-empty')?.remove();
                membersList.appendChild(createListRow('member', userId, memberButton.textContent.trim()));
            }

            if (isSelected && existing) {
                existing.remove();
                syncEmptyState(membersList, '.list-pill-row[data-user-id]', 'No members assigned to this issue.', 'member-empty');
            }
        });
    });
</script>
@endsection
