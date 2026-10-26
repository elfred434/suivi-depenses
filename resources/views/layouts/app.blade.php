<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('titre', 'Mon application')</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --text: #1f2937;
            --text-light: #6b7280;
            --bg: #f9fafb;
            --card: #ffffff;
            --border: #e5e7eb;
            --danger: #dc2626;
            --success: #16a34a;
            --warning: #d97706;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        .navbar {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 14px 28px;
            background: var(--card);
            border-bottom: 1px solid var(--border);
        }
        .navbar a {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.15s;
        }
        .navbar a:hover { color: var(--primary); }
        .right { margin-left: auto; display: flex; align-items: center; gap: 14px; }
        .right span { color: var(--text-light); font-size: 0.9rem; }
        main { padding: 32px 24px; max-width: 1100px; margin: 0 auto; }
        h1 { font-size: 1.75rem; margin: 0 0 20px; color: var(--text); font-weight: 700; }
        h2 { font-size: 1.25rem; margin: 24px 0 12px; color: var(--text); }
        .card { background: var(--card); border-radius: 10px; padding: 24px; border: 1px solid var(--border); margin-bottom: 16px; }
        button, .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.15s;
        }
        button:hover, .btn:hover { background: var(--primary-dark); }
        button.danger { background: var(--danger); }
        button.danger:hover { background: #b91c1c; }
        button.warning { background: var(--warning); }
        button.warning:hover { background: #b45309; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; background: var(--card); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: #f3f4f6; color: var(--text-light); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f9fafb; }
        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }
        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        label { font-weight: 600; color: var(--text); font-size: 0.9rem; display: block; margin-bottom: 6px; }
        .error { color: var(--danger); font-size: 0.85rem; margin-top: 4px; }
        .success-msg { background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #bbf7d0; }
        footer { text-align: center; padding: 24px; color: var(--text-light); font-size: 0.85rem; border-top: 1px solid var(--border); margin-top: 40px; }
        .empty-state { text-align: center; padding: 48px; color: var(--text-light); }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .grid-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 20px; transition: border-color 0.15s; }
        .grid-card:hover { border-color: var(--primary); }
        .stat-bar { background: var(--primary); color: #fff; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .pagination { display: flex; justify-content: center; margin-top: 24px; gap: 8px; }
        .pagination a, .pagination span { padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; text-decoration: none; color: var(--text); }
        .pagination .active { background: var(--primary); color: #fff; border-color: var(--primary); }
        .form-group { margin-bottom: 16px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('categories.create') }}">Créer une catégorie</a>
        <a href="{{ route('depenses.create') }}">Créer une dépense</a>
        <a href="{{ route('depenses.index') }}">Voir les dépenses</a>
        <a href="{{ route('categories.index') }}">Voir les catégories</a>
        <div class="right">
            @auth
            <span>{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="post" style="display:inline;">
                @csrf
                <button type="submit" style="background:transparent;color:var(--text);padding:6px 12px;border:1px solid var(--border);">Déconnexion</button>
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
        <p>Mon application Laravel - Elfred</p>
    </footer>
</body>
</html>