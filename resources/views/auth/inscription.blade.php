@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <style>
        .inscription-wrapper {
            max-width: 480px;
            margin: 50px auto;
            padding: 44px;
            background: #fafafa;
            border-radius: 20px;
            border: 1px solid #e8e8e8;
            animation: fadeInScale 0.7s ease-out;
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .inscription-wrapper h1 { text-align: center; margin-bottom: 28px; color: #2d3748; }
        .inscription-wrapper form { display: flex; flex-direction: column; gap: 20px; }
        .inscription-wrapper label { font-weight: 600; color: #4a5568; font-size: 0.9rem; display: block; margin-bottom: 6px; }
        .inscription-wrapper input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }
        .inscription-wrapper input:focus {
            outline: none;
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.15);
        }
        .inscription-wrapper button {
            background: linear-gradient(135deg, #764ba2, #667eea);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .inscription-wrapper button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(118, 75, 162, 0.35);
        }
        .inscription-wrapper .error { color: #e53e3e; font-size: 0.85rem; margin-top: 4px; }
        .inscription-wrapper .link { text-align: center; margin-top: 16px; color: #718096; }
        .inscription-wrapper .link a { color: #764ba2; font-weight: 600; text-decoration: none; }
    </style>

    <div class="inscription-wrapper">
        <h1>Inscription</h1>
        <form action="{{ route('register.store') }}" method="post">
            @csrf
            
            <div>
                <label for="name">Nom</label>
                <input type="text" name="name" id="name"
                value="{{ old('name') }}" autocomplete="name">
                @error('name')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>

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
                <input type="password" name="password" id="password" autocomplete="new-password">
                @error('password')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation">Confirmez le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                autocomplete="new-password">
            </div>

            <button type="submit">S'inscrire</button>
            <p class="link">Déjà un compte ? <a href="{{ route('login') }}">Connectez-vous</a></p>
        </form>
    </div>
@endsection