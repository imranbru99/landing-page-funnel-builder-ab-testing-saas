@extends('layouts.marketing')
@section('title', $post->title.' — Blog')
@section('content')
<main class="shell section">
    <h2>{{ $post->title }}</h2>
    <div class="prose">{!! nl2br(e($post->body)) !!}</div>
</main>
@endsection