<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = Issue::query()
            ->where(function ($query) {
                $query->whereHas('project', fn ($project) => $project->where('owner_id', auth()->id()))
                    ->orWhereHas('members', fn ($members) => $members->where('users.id', auth()->id()));
            })
            ->with(['project'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';

                $query->where(fn ($nested) => $nested->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                );
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status'))
            )
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('issues.partials.results', compact('issues'));
        }

        return view('issues.index', compact('issues'));
    }

    public function create()
    {
        Gate::authorize('create', Project::class);

        $projects = auth()->user()->projects()->pluck('name', 'id');
        $users = User::orderBy('name')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);

        return view('issues.create', [
            'projects' => $projects,
            'projectId' => request('project_id'),
            'users' => $users,
            'tags' => $tags,
        ]);
    }

    public function store(StoreIssueRequest $request)
    {
        Gate::authorize('create', Project::class);

        $validated = $request->validated();
        $memberIds = $validated['members'] ?? [];
        $tagIds = $validated['tags'] ?? [];
        unset($validated['members'], $validated['tags']);

        $issue = Issue::create($validated);
        $issue->members()->sync($memberIds);
        $issue->tags()->sync($tagIds);

        return redirect()->route('issues.show', $issue)->with('success', 'Issue created.');
    }

    public function show(Issue $issue)
    {
        Gate::authorize('view', $issue->project);

        return view('issues.show', compact('issue'));
    }

    public function edit(Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $projects = auth()->user()->projects()->pluck('name', 'id');
        $users = User::orderBy('name')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);

        return view('issues.edit', compact('issue', 'projects', 'users', 'tags'));
    }

    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $validated = $request->validated();
        $memberIds = $validated['members'] ?? [];
        $tagIds = $validated['tags'] ?? [];
        unset($validated['members'], $validated['tags']);

        $issue->update($validated);
        $issue->members()->sync($memberIds);
        $issue->tags()->sync($tagIds);

        return redirect()->route('issues.show', $issue)->with('success', 'Issue updated.');
    }

    public function destroy(Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $issue->delete();

        return redirect()->route('issues.index')->with('success', 'Issue deleted.');
    }
}
