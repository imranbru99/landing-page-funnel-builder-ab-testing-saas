<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Landing Funnel Builder' }}</title>
    <style>
        :root {
            --ink: #0b1f1c;
            --teal: #0f766e;
            --teal-dark: #115e59;
            --sand: #f3f7f5;
            --mist: #d9ebe6;
            --accent: #c45c26;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", "Iowan Old Style", Georgia, serif; color: var(--ink); background: linear-gradient(180deg, #eef7f4 0%, #f8faf9 40%, #fff 100%); }
        a { color: var(--teal-dark); text-decoration: none; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 1.25rem; }
        .btn { display: inline-block; background: var(--teal); color: #fff; padding: .7rem 1.1rem; border-radius: .55rem; border: 0; cursor: pointer; font-weight: 600; }
        .btn.secondary { background: transparent; color: var(--teal-dark); border: 1px solid var(--mist); }
        .card { background: rgba(255,255,255,.9); border: 1px solid var(--mist); border-radius: .9rem; padding: 1rem; }
        input, textarea, select { width: 100%; padding: .55rem .7rem; border: 1px solid #c9ddd7; border-radius: .45rem; font: inherit; }
        label { display:block; font-size:.9rem; margin-bottom:.25rem; }
        .grid { display:grid; gap:1rem; }
        @media (min-width: 900px) { .grid-2 { grid-template-columns: 1.1fr .9fr; } }
    </style>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
