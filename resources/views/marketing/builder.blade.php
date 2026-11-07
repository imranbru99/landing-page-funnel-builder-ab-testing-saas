@extends('layouts.marketing')
@section('title', 'Page Builder — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>Page Builder</h2>
    <p class="lead">Structured visual editor with reorderable sections, draft saves, and real publish URLs.</p>
    <div class="prose">
        <p>This product surface is fully wired into the application modules, Filament administration, API, and demo seed so you can evaluate real workflows—not placeholder screens.</p>
        <p><a class="cta" href="{{ url('/contact') }}">Talk to us</a></p>
    </div>
</main>
@endsection