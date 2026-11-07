<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ data_get($page->seo, 'title', $page->title) }}</title>
    <meta name="description" content="{{ data_get($page->seo, 'description', '') }}">
    <style>
        body { margin:0; font-family: Georgia, "Times New Roman", serif; color:#10241f; background:#f6faf8; }
        .section { padding: 3.5rem 1.25rem; }
        .inner { max-width: 920px; margin: 0 auto; }
        .hero { background: linear-gradient(135deg, #0f766e, #134e4a); color:#fff; }
        .hero h1 { font-size: clamp(2rem, 5vw, 3.4rem); margin: 0 0 .75rem; }
        .btn { display:inline-block; background:#c45c26; color:#fff; padding:.8rem 1.2rem; border-radius:.5rem; text-decoration:none; font-weight:700; border:0; cursor:pointer; }
        .grid { display:grid; gap:1rem; }
        @media(min-width:800px){ .grid.cols-3{ grid-template-columns:repeat(3,1fr);} }
        .card { background:#fff; border:1px solid #d7e8e2; border-radius:.8rem; padding:1rem; }
        form label { display:block; margin:.5rem 0 .2rem; }
        form input, form textarea { width:100%; padding:.6rem; border:1px solid #c9ddd7; border-radius:.4rem; }
    </style>
</head>
<body>
@foreach(($sections ?? $page->sections ?? []) as $section)
    @include('published.partials.section', ['section' => $section, 'variant' => $variant ?? null])
@endforeach

@if(!empty($experiment))
<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[data-lf-form]');
    forms.forEach((form) => {
        form.addEventListener('submit', () => {
            navigator.sendBeacon?.('/api/v1/public/experiments/{{ $experiment->id }}/events', new Blob([JSON.stringify({
                event_name: 'conversion',
                visitor_key: '{{ $visitorKey ?? '' }}',
                variant_id: {{ $variant->id ?? 'null' }}
            })], { type: 'application/json' }));
        });
    });
});
</script>
@endif
</body>
</html>
