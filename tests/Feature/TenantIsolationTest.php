<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_cannot_see_another_organizations_pages(): void
    {
        $plan = Plan::query()->create([
            'name' => 'Test',
            'slug' => 'test',
            'price_cents' => 0,
            'is_active' => true,
        ]);

        [$orgA, $userA, $pageA] = $this->makeTenant('Alpha', 'a@example.com', $plan);
        [$orgB, $userB, $pageB] = $this->makeTenant('Beta', 'b@example.com', $plan);

        $this->assertNotSame($pageA->id, $pageB->id);

        $visibleToA = Page::query()->forOrganization($orgA)->pluck('id');
        $this->assertTrue($visibleToA->contains($pageA->id));
        $this->assertFalse($visibleToA->contains($pageB->id));

        $this->actingAs($userA);
        $this->assertSame($orgA->id, $userA->fresh()->current_organization_id);
        $this->assertNotSame($orgA->id, $orgB->id);
    }

    protected function makeTenant(string $name, string $email, Plan $plan): array
    {
        $user = User::factory()->create([
            'email' => $email,
            'name' => $name.' Owner',
        ]);

        $org = Organization::query()->create([
            'name' => $name,
            'owner_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $org->users()->attach($user->id, ['role' => 'owner']);

        $workspace = Workspace::query()->create([
            'organization_id' => $org->id,
            'name' => 'Main',
        ]);

        $project = Project::query()->create([
            'organization_id' => $org->id,
            'workspace_id' => $workspace->id,
            'name' => 'Site',
        ]);

        $page = Page::query()->create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => $name.' Page',
            'status' => 'draft',
            'sections' => [],
        ]);

        $user->update(['current_organization_id' => $org->id]);

        return [$org, $user->fresh(), $page];
    }
}
