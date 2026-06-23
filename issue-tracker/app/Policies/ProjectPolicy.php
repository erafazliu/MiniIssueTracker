<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if ($project->owner_id === $user->id) {
            return true;
        }

        return $project->issues()
            ->whereHas('members', fn ($query) => $query->where('users.id', $user->id))
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->is_owner;
    }

    public function update(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }
}
