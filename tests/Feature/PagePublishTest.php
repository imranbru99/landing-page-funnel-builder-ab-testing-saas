<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Pages\PagePublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagePublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_page_is_publicly_reachable(): void
    {
        $plan = Plan::query()->create(['name' => 'T', 'slug' => 't', 'price_cents' => 0]);
        $user = User::factory()->create();
        $org = Organization::query()->create(['name' => 'Acme', 'owner_id' => $user->id, 'plan_id' => $plan->id]);
        $workspace = Workspace::query()->create(['organization_id' => $org->id, 'name' => 'Main']);
        $project = Project::query()->create(['organization_id' => $org->id, 'workspace_id' => $workspace->id, 'name' => 'Site']);

        $page = Page::query()->create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'title' => 'Launch Page',
            'sections' => [
                ['id' => '1', 'type' => 'hero', 'props' => ['headline' => 'Hello Publish', 'subheadline' => 'Works', 'cta' => 'Go', 'cta_url' => '#']],
            ],
            'status' => 'draft',
        ]);

        $published = app(PagePublisher::class)->publish($page, 'launch-page', 'acme-launch', $user->id);

        $this->assertSame('published', $published->status);
        $this->assertNotNull($published->published_at);

        $this->get('/p/acme-launch')
            ->assertOk()
            ->assertSee('Hello Publish');

        $this->get('/sites/launch-page')
            ->assertOk()
            ->assertSee('Hello Publish');
    }
}
