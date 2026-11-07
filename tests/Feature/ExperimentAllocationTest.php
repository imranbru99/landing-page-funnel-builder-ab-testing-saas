<?php

namespace Tests\Feature;

use App\Models\Experiment;
use App\Models\ExperimentAssignment;
use App\Models\ExperimentVariant;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Experiments\ExperimentAllocator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperimentAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_is_stickily_assigned_to_one_variant(): void
    {
        $experiment = $this->makeRunningExperiment();
        $allocator = app(ExperimentAllocator::class);

        $visitor = 'visitor-123';
        $first = $allocator->assign($experiment, $visitor);
        $second = $allocator->assign($experiment, $visitor);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, ExperimentAssignment::query()->where('visitor_key', $visitor)->count());
    }

    public function test_weighted_pick_respects_variant_weights(): void
    {
        $allocator = app(ExperimentAllocator::class);
        $experiment = $this->makeRunningExperiment();
        $variants = $experiment->variants;

        $counts = ['A' => 0, 'B' => 0];
        for ($i = 0; $i < 200; $i++) {
            $picked = $allocator->weightedPick($variants);
            $counts[$picked->key]++;
        }

        $this->assertGreaterThan(40, $counts['A']);
        $this->assertGreaterThan(40, $counts['B']);
    }

    protected function makeRunningExperiment(): Experiment
    {
        $plan = Plan::query()->create(['name' => 'T', 'slug' => 't', 'price_cents' => 0]);
        $user = User::factory()->create();
        $org = Organization::query()->create(['name' => 'Org', 'owner_id' => $user->id, 'plan_id' => $plan->id]);
        $workspace = Workspace::query()->create(['organization_id' => $org->id, 'name' => 'W']);
        $project = Project::query()->create(['organization_id' => $org->id, 'workspace_id' => $workspace->id, 'name' => 'P']);
        $page = Page::query()->create(['organization_id' => $org->id, 'project_id' => $project->id, 'title' => 'P1', 'sections' => []]);

        $experiment = Experiment::query()->create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'page_id' => $page->id,
            'name' => 'Headline',
            'status' => 'running',
            'goal_event' => 'conversion',
            'started_at' => now(),
        ]);

        ExperimentVariant::query()->create([
            'experiment_id' => $experiment->id,
            'organization_id' => $org->id,
            'name' => 'Control',
            'key' => 'A',
            'weight' => 50,
            'is_control' => true,
            'content' => ['headline' => 'A'],
        ]);

        ExperimentVariant::query()->create([
            'experiment_id' => $experiment->id,
            'organization_id' => $org->id,
            'name' => 'B',
            'key' => 'B',
            'weight' => 50,
            'is_control' => false,
            'content' => ['headline' => 'B'],
        ]);

        return $experiment->fresh('variants');
    }
}
