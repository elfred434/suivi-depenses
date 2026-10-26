@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <div class="card" style="max-width:480px;margin:40px auto;">
        <h1>Inscription</h1>
        <form action="{{ route('register.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name">
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" autocomplete="new-password">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmez le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
            </div>
            <button type="submit">S'inscrire</button>
            <p style="text-align:center;margin-top:16px;color:var(--text-light);">
                Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
            </p>
        </form>
    </div>
@endsection