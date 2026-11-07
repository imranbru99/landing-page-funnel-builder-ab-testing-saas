<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Landing Funnel Builder')</title>
    <meta name="description" content="@yield('description', 'Build landing pages, funnels, and A/B tests that convert.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560;9..144,700&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #071512;
            --panel: #0d2420;
            --teal: #1aa896;
            --teal-2: #0e7c6f;
            --cream: #e8f4ef;
            --muted: #9fb7b0;
            --line: rgba(232,244,239,.12);
            --accent: #ef8a4a;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: "Sora", sans-serif;
            color: var(--cream);
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(26,168,150,.28), transparent 55%),
                radial-gradient(900px 500px at 90% 0%, rgba(239,138,74,.18), transparent 50%),
                linear-gradient(160deg, #04110e 0%, #0a1d19 45%, #102823 100%);
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .shell { width: min(1120px, calc(100% - 2rem)); margin-inline: auto; }
        .nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.1rem 0; gap: 1rem; position: sticky; top: 0; z-index: 20;
            backdrop-filter: blur(10px); background: rgba(4,17,14,.55); border-bottom: 1px solid var(--line);
        }
        .brand { font-family: "Fraunces", serif; font-size: 1.35rem; letter-spacing: -.02em; }
        .brand span { color: var(--teal); }
        .nav-links { display: flex; gap: 1rem; flex-wrap: wrap; font-size: .92rem; color: var(--muted); }
        .nav-links a:hover { color: var(--cream); }
        .cta {
            display: inline-flex; align-items: center; gap: .4rem;
            background: linear-gradient(135deg, var(--teal), var(--teal-2));
            color: #04110e; font-weight: 700; padding: .7rem 1.05rem; border-radius: 999px;
            transition: transform .2s ease, box-shadow .2s ease;
            box-shadow: 0 10px 30px rgba(26,168,150,.25);
        }
        .cta:hover { transform: translateY(-2px); }
        .cta.ghost { background: transparent; color: var(--cream); border: 1px solid var(--line); box-shadow: none; }
        .hero {
            display: grid; gap: 1.5rem; padding: 4.5rem 0 3.5rem; align-items: end;
            min-height: calc(100vh - 74px);
        }
        @media (min-width: 900px) {
            .hero { grid-template-columns: 1.05fr .95fr; }
            .nav-links { gap: 1.25rem; }
        }
        .hero h1 {
            font-family: "Fraunces", serif; font-size: clamp(2.4rem, 6vw, 4.4rem);
            line-height: 1.02; margin: 0 0 1rem; letter-spacing: -.03em;
            animation: rise .7s ease both;
        }
        .hero p { color: var(--muted); font-size: 1.08rem; max-width: 36rem; margin: 0 0 1.5rem; animation: rise .8s ease both; }
        .hero-actions { display: flex; gap: .75rem; flex-wrap: wrap; animation: rise .9s ease both; }
        .visual {
            border: 1px solid var(--line); border-radius: 1.4rem; overflow: hidden; min-height: 360px;
            background:
                linear-gradient(145deg, rgba(26,168,150,.2), transparent 40%),
                linear-gradient(20deg, #12352e, #0b201c 60%, #173f37);
            position: relative; animation: floatIn .9s ease both;
        }
        .visual::after {
            content: ""; position: absolute; inset: 18% 12% auto; height: 52%;
            border-radius: 1rem; border: 1px solid rgba(232,244,239,.18);
            background: linear-gradient(180deg, rgba(232,244,239,.08), rgba(232,244,239,.02));
            box-shadow: 0 20px 50px rgba(0,0,0,.35);
        }
        .section { padding: 4rem 0; border-top: 1px solid var(--line); }
        .section h2 { font-family: "Fraunces", serif; font-size: clamp(1.8rem, 3vw, 2.6rem); margin: 0 0 .75rem; }
        .section p.lead { color: var(--muted); max-width: 40rem; margin: 0 0 1.5rem; }
        .grid { display: grid; gap: 1rem; }
        @media (min-width: 800px) { .grid.cols-3 { grid-template-columns: repeat(3, 1fr); } .grid.cols-2 { grid-template-columns: repeat(2, 1fr); } }
        .tile {
            background: rgba(13,36,32,.72); border: 1px solid var(--line); border-radius: 1rem; padding: 1.2rem;
            transition: transform .2s ease, border-color .2s ease;
        }
        .tile:hover { transform: translateY(-3px); border-color: rgba(26,168,150,.45); }
        .tile h3 { margin: 0 0 .45rem; font-size: 1.05rem; }
        .tile p { margin: 0; color: var(--muted); font-size: .95rem; }
        .price {
            background: rgba(13,36,32,.8); border: 1px solid var(--line); border-radius: 1.2rem; padding: 1.4rem;
        }
        .price .amount { font-family: "Fraunces", serif; font-size: 2.4rem; margin: .4rem 0 1rem; }
        footer { border-top: 1px solid var(--line); padding: 2rem 0 3rem; color: var(--muted); font-size: .9rem; }
        .footer-grid { display: grid; gap: 1rem; }
        @media (min-width: 800px) { .footer-grid { grid-template-columns: 2fr 1fr 1fr 1fr; } }
        @keyframes rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        @keyframes floatIn { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: none; } }
        .prose { color: var(--muted); line-height: 1.7; max-width: 48rem; }
        .prose h2, .prose h3 { color: var(--cream); }
    </style>
</head>
<body>
    <header class="shell">
        <nav class="nav">
            <a class="brand" href="{{ url('/') }}">Landing<span>Funnel</span></a>
            <div class="nav-links">
                <a href="{{ url('/features') }}">Features</a>
                <a href="{{ url('/builder') }}">Builder</a>
                <a href="{{ url('/funnels') }}">Funnels</a>
                <a href="{{ url('/ab-testing') }}">A/B</a>
                <a href="{{ url('/pricing') }}">Pricing</a>
                <a href="{{ url('/templates') }}">Templates</a>
                <a href="{{ url('/blog') }}">Blog</a>
            </div>
            <div style="display:flex;gap:.5rem;">
                <a class="cta ghost" href="{{ url('/app/login') }}">Login</a>
                <a class="cta" href="{{ url('/contact') }}">Book demo</a>
            </div>
        </nav>
    </header>

    @yield('content')

    <footer>
        <div class="shell footer-grid">
            <div>
                <div class="brand">Landing<span>Funnel</span></div>
                <p>Modular multi-tenant SaaS for pages, funnels, experiments, and CRM.</p>
            </div>
            <div>
                <strong>Product</strong><br>
                <a href="{{ url('/analytics') }}">Analytics</a><br>
                <a href="{{ url('/ai') }}">AI Assistant</a><br>
                <a href="{{ url('/use-cases') }}">Use cases</a>
            </div>
            <div>
                <strong>Company</strong><br>
                <a href="{{ url('/about') }}">About</a><br>
                <a href="{{ url('/contact') }}">Contact</a><br>
                <a href="{{ url('/blog') }}">Blog</a>
            </div>
            <div>
                <strong>Legal</strong><br>
                <a href="{{ url('/legal/privacy') }}">Privacy</a><br>
                <a href="{{ url('/legal/terms') }}">Terms</a>
            </div>
        </div>
    </footer>
</body>
</html>
