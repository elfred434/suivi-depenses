@extends('layouts.app')

@section('titre', 'Mes catégories')

@section('contenu')
    <h1>Créé une catégorie</h1>
    <form action="{{ route('categories.store') }}" method="post">
        @csrf

        <div>
            <label for="nom">Nom (obligatoire)</label>
            <input type="text" name="nom" id="nom" minlength="2" maxlength="50" value="{{ old('nom') }}"
            required>

            @error('nom')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description">Description (facultatif)</label>
            <textarea name="description" id="description" maxlength="1000" cols="30" rows="10">
                {{ old('description') }}
            </textarea>
            @error('description')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">créé </button>
    </form>
@endsection