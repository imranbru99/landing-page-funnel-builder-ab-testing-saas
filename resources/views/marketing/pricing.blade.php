@extends('layouts.marketing')
@section('title', 'Pricing — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>Simple plans for growth teams</h2>
    <p class="lead">Entitlements control workspaces, pages, funnels, contacts, and AI credits.</p>
    <div class="grid cols-3">
        @forelse($plans as $plan)
            <article class="price">
                <h3>{{ $plan->name }}</h3>
                <div class="amount">${{ number_format($plan->price_cents / 100, 0) }}<span style="font-size:1rem;color:var(--muted);">/{{ $plan->billing_interval }}</span></div>
                <p>{{ $plan->max_pages }} pages · {{ $plan->max_funnels }} funnels · {{ number_format($plan->max_contacts) }} contacts</p>
            </article>
        @empty
            <article class="price"><h3>Starter</h3><div class="amount">$29</div><p>Seed plans after migrate.</p></article>
        @endforelse
    </div>
</main>
@endsection