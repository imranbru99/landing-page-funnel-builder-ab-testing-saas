<?php

namespace App\Livewire\FunnelBuilder;

use App\Models\Funnel;
use App\Models\FunnelConnection;
use App\Models\FunnelStep;
use App\Services\Funnels\FunnelTracker;
use Illuminate\Support\Str;
use Livewire\Component;

class FunnelEditor extends Component
{
    public Funnel $funnel;

    public string $newStepName = '';

    public string $newStepType = 'landing';

    public ?int $connectFrom = null;

    public ?int $connectTo = null;

    public string $message = '';

    public function mount(Funnel $funnel): void
    {
        $this->funnel = $funnel->load(['steps', 'connections']);
    }

    public function addStep(): void
    {
        $this->validate([
            'newStepName' => 'required|string|min:2',
            'newStepType' => 'required|string',
        ]);

        $position = (int) $this->funnel->steps()->max('position') + 1;

        FunnelStep::query()->create([
            'funnel_id' => $this->funnel->id,
            'organization_id' => $this->funnel->organization_id,
            'name' => $this->newStepName,
            'slug' => Str::slug($this->newStepName).'-'.$position,
            'type' => $this->newStepType,
            'position' => $position,
            'canvas_x' => 80 + ($position * 40),
            'canvas_y' => 80 + (($position % 3) * 60),
        ]);

        $this->newStepName = '';
        $this->funnel->refresh()->load(['steps', 'connections']);
        $this->message = 'Step added.';
    }

    public function connectSteps(): void
    {
        $this->validate([
            'connectFrom' => 'required|integer',
            'connectTo' => 'required|integer|different:connectFrom',
        ]);

        FunnelConnection::query()->firstOrCreate([
            'funnel_id' => $this->funnel->id,
            'from_step_id' => $this->connectFrom,
            'to_step_id' => $this->connectTo,
        ], [
            'label' => 'Next',
            'condition_type' => 'always',
        ]);

        $this->funnel->refresh()->load(['steps', 'connections']);
        $this->message = 'Steps connected.';
    }

    public function publish(): void
    {
        $this->funnel->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
        $this->message = 'Funnel published.';
    }

    public function render(FunnelTracker $tracker)
    {
        return view('livewire.funnel-builder.funnel-editor', [
            'performance' => $tracker->performance($this->funnel),
        ])->layout('layouts.app', ['title' => 'Funnel Editor']);
    }
}
