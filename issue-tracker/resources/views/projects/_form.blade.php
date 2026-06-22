@include('partials.errors')
<div class="form-grid">
    <label class="full">Project name<input name="name" value="{{ old('name', $project->name ?? '') }}" required maxlength="120"></label>
    <label>Start date<input type="date" name="start_date" value="{{ old('start_date', isset($project) && $project->start_date ? $project->start_date->format('Y-m-d') : '') }}"></label>
    <label>Deadline<input type="date" name="deadline" value="{{ old('deadline', isset($project) && $project->deadline ? $project->deadline->format('Y-m-d') : '') }}"></label>
    <label class="full">Description<textarea name="description" rows="7">{{ old('description', $project->description ?? '') }}</textarea></label>
</div>
<div class="form-actions">
    <a class="button secondary" href="{{ isset($project) ? route('projects.show', $project) : route('projects.index') }}">Cancel</a>
    <button class="button primary">{{ $submit ?? 'Save' }}</button>
</div>
