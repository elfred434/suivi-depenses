<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('titre', 'Mon application')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --paper: #F6F3EC;
            --paper-soft: #FBF9F5;
            --ink: #181816;
            --ink-light: #5a5852;
            --rule: #D8D2C5;
            --accent: #1B4332;
            --accent-dark: #123023;
            --danger: #9B2226;
            --danger-dark: #7a1b1e;
            --warning: #8a6a1f;
            --success: #1B4332;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'IBM Plex Sans', -apple-system, sans-serif;
            background: var(--paper);
            color: var(--ink);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, h4 {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -0.01em;
        }
        h1 { font-size: 2rem; margin: 0 0 20px; }
        h2 { font-size: 1.4rem; margin: 24px 0 12px; }
        h3 { font-size: 1.15rem; margin: 0 0 8px; }
        .mono, .amount, .date, .total {
            font-family: 'IBM Plex Mono', 'Courier New', monospace;
            font-variant-numeric: tabular-nums;
        }
        .navbar {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 16px 32px;
            background: var(--paper-soft);
            border-bottom: 1px solid var(--rule);
        }
        .navbar a {
            color: var(--ink);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.15s;
        }
        .navbar a:hover { color: var(--accent); }
        .right { margin-left: auto; display: flex; align-items: center; gap: 16px; }
        .right span { color: var(--ink-light); font-size: 0.85rem; }
        main { padding: 40px 28px; max-width: 1120px; margin: 0 auto; }
        .card {
            background: var(--paper-soft);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: 28px;
            margin-bottom: 20px;
        }
        button, .btn {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            font-family: 'IBM Plex Sans', sans-serif;
            transition: background 0.15s;
        }
        button:hover, .btn:hover { background: var(--accent-dark); }
        button.danger { background: var(--danger); }
        button.danger:hover { background: var(--danger-dark); }
        button.warning { background: var(--warning); }
        button.warning:hover { background: #6b5218; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            background: var(--paper-soft);
            border: 1px solid var(--rule);
        }
        th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--rule); }
        th {
            background: var(--paper);
            color: var(--ink-light);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-family: 'IBM Plex Sans', sans-serif;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: var(--paper); }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--rule);
            border-radius: 4px;
            font-size: 0.95rem;
            font-family: 'IBM Plex Sans', sans-serif;
            background: var(--paper-soft);
            color: var(--ink);
            transition: border-color 0.15s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        label {
            font-weight: 600;
            color: var(--ink);
            font-size: 0.85rem;
            display: block;
            margin-bottom: 6px;
            font-family: 'IBM Plex Sans', sans-serif;
        }
        .error { color: var(--danger); font-size: 0.85rem; margin-top: 4px; }
        .success-msg {
            background: var(--paper-soft);
            color: var(--accent);
            padding: 14px 18px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid var(--rule);
            border-left: 4px solid var(--accent);
        }
        footer {
            text-align: center;
            padding: 32px;
            color: var(--ink-light);
            font-size: 0.8rem;
            border-top: 1px solid var(--rule);
            margin-top: 48px;
            font-family: 'IBM Plex Mono', monospace;
        }
        .empty-state { text-align: center; padding: 48px; color: var(--ink-light); font-style: italic; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .grid-card {
            background: var(--paper-soft);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: 20px;
            transition: border-color 0.15s;
        }
        .grid-card:hover { border-color: var(--accent); }
        .stat-bar {
            background: var(--paper-soft);
            color: var(--ink);
            padding: 16px 20px;
            border: 1px solid var(--rule);
            border-top: 1px solid var(--ink);
            border-bottom: 3px double var(--ink);
            margin-bottom: 24px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 1.05rem;
            font-weight: 500;
            text-align: right;
        }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 0.75rem; font-weight: 600; font-family: 'IBM Plex Mono', monospace; }
        .badge-green { background: #e8f0ea; color: var(--accent); }
        .badge-blue { background: #e8edf5; color: #1e3a5f; }
        .pagination { display: flex; justify-content: center; margin-top: 24px; gap: 6px; }
        .pagination a, .pagination span { padding: 6px 12px; border: 1px solid var(--rule); border-radius: 3px; text-decoration: none; color: var(--ink); font-family: 'IBM Plex Mono', monospace; font-size: 0.85rem; }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .form-group { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
        .divider { border: none; border-top: 1px solid var(--rule); margin: 24px 0; }
        .link-muted { color: var(--ink-light); text-decoration: none; font-size: 0.9rem; }
        .link-muted:hover { color: var(--accent); text-decoration: underline; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('categories.create') }}">Nouvelle catégorie</a>
        <a href="{{ route('depenses.create') }}">Nouvelle dépense</a>
        <a href="{{ route('depenses.index') }}">Dépenses</a>
        <a href="{{ route('categories.index') }}">Catégories</a>
        <div class="right">
            @auth
            <span>{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="post" style="display:inline;">
                @csrf
                <button type="submit" style="background:transparent;color:var(--ink);padding:6px 12px;border:1px solid var(--rule);font-size:0.85rem;">Déconnexion</button>
            </form>
            @endauth
            @guest
            <a href="{{ route('register') }}">Inscription</a>
            <a href="{{ route('login') }}">Connexion</a>
            @endguest
        </div>
    </nav>

    <main>
        @if (session('succes'))
            <p class="success-msg" role="status">{{ session('succes') }}</p>
        @endif
        @yield('contenu')
    </main>

    <footer>
        <p>Suivi de dépenses — Elfred</p>
    </footer>
</body>
</html>