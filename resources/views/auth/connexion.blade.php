@extends('layouts.app')

@section('titre', 'mon formulaire de connexion')

@section('contenu')
    <form action="{{ route('login.store') }}" method="post">
            @csrf
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
            value="{{ old('email') }}" autocomplete="email">

            @error('email')
            <p>{{ $message }}</p>
        @enderror
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password"
            autocomplete="current-password">
            @error('password')
            <p>{{ $message }}</p>
        @enderror
        </div>
        <button type="submit">Connectez-vous</button>
        <p>Si vous n'avez pas de compte </p>
        <a href="{{ route('register') }}"> inscivez-vous</a>
    </form>
@endsection