<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use Illuminate\Support\Facades\Gate;

class IssueController extends Controller
{

    public function index()
    {
        $issues = Issue::with('project')
            ->whereHas('project', fn ($query) => $query->where('owner_id', auth()->id()))
            ->latest()
            ->paginate(10);

        return view('issues.index', compact('issues'));
    }


    public function create()
    {
        $projects = auth()->user()->projects()->pluck('name', 'id');

        return view('issues.create', [
            'projects' => $projects,
            'projectId' => request('project_id'),
        ]);
    }


    public function store(StoreIssueRequest $request)
    {
        $issue = Issue::create($request->validated());

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

        return view('issues.edit', compact('issue', 'projects'));
    }


    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $issue->update($request->validated());

        return redirect()->route('issues.show', $issue)->with('success', 'Issue updated.');
    }


    public function destroy(Issue $issue)
    {
        Gate::authorize('update', $issue->project);

        $issue->delete();

        return redirect()->route('issues.index')->with('success', 'Issue deleted.');
    }
}
