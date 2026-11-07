@extends('layouts.marketing')
@section('title', 'Blog — Landing Funnel Builder')
@section('content')
<main class="shell section">
    <h2>Blog</h2>
    <div class="grid cols-2">
        @foreach($posts as $post)
            <article class="tile">
                <h3><a href="{{ url('/blog/'.$post->slug) }}">{{ $post->title }}</a></h3>
                <p>{{ $post->excerpt }}</p>
            </article>
        @endforeach
    </div>
</main>
@endsection