@extends('layouts.marketing')
@section('title', 'Templates — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>Template marketplace</h2>
    <p class="lead">Start from conversion-ready layouts and customize sections in the editor.</p>
    <div class="grid cols-3">
        @forelse($templates as $template)
            <article class="tile">
                <h3>{{ $template->name }}</h3>
                <p>{{ $template->description }}</p>
                <p style="margin-top:.6rem;color:var(--teal);">{{ $template->category }} · {{ $template->installs }} installs</p>
            </article>
        @empty
            <article class="tile"><h3>SaaS Launch</h3><p>Hero, features, pricing, FAQ.</p></article>
        @endforelse
    </div>
</main>
@endsection