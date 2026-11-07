@extends('layouts.marketing')
@section('title', ucfirst($page).' — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>{{ ucfirst($page) }}</h2>
    <div class="prose">
        <p>This {{ $page }} policy covers the Landing Funnel Builder SaaS platform, including multi-tenant data isolation, module activation, analytics events, and API access.</p>
        <p>Organizations own their workspace data. Platform administrators may access demo or support contexts under agreed policies.</p>
    </div>
</main>
@endsection