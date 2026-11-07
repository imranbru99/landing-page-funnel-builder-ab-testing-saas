@extends('layouts.marketing')
@section('title', 'Contact — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>Contact</h2>
    <p class="lead">Tell us about your conversion stack. Demo org available after seeding.</p>
    @if(session('success'))
        <div class="tile" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ url('/contact') }}" class="tile" style="max-width:560px;">
        @csrf
        <label>Name</label>
        <input name="name" required style="width:100%;margin:.3rem 0 0.8rem;padding:.6rem;border-radius:.5rem;border:1px solid var(--line);background:#081a17;color:var(--cream);">
        <label>Email</label>
        <input type="email" name="email" required style="width:100%;margin:.3rem 0 0.8rem;padding:.6rem;border-radius:.5rem;border:1px solid var(--line);background:#081a17;color:var(--cream);">
        <label>Message</label>
        <textarea name="message" rows="5" required style="width:100%;margin:.3rem 0 0.8rem;padding:.6rem;border-radius:.5rem;border:1px solid var(--line);background:#081a17;color:var(--cream);"></textarea>
        <button class="cta" type="submit">Send message</button>
    </form>
</main>
@endsection