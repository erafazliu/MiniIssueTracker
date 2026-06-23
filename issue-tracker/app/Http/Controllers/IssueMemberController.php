<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;

class IssueMemberController extends Controller
{
    public function attach(Issue $issue, User $user)
    {
        $this->authorize('update', $issue->project);

        $issue->members()->syncWithoutDetaching([$user->id]);

        return response()->json(['success' => true]);
    }

    public function detach(Issue $issue, User $user)
    {
        $this->authorize('update', $issue->project);

        $issue->members()->detach($user->id);

        return response()->json(['success' => true]);
    }
}
