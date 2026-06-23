<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IssueTagController extends Controller
{
    public function store(Request $request, Issue $issue, Tag $tag): JsonResponse
    {
        Gate::authorize('update', $issue->project);

        $issue->tags()->syncWithoutDetaching([$tag->id]);

        return response()->json(['status' => 'attached', 'tag' => $tag]);
    }

    public function destroy(Request $request, Issue $issue, Tag $tag): JsonResponse
    {
        Gate::authorize('update', $issue->project);

        $issue->tags()->detach($tag->id);

        return response()->json(['status' => 'detached', 'tag' => $tag]);
    }
}
