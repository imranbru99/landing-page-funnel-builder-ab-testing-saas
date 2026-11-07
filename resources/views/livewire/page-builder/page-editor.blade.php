<div class="wrap">
    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;margin-bottom:1rem;">
        <div>
            <h1 style="margin:0;font-size:1.6rem;">{{ $page->title }}</h1>
            <p style="margin:.2rem 0 0;color:#4b635d;">Structured section editor · {{ $page->status }}</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <button class="btn secondary" wire:click="$set('previewDevice','mobile')" type="button">Mobile</button>
            <button class="btn secondary" wire:click="$set('previewDevice','desktop')" type="button">Desktop</button>
            <button class="btn secondary" wire:click="saveDraft" type="button">Save draft</button>
            <button class="btn" wire:click="publish" type="button">Publish</button>
        </div>
    </div>

    @if($message)
        <div class="card" style="margin-bottom:1rem;border-color:#9ad3c7;">{{ $message }}</div>
    @endif

    <div class="grid grid-2">
        <div class="card">
            <h3 style="margin-top:0;">Sections</h3>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1rem;">
                @foreach(['hero','features','cta','testimonials','form','faq','html'] as $type)
                    <button class="btn secondary" type="button" wire:click="addSection('{{ $type }}')">+ {{ ucfirst($type) }}</button>
                @endforeach
            </div>

            @foreach($sections as $index => $section)
                <div class="card" style="margin-bottom:.75rem;background:#f7fbf9;" wire:key="{{ $section['id'] }}">
                    <div style="display:flex;justify-content:space-between;gap:.5rem;align-items:center;">
                        <strong>{{ strtoupper($section['type']) }}</strong>
                        <div style="display:flex;gap:.35rem;">
                            <button class="btn secondary" type="button" wire:click="moveUp({{ $index }})">↑</button>
                            <button class="btn secondary" type="button" wire:click="moveDown({{ $index }})">↓</button>
                            <button class="btn secondary" type="button" wire:click="removeSection({{ $index }})">Remove</button>
                        </div>
                    </div>
                    @foreach(($section['props'] ?? []) as $key => $value)
                        @if(!is_array($value))
                            <div style="margin-top:.6rem;">
                                <label>{{ $key }}</label>
                                <input type="text" wire:model.live="sections.{{ $index }}.props.{{ $key }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Preview ({{ $previewDevice }})</h3>
            <div style="margin:0 auto; border:1px solid #c9ddd7; border-radius:.8rem; overflow:hidden; background:#fff; width: {{ $previewDevice === 'mobile' ? '360px' : '100%' }};">
                @foreach($sections as $section)
                    @include('published.partials.section', ['section' => $section, 'variant' => null])
                @endforeach
            </div>
            @if($page->publicUrl())
                <p style="margin-top:1rem;"><a href="{{ $page->publicUrl() }}" target="_blank">Open published page</a></p>
            @endif
        </div>
    </div>
</div>
