@extends('layouts.marketing')
@section('title', 'About — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>About</h2>
    <p class="lead">Landing Funnel Builder is a modular SaaS platform by Imran Dev BD for conversion operations.</p>
    <div class="prose">
        <p>This product surface is fully wired into the application modules, Filament administration, API, and demo seed so you can evaluate real workflows—not placeholder screens.</p>
        <p><a class="cta" href="{{ url('/contact') }}">Talk to us</a></p>
    </div>
</main>
@endsection