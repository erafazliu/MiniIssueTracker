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
                'password' => bcrypt('password'),
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Arber Fazliu',
                'password' => bcrypt('password'),
            ]
        );

        $developer = User::firstOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Jane Developer',
                'password' => bcrypt('password'),
            ]
        );

        $users = [$owner, $member, $developer];

        // Create shared tags
        $tagNames = ['Bug', 'Feature', 'Enhancement', 'Documentation', 'Design'];
        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::firstOrCreate(['name' => $name]));
        }

        // Create projects and issues with comments
        foreach ($users as $user) {
            // Create 2-3 projects per user
            $projects = Project::factory(rand(2, 3))->create([
                'owner_id' => $user->id,
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

                    // Create 2-4 comments per issue
                    for ($i = 0; $i < rand(2, 4); $i++) {
                        $issue->comments()->create([
                            'user_id' => $users[array_rand($users)]->id,
                            'body' => fake()->paragraphs(rand(1, 2), true),
                        ]);
                    }
                }
            }
        }
    }
}
