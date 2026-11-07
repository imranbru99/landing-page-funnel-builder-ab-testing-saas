@php
    $props = $section['props'] ?? [];
    if (!empty($variant?->content) && ($section['type'] ?? '') === 'hero') {
        $props = array_merge($props, $variant->content);
    }
@endphp

@switch($section['type'] ?? 'html')
    @case('hero')
        <section class="section hero">
            <div class="inner">
                <h1>{{ $props['headline'] ?? 'Headline' }}</h1>
                <p>{{ $props['subheadline'] ?? '' }}</p>
                <a class="btn" href="{{ $props['cta_url'] ?? '#' }}">{{ $props['cta'] ?? 'Get Started' }}</a>
            </div>
        </section>
        @break

    @case('features')
        <section class="section">
            <div class="inner">
                <h2>{{ $props['title'] ?? 'Features' }}</h2>
                <div class="grid cols-3">
                    @foreach(($props['items'] ?? []) as $item)
                        <div class="card">
                            <h3>{{ $item['title'] ?? 'Feature' }}</h3>
                            <p>{{ $item['body'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @break

    @case('cta')
        <section class="section" style="background:#e8f5f1;">
            <div class="inner" style="text-align:center;">
                <h2>{{ $props['headline'] ?? 'Ready?' }}</h2>
                <a class="btn" href="{{ $props['url'] ?? '#' }}">{{ $props['button'] ?? 'Continue' }}</a>
            </div>
        </section>
        @break

    @case('testimonials')
        <section class="section">
            <div class="inner">
                <h2>{{ $props['title'] ?? 'Testimonials' }}</h2>
                <div class="grid cols-3">
                    @foreach(($props['quotes'] ?? []) as $quote)
                        <div class="card">
                            <p>“{{ $quote['quote'] ?? '' }}”</p>
                            <strong>{{ $quote['name'] ?? '' }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @break

    @case('form')
        <section class="section">
            <div class="inner">
                <div class="card" style="max-width:520px;margin:0 auto;">
                    <h2>{{ $props['title'] ?? 'Contact us' }}</h2>
                    <form method="POST" action="{{ url('/forms/'.($props['form_slug'] ?? 'lead-capture').'/submit') }}" data-lf-form>
                        @csrf
                        <input type="hidden" name="page_id" value="{{ $page->id ?? '' }}">
                        <label>Name</label>
                        <input type="text" name="name" required>
                        <label>Email</label>
                        <input type="email" name="email" required>
                        <label>Message</label>
                        <textarea name="message" rows="3"></textarea>
                        <button class="btn" style="margin-top:1rem;" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </section>
        @break

    @case('faq')
        <section class="section">
            <div class="inner">
                <h2>{{ $props['title'] ?? 'FAQ' }}</h2>
                @foreach(($props['items'] ?? []) as $item)
                    <div class="card" style="margin-bottom:.6rem;">
                        <strong>{{ $item['q'] ?? '' }}</strong>
                        <p>{{ $item['a'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
        @break

    @default
        <section class="section">
            <div class="inner">
                {!! strip_tags($props['html'] ?? '', '<p><br><strong><em><ul><ol><li><a><h2><h3><h4>') !!}
            </div>
        </section>
@endswitch
