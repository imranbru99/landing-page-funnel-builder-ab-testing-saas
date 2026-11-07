@extends('layouts.marketing')
@section('title', 'Landing Funnel Builder — Pages, Funnels & A/B Testing')
@section('content')
<main class="shell">
    <section class="hero">
        <div>
            <div class="brand" style="margin-bottom:1rem;">Landing<span>Funnel</span></div>
            <h1>Ship pages, funnels, and experiments that actually convert.</h1>
            <p>A modular multi-tenant platform for agencies and growth teams — visual builders, real A/B allocation, CRM, automation, and AI assist.</p>
            <div class="hero-actions">
                <a class="cta" href="{{ url('/app/login') }}">Open app</a>
                <a class="cta ghost" href="{{ url('/pricing') }}">See pricing</a>
            </div>
        </div>
        <div class="visual" aria-hidden="true"></div>
    </section>

    <section class="section">
        <h2>Built as independent modules</h2>
        <p class="lead">Enable only what each organization needs. Platform admins control global availability; tenants activate modules from their plan.</p>
        <div class="grid cols-3">
            @foreach([
                ['Landing pages', 'JSON section builder, templates, versioning, publish to subdomain/path.'],
                ['Funnels', 'Steps, connections, conversion tracking, performance by stage.'],
                ['A/B testing', 'Weighted allocation, sticky cookies, events, statistical summary.'],
                ['CRM & forms', 'Contacts, tags, pipelines, public form capture endpoint.'],
                ['Automation', 'Trigger/action workflows with queued runner.'],
                ['AI assistant', 'Copy, SEO, variants via Laravel AI Hub (mockable without keys).'],
            ] as [$t,$d])
                <article class="tile"><h3>{{ $t }}</h3><p>{{ $d }}</p></article>
            @endforeach
        </div>
    </section>
</main>
@endsection