<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create users
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Era Fazliu',
                'is_owner' => true,
                'password' => bcrypt('password'),
            ]
        );
        $owner->update(['is_owner' => true]);

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Arber Fazliu',
                'is_owner' => false,
                'password' => bcrypt('password'),
            ]
        );
        $member->update(['is_owner' => false]);

        $developer = User::firstOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Jane Developer',
                'is_owner' => false,
                'password' => bcrypt('password'),
            ]
        );
        $developer->update(['is_owner' => false]);

        $users = collect([$owner, $member, $developer]);
        $members = $users->where('is_owner', false)->values();

        // Create shared tags
        $tagNames = ['Bug', 'Feature', 'Enhancement', 'Documentation', 'Design'];
        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::firstOrCreate(['name' => $name]));
        }

        // Create owner projects and assign members to issues
        $projects = Project::factory(rand(2, 3))->create([
            'owner_id' => $owner->id,
        ]);

        foreach ($projects as $project) {
            // Create 3-5 issues per project
            $issues = Issue::factory(rand(3, 5))->create([
                'project_id' => $project->id,
            ]);

            foreach ($issues as $issue) {
                // Randomly attach 1-3 tags to each issue
                $tagsToAttach = $tags->random(rand(1, 3))->pluck('id');
                $issue->tags()->sync($tagsToAttach);

                // Randomly assign 1-2 member users to each issue
                if ($members->isNotEmpty()) {
                    $selectedMembers = $members->random(rand(1, min(2, $members->count())));
                    $issue->members()->sync(collect($selectedMembers)->pluck('id')->all());
                }

                // Create 2-4 comments per issue
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $issue->comments()->create([
                        'user_id' => $users->random()->id,
                        'body' => fake()->paragraphs(rand(1, 2), true),
                    ]);
                }
            }
        }
    }
}
