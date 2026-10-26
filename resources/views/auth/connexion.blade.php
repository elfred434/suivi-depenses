@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
    <style>
        .connexion-wrapper {
            max-width: 420px;
            margin: 60px auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .connexion-wrapper form { display: flex; flex-direction: column; gap: 18px; }
        .connexion-wrapper label { font-weight: 600; color: #4a5568; font-size: 0.9rem; }
        .connexion-wrapper input {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .connexion-wrapper input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        .connexion-wrapper button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .connexion-wrapper button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.35);
        }
        .connexion-wrapper .error { color: #e53e3e; font-size: 0.85rem; margin-top: -10px; }
        .connexion-wrapper .link { text-align: center; margin-top: 16px; color: #718096; }
        .connexion-wrapper .link a { color: #667eea; font-weight: 600; text-decoration: none; }
    </style>

    <div class="connexion-wrapper">
        <h1>Connexion</h1>
        <form action="{{ route('login.store') }}" method="post">
            @csrf
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                value="{{ old('email') }}" autocomplete="email">
                @error('email')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password"
                autocomplete="current-password">
                @error('password')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Se connecter</button>
            <p class="link">Si vous n'avez pas de compte, <a href="{{ route('register') }}">inscrivez-vous</a></p>
        </form>
    </div>
@endsection