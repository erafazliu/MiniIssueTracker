<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Issue $issue): JsonResponse
    {
        Gate::authorize('view', $issue->project);

        $comments = $issue->comments()
            ->with('user')
            ->latest()
            ->paginate(5);

        return response()->json([
            'html' => view('comments.partials.list', compact('comments'))->render(),
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        Gate::authorize('view', $issue->project);

        $comment = $issue->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->validated()['body'],
        ]);

        $comment->load('user');

        return response()->json([
            'message' => 'Comment added.',
            'html' => view('comments.partials.comment', compact('comment'))->render(),
        ], 201);
    }
}
