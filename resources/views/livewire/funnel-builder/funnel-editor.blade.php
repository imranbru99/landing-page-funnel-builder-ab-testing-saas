<div class="wrap">
    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;">
        <div>
            <h1 style="margin:0;">{{ $funnel->name }}</h1>
            <p style="margin:.25rem 0 0;color:#4b635d;">List + connection editor · {{ $funnel->status }}</p>
        </div>
        <button class="btn" type="button" wire:click="publish">Publish funnel</button>
    </div>

    @if($message)
        <div class="card" style="margin:1rem 0;border-color:#9ad3c7;">{{ $message }}</div>
    @endif

    <div class="grid grid-2" style="margin-top:1rem;">
        <div class="card">
            <h3 style="margin-top:0;">Steps</h3>
            @foreach($funnel->steps as $step)
                <div class="card" style="margin-bottom:.6rem;background:#f7fbf9;">
                    <strong>#{{ $step->position }} {{ $step->name }}</strong>
                    <div style="color:#4b635d;">{{ $step->type }}</div>
                </div>
            @endforeach

            <div style="margin-top:1rem;">
                <label>New step name</label>
                <input type="text" wire:model="newStepName">
                <label style="margin-top:.5rem;">Type</label>
                <select wire:model="newStepType">
                    @foreach(['landing','lead_capture','sales','checkout','thank_you','upsell','email','redirect'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                <button class="btn" style="margin-top:.75rem;" type="button" wire:click="addStep">Add step</button>
            </div>

            <div style="margin-top:1.25rem;">
                <h4>Connect steps</h4>
                <label>From</label>
                <select wire:model="connectFrom">
                    <option value="">Select</option>
                    @foreach($funnel->steps as $step)
                        <option value="{{ $step->id }}">{{ $step->name }}</option>
                    @endforeach
                </select>
                <label style="margin-top:.5rem;">To</label>
                <select wire:model="connectTo">
                    <option value="">Select</option>
                    @foreach($funnel->steps as $step)
                        <option value="{{ $step->id }}">{{ $step->name }}</option>
                    @endforeach
                </select>
                <button class="btn secondary" style="margin-top:.75rem;" type="button" wire:click="connectSteps">Connect</button>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Flow</h3>
            @forelse($funnel->connections as $connection)
                <div style="padding:.5rem 0;border-bottom:1px solid #d9ebe6;">
                    {{ $connection->fromStep?->name }} → {{ $connection->toStep?->name }}
                    <span style="color:#4b635d;">({{ $connection->condition_type }})</span>
                </div>
            @empty
                <p style="color:#4b635d;">No connections yet.</p>
            @endforelse

            <h3 style="margin-top:1.5rem;">Performance</h3>
            @foreach($performance as $row)
                <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid #e7f1ee;">
                    <span>{{ $row['name'] }}</span>
                    <span>{{ $row['views'] }} views · {{ $row['conversion_rate_pct'] }}%</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
