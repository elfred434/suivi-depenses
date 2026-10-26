@extends('layouts.app')

@section('titre', 'Modifier la catégorie {{ $cat->nom }}')

@section('contenu')

    <h1>Modifier la tâche {{ $cat->nom }}</h1>

    <form action="{{ route('categories.update', ['cat' => $cat->id]) }}" method="post">
        @csrf
        @method('PATCH')
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" minlength="2" maxlength="50"
            value="{{ old('nom', $cat->nom) }}">

            @error('nom')
                <p>{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" maxlength="1000" cols="30" rows="10">
                {{ old('description', $cat->description) }}
            </textarea>

            @error('description')
                <p>{{ $message }}</p>
            @enderror
        </div>
        <button type="submit">Enregistrez les modifications</button>

    </form>

    <a href="{{ route('categorie.show', ['cat' => $cat->id]) }}">Revenir au détails </a>
    
@endsection