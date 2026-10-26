@extends('layouts.app')

@section('titre', 'mon formulaire d\'inscription');

@section('contenu')
    <form action="{{ route('register.store') }}" method="post">
        @csrf
        
        <div>
            <label for="name">Nom</label>
            <input type="text" name="name" id="name"
            value="{{ old('name') }}" autocomplete="name">
            @error('name')
            <p>{{ $message }}</p>
        @enderror
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
            value="{{ old('email') }}" autocomplete="email">
            @error('email')
            <p>{{ $message }}</p>
        @enderror
        </div>

        <div>
            <label for="password"> Mot de passe</label>
            <input type="password" name="password" id="password" autocomplete="new-password">
            @error('password')
            <p>{{ $message }}</p>
        @enderror
        </div>

        <div>
            <label for="password_confirmation">Confirmez le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
            autocomplete="new-password">
            
        </div>

        <button type="submit">inscrivez-vous</button>
    </form>
@endsection