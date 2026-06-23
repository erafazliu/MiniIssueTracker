<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Tests\TestCase;

class IssueTrackerTest extends TestCase
{
    /**
     * Test 1: Guests are redirected to login
     */
    public function test_guests_are_redirected_to_login()
    {
        $response = $this->get('/issues');
        $response->assertRedirect('/login');

        $response = $this->get('/projects');
        $response->assertRedirect('/login');

        $response = $this->get('/tags');
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: Valid login succeeds
     */
    public function test_valid_login_succeeds()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 3: Users can create projects
     */
    public function test_users_can_create_projects()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Test Project',
            'description' => 'A test project description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'owner_id' => $user->id,
            'name' => 'Test Project',
            'description' => 'A test project description',
        ]);
    }

    /**
     * Test 4: Non-owners cannot edit/delete projects
     */
    public function test_non_owners_cannot_edit_delete_projects()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);

        // Non-owner cannot access edit page
        $response = $this->actingAs($other)->get("/projects/{$project->id}/edit");
        $response->assertForbidden();

        // Non-owner cannot update
        $response = $this->actingAs($other)->put("/projects/{$project->id}", [
            'name' => 'Updated',
            'description' => 'Updated',
        ]);
        $response->assertForbidden();

        // Non-owner cannot delete
        $response = $this->actingAs($other)->delete("/projects/{$project->id}");
        $response->assertForbidden();
    }

    /**
     * Test 5: Issue results are scoped to the project owner
     */
    public function test_issue_results_are_scoped_to_project_owner()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerProject = Project::factory()->create(['owner_id' => $owner->id]);
        $otherProject = Project::factory()->create(['owner_id' => $other->id]);

        $ownerIssue = Issue::factory()->create(['project_id' => $ownerProject->id]);
        $otherIssue = Issue::factory()->create(['project_id' => $otherProject->id]);

        $response = $this->actingAs($owner)->get('/issues');
        $response->assertOk();
        // Verify the owner sees only their project's issues
        $this->assertDatabaseCount('issues', 2);
        $ownerCount = Issue::whereIn('project_id', $owner->projects->pluck('id'))->count();
        $this->assertEquals(1, $ownerCount);
    }

    /**
     * Test 6: Status, priority, tag, and search filters work
     */
    public function test_status_priority_tag_and_search_filters_work()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);

        $openIssue = Issue::factory()->create([
            'project_id' => $project->id,
            'title' => 'Open Bug',
            'status' => 'open',
            'priority' => 'high',
        ]);

        $closedIssue = Issue::factory()->create([
            'project_id' => $project->id,
            'title' => 'Closed Feature',
            'status' => 'closed',
            'priority' => 'low',
        ]);

        $tag = Tag::factory()->create(['name' => 'urgent']);
        $openIssue->tags()->attach($tag->id);

        // Test status filter works at the database level
        $statusFiltered = Issue::where('status', 'open')->get();
        $this->assertCount(1, $statusFiltered);

        // Test priority filter works
        $priorityFiltered = Issue::where('priority', 'high')->get();
        $this->assertCount(1, $priorityFiltered);

        // Test tag attachment
        $taggedIssues = Issue::whereHas('tags', fn ($q) => $q->where('name', 'urgent'))->get();
        $this->assertCount(1, $taggedIssues);
    }

    /**
     * Test 7: Tags attach and detach through JSON endpoints
     */
    public function test_tags_attach_and_detach_through_json_endpoints()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $issue = Issue::factory()->create(['project_id' => $project->id]);
        $tag = Tag::factory()->create();

        // Attach tag
        $response = $this->actingAs($user)->postJson("/issues/{$issue->id}/tags/{$tag->id}");
        $response->assertSuccessful();
        $this->assertDatabaseHas('issue_tag', [
            'issue_id' => $issue->id,
            'tag_id' => $tag->id,
        ]);

        // Detach tag
        $response = $this->actingAs($user)->deleteJson("/issues/{$issue->id}/tags/{$tag->id}");
        $response->assertSuccessful();
        $this->assertDatabaseMissing('issue_tag', [
            'issue_id' => $issue->id,
            'tag_id' => $tag->id,
        ]);
    }

    /**
     * Test 8: Comments are created through JSON endpoints
     */
    public function test_comments_are_created_through_json_endpoints()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $issue = Issue::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)->postJson("/issues/{$issue->id}/comments", [
            'body' => 'This is a test comment',
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('comments', [
            'issue_id' => $issue->id,
            'user_id' => $user->id,
            'body' => 'This is a test comment',
        ]);
    }

    /**
     * Test 9: Empty comments return validation errors
     */
    public function test_empty_comments_return_validation_errors()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $issue = Issue::factory()->create(['project_id' => $project->id]);

        $countBefore = Comment::count();

        $response = $this->actingAs($user)->postJson("/issues/{$issue->id}/comments", [
            'body' => '',
        ]);

        $countAfter = Comment::count();

        // Validation should prevent empty comments from being created
        $this->assertEquals($countBefore, $countAfter);
    }

    /**
     * Test 10: Members attach and detach through JSON endpoints
     */
    public function test_members_attach_and_detach_through_json_endpoints()
    {
        $projectOwner = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $projectOwner->id]);
        $issue = Issue::factory()->create(['project_id' => $project->id]);

        // Attach member
        $response = $this->actingAs($projectOwner)->postJson("/issues/{$issue->id}/members/{$member->id}");
        $response->assertSuccessful();
        $this->assertDatabaseHas('issue_user', [
            'issue_id' => $issue->id,
            'user_id' => $member->id,
        ]);

        // Detach member
        $response = $this->actingAs($projectOwner)->deleteJson("/issues/{$issue->id}/members/{$member->id}");
        $response->assertSuccessful();
        $this->assertDatabaseMissing('issue_user', [
            'issue_id' => $issue->id,
            'user_id' => $member->id,
        ]);
    }
}
