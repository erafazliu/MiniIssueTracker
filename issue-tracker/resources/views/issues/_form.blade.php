@include('partials.errors')
<div class="form-grid">
    <label class="full">
        Project
        <select name="project_id" required>
            <option value="">Select a project</option>
            @foreach($projects as $id => $name)
                <option value="{{ $id }}" {{ old('project_id', $issue->project_id ?? $projectId ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </label>
    <label class="full">
        Title
        <input name="title" value="{{ old('title', $issue->title ?? '') }}" required maxlength="180">
    </label>
    <label class="full">
        Description
        <textarea name="description" rows="7" required>{{ old('description', $issue->description ?? '') }}</textarea>
    </label>
    <label>
        Status
        <select name="status" required>
            @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $issue->status ?? 'open') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label>
        Priority
        <select name="priority" required>
            @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                <option value="{{ $value }}" {{ old('priority', $issue->priority ?? 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label>
        Due date
        <input type="date" name="due_date" value="{{ old('due_date', isset($issue) && $issue->due_date ? $issue->due_date->format('Y-m-d') : '') }}">
    </label>
    <fieldset class="full">
        <legend>Assign members</legend>
        <div class="stack">
            @php
                $selectedMembers = collect(old('members', isset($issue) ? $issue->members->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
            @endphp
            @forelse($users as $user)
                <label class="check">
                    <input
                        type="checkbox"
                        name="members[]"
                        value="{{ $user->id }}"
                        {{ in_array($user->id, $selectedMembers, true) ? 'checked' : '' }}
                    >
                    <span>{{ $user->name }}</span>
                </label>
            @empty
                <p class="muted">No users available to assign.</p>
            @endforelse
        </div>
    </fieldset>
    <fieldset class="full">
        <legend>Attach tags</legend>
        <div class="stack">
            @php
                $selectedTags = collect(old('tags', isset($issue) ? $issue->tags->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
            @endphp
            @forelse($tags as $tag)
                <label class="check">
                    <input
                        type="checkbox"
                        name="tags[]"
                        value="{{ $tag->id }}"
                        {{ in_array($tag->id, $selectedTags, true) ? 'checked' : '' }}
                    >
                    <span>{{ $tag->name }}</span>
                </label>
            @empty
                <p class="muted">No tags available to attach.</p>
            @endforelse
        </div>
    </fieldset>
</div>
<div class="form-actions">
    <a class="button secondary" href="{{ isset($issue) ? route('issues.show', $issue) : route('issues.index') }}">Cancel</a>
    <button class="button primary">{{ $submit ?? 'Save issue' }}</button>
</div>
