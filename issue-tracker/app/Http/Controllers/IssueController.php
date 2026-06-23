<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class IssueController extends Controller
{

    public function index()
    {
        $issues = Issue::query()
            ->whereHas('project', fn ($query) =>
                $query->where('owner_id', auth()->id())
            )
            ->with(['project'])
            ->when(request()->filled('status'), fn ($query) =>
                $query->where('status', request('status'))
            )
            ->when(request()->filled('priority'), fn ($query) =>
                $query->where('priority', request('priority'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('issues.index', compact('issues'));
    }


    public function create()
    {
        $projects = auth()->user()->projects()->pluck('name', 'id');
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('issues.create', [
            'projects' => $projects,
            'projectId' => request('project_id'),
            'users' => $users,
        ]);
    }


    public function store(StoreIssueRequest $request)
    {
        $validated = $request->validated();
        $memberIds = $validated['members'] ?? [];
        unset($validated['members']);

        $issue = Issue::create($validated);
        $issue->members()->sync($memberIds);

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

        return view('issues.edit', compact('issue', 'projects', 'users'));
    }


    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $validated = $request->validated();
        $memberIds = $validated['members'] ?? [];
        unset($validated['members']);

        $issue->update($validated);
        $issue->members()->sync($memberIds);

        return redirect()->route('issues.show', $issue)->with('success', 'Issue updated.');
    }


    public function destroy(Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $issue->delete();

        return redirect()->route('issues.index')->with('success', 'Issue deleted.');
    }
}
