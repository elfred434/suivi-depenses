@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
    <div class="card" style="max-width:420px;margin:40px auto;">
        <h1>Connexion</h1>
        <form action="{{ route('login.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" autocomplete="current-password">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Se connecter</button>
            <p style="text-align:center;margin-top:16px;color:var(--text-light);">
                Pas de compte ? <a href="{{ route('register') }}">S'inscrire</a>
            </p>
        </form>
    </div>
@endsection