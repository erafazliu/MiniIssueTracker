<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhereHas('issues.members', fn ($members) => $members->where('users.id', auth()->id()));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';

                $query->where(fn ($nested) => $nested->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search)
                );
            })
            ->distinct()
            ->latest()
            ->paginate(9)
            ->withQueryString();

        if ($request->ajax()) {
            return view('projects.partials.results', compact('projects'));
        }

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $project = auth()->user()->projects()->create($request->validated());

        return redirect()->route('projects.show', $project)->with('success', 'Project created.');
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
