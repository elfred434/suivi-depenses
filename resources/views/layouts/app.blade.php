<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('titre', 'Mon application')</title>
    <style>
        .navbar{
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .right{
            margin-left: auto
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('categories.create') }}">Créé votre catégorie</a>
        <a href="{{ route('depenses.create') }}">Créé la dépenses</a>
        <a href="{{ route('depenses.index') }}">Voir les dépenses</a>
        <a href="{{ route('categories.index') }}">Voir les catégories</a>
        <div class="right">
            @auth
            <span>Connecté : {{auth()->user()->name}}</span>

            <form action="{{ route('logout') }}" method="post">
                @csrf

                <button type="submit">Déconnectez vous</button>
            </form>
                
            @endauth

            @guest
            <a href="{{ route('register') }}">Inscription</a>
            <a href="{{ route('login') }}">Connexion</a>
            @endguest
        </div>
    </nav>

    <hr>

    <main>
        @if (session('succes'))
            <p role="status">{{ session('succes')}}</p>
        @endif
        @yield('contenu')
    </main>

    <footer>
        <p>Mon application Laravel - Elfred</p>
    </footer>
</body>
</html>