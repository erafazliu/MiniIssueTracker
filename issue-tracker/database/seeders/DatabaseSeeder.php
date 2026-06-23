<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        $owner = User::query()->firstOrCreate(
            [
                'email' => 'owner@example.com',
            ],
            [
                'name' => 'Era Fazliu',
                'password' => bcrypt('password'),
            ]
        );

        $member = User::query()->firstOrCreate(
            [
                'email' => 'member@example.com',
            ],
            [
                'name' => 'Arber Fazliu',
                'password' => bcrypt('password'),
            ]
        );

        // Create tags
        $tags = Tag::firstOrCreate(['name' => 'Bug'], ['name' => 'Bug']);
        Tag::firstOrCreate(['name' => 'Feature'], ['name' => 'Feature']);
        Tag::firstOrCreate(['name' => 'Enhancement'], ['name' => 'Enhancement']);
        Tag::firstOrCreate(['name' => 'Documentation'], ['name' => 'Documentation']);

        // Create projects
        $project = Project::firstOrCreate(
            ['name' => 'Sample Project'],
            [
                'owner_id' => $owner->id,
                'description' => 'A sample project to get started',
            ]
        );

        // Create issues
        $issue = Issue::firstOrCreate(
            ['title' => 'Fix login bug', 'project_id' => $project->id],
            [
                'description' => 'Users are unable to log in on mobile devices',
                'status' => 'open',
                'priority' => 'high',
                'project_id' => $project->id,
            ]
        );

        Issue::firstOrCreate(
            ['title' => 'Add dark mode', 'project_id' => $project->id],
            [
                'description' => 'Implement dark mode for better user experience',
                'status' => 'in_progress',
                'priority' => 'medium',
                'project_id' => $project->id,
            ]
        );
    }
}
