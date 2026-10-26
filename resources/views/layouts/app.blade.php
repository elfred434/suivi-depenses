<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('titre', 'Mon application')</title>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
    <style>
        .navbar{
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .navbar a{ color: #fff; text-decoration: none; font-weight: 600; transition: transform .2s ease, opacity .2s ease; }
        .navbar a:hover{ transform: translateY(-2px); opacity: .85; }
        .right{ margin-left: auto; display: flex; align-items: center; gap: 12px; }
        main{ padding: 24px; max-width: 1100px; margin: 0 auto; }
        h1{ font-size: 2rem; margin-bottom: 16px; color: #333; }
        .card{ background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 6px 18px rgba(0,0,0,.08); margin-bottom: 16px; transition: transform .25s ease, box-shadow .25s ease; }
        .card:hover{ transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,.12); }
        button, .btn{ background: #667eea; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background .2s ease, transform .2s ease; }
        button:hover, .btn:hover{ background: #5a67d8; transform: translateY(-2px); }
        table{ width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td{ padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th{ background: #f7f7f7; }
        a{ color: #667eea; }
        footer{ text-align: center; padding: 24px; color: #888; }
        .fade-in{ animation: fadeIn .6s ease both; }
        @keyframes fadeIn{ from{ opacity:0; transform: translateY(8px);} to{ opacity:1; transform:none;} }
    </style>
</head>
<body>
    <nav class="navbar" data-aos="fade-down">
        <a href="{{ route('categories.create') }}">Créer votre catégorie</a>
        <a href="{{ route('depenses.create') }}">Créer la dépense</a>
        <a href="{{ route('depenses.index') }}">Voir les dépenses</a>
        <a href="{{ route('categories.index') }}">Voir les catégories</a>
        <div class="right">
            @auth
            <span>Connecté : {{auth()->user()->name}}</span>

            <form action="{{ route('logout') }}" method="post">
                @csrf

                <button type="submit">Déconnexion</button>
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
            <p role="status" data-aos="fade-in">{{ session('succes')}}</p>
        @endif
        <div data-aos="fade-up">
            @yield('contenu')
        </div>
    </main>

    <footer>
        <p>Mon application Laravel - Elfred</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
    <script>
        AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });
        document.addEventListener('DOMContentLoaded', function () {
            if (window.gsap) {
                gsap.from('h1', { y: 24, opacity: 0, duration: 0.8, ease: 'power3.out' });
                gsap.from('.card', { y: 30, opacity: 0, duration: 0.9, stagger: 0.08, ease: 'power3.out' });
            }
            if (window.anime) {
                anime({ targets: 'button', scale: [0.96, 1], duration: 400, easing: 'easeOutElastic(1, .6)' });
            }
        });
    </script>
</body>
</html>